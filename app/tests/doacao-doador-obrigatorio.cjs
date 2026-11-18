// Autocomplete/submit reais com DOM e fetch simulados. Sem POST ou banco real.
const fs = require('fs');
const vm = require('vm');
const assert = require('assert/strict');
function elemento() {
    return {
        value: '', style: {}, handlers: {}, children: [],
        set innerHTML(texto) { this.children = []; },
        addEventListener(evento, fn) { this.handlers[evento] = fn; },
        appendChild(node) { this.children.push(node); },
        focus() {}, scrollIntoView() {}
    };
}
async function ambiente() {
    const nodes = {}, requests = [], alertas = [], dom = [];
    const get = id => nodes[id] ||= elemento();
    const ctx = {
        document: { createElement: elemento, head: { appendChild() {} }, getElementById: get,
            addEventListener(evento, fn) { if (evento === 'DOMContentLoaded') dom.push(fn); } },
        console: { log() {}, error() {} }, alert: texto => alertas.push(texto),
        FormData: class { constructor() { this.dados = { id_doador: get('id_doador').value, id_projeto: get('projeto').value }; } },
        fetch: async (url, options) => {
            requests.push({ url, options });
            return { text: async () => JSON.stringify({ doadores: [{ id_doador: 3, nome: 'Doador teste' }] }),
                json: async () => ({ success: true, message: 'Sucesso simulado' }) };
        }
    };
    vm.createContext(ctx);
    vm.runInContext(fs.readFileSync('app/view/JS/cadastro-doacao.js', 'utf8'), ctx);
    for (const fn of dom) await fn();
    const submit = async () => { await get('formCadastro').handlers.submit.call(get('formCadastro'), { preventDefault() {} }); };
    return { get, requests, alertas, submit };
}
(async () => {
    for (const id of ['', '0', '-1', 'abc']) {
        const a = await ambiente();
        a.get('nome_doador').value = 'Doador teste';
        a.get('id_doador').value = id;
        await a.submit();
        assert.equal(a.requests.length, 1); // Somente GET de opcoes, nenhum POST.
        assert.equal(a.alertas[0], 'Selecione um doador da lista antes de salvar a doação.');
    }
    for (const projeto of ['', '7']) {
        const a = await ambiente();
        a.get('nome_doador').handlers.focus();
        a.get('sugestoes_doadores').children[0].handlers.mousedown({ preventDefault() {} });
        a.get('projeto').value = projeto;
        await a.submit();
        assert.equal(a.requests[1].options.method, 'POST');
        assert.equal(a.requests[1].options.body.dados.id_doador, 3);
        assert.equal(a.requests[1].options.body.dados.id_projeto, projeto);
        // Redigitar o nome apaga o ID e impede envio ate uma nova selecao.
        a.get('nome_doador').value = 'Outro texto';
        a.get('nome_doador').handlers.input();
        await a.submit();
        assert.equal(a.requests.length, 2);
    }
    const a = await ambiente();
    a.get('nome_doador').value = 'Doador teste';
    a.get('nome_doador').handlers.blur();
    assert.equal(a.get('id_doador').value, 3);
    await a.submit();
    assert.equal(a.requests.length, 2);
    console.log('OK: ID ausente/zero/invalido bloqueado; selecao pelo autocomplete com/sem projeto, invalidacao ao redigitar e nome exato no blur preservados.');
})().catch(erro => { console.error(erro); process.exitCode = 1; });
