// DOM/fetch simulados: nenhum navegador, endpoint ou banco real e acessado.
const fs = require('fs');
const vm = require('vm');
const assert = require('assert/strict');
const fixture = projeto => ({
    id_doacao: 11, id_doador: 3, nome: 'Doador', nome_doador: 'Doador',
    tipo: 'Financeira', id_projeto: projeto, projeto: projeto ? 'Projeto teste' : null,
    projeto_nome: projeto ? 'Projeto teste' : null, descricao: 'Teste',
    forma_pagamento: 'PIX', data_doacao: '2026-11-02', valor: '75.00', status_doacao: 1
});
function ambiente() {
    const nodes = {}, rows = [], handlers = {}, alertas = [];
    const novo = () => ({
        value: '', textContent: '', innerHTML: '', style: {}, children: [],
        classList: { add() {}, remove() {}, toggle() {} },
        addEventListener(evento, fn) { handlers[evento] = fn; },
        appendChild(node) { this.children.push(node); },
        querySelector() { return { addEventListener() {} }; }
    });
    const get = id => nodes[id] ||= novo();
    get('bodyTable').appendChild = row => rows.push(row);
    return {
        nodes, rows, handlers, alertas,
        ctx: {
            document: { getElementById: get, createElement: novo, addEventListener() {}, querySelector: novo },
            window: { addEventListener() {}, location: { href: '', search: '' } },
            console: { log() {}, error() {} }, alert: x => alertas.push(x),
            FormData: class { constructor(form) { this.form = form; } }, URLSearchParams,
            fetch: async () => { throw Error('Defina a resposta simulada'); }
        }
    };
}
function carregar(ambiente, file) {
    vm.createContext(ambiente.ctx);
    vm.runInContext(fs.readFileSync(file, 'utf8'), ambiente.ctx);
}
(async () => {
    for (const projeto of [7, null]) {
        const a = ambiente();
        a.ctx.fetch = async () => ({ json: async () => ({ status: 200, data: fixture(projeto) }) });
        carregar(a, 'app/view/JS/editar-doacao.js');
        await a.ctx.carregarDadosdoacao(11);
        assert.equal(a.nodes.projeto.value, projeto ?? '');
        assert.equal(a.nodes.valor.value, '75.00');
    }
    for (const status of [200, 400]) {
        const a = ambiente();
        a.ctx.fetch = async () => ({ ok: true, json: async () => ({ status, msg: 'Resposta simulada' }) });
        carregar(a, 'app/view/JS/editar-doacao.js');
        await a.handlers.submit({ preventDefault() {} });
        assert.equal(a.ctx.window.location.href, status === 200 ? './listagem-doacao.php' : '');
    }
    const a = ambiente();
    a.ctx.fetch = async () => ({ json: async () => [fixture(null), fixture(7)] });
    carregar(a, 'app/view/JS/listagem-doacao.js');
    await a.ctx.listagemDoacao();
    assert.equal(a.rows.length, 2);
    assert(a.rows[0].innerHTML.includes('<td>Sem projeto</td>'));
    assert(a.rows[1].innerHTML.includes('<td>Projeto teste</td>'));

    // Os formularios preservam nome/ID; somente projeto deixa de ser obrigatorio.
    for (const file of ['cadastro-doacao.php', 'edicao-doacao.php']) {
        const html = fs.readFileSync('app/view/pages/' + file, 'utf8');
        assert(html.includes('Projeto (opcional)'));
        const select = html.match(/<select id="projeto" name="id_projeto"([^>]*)>/);
        assert(select && !select[1].includes('required'));
        assert(html.includes('<option value="" selected>Sem projeto</option>'));
    }
    const cadastro = fs.readFileSync('app/view/pages/cadastro-doacao.php', 'utf8');
    for (const id of ['tipo', 'forma_pagamento', 'data_doacao']) {
        assert(new RegExp('<(?:select|input)[^>]*id="' + id + '"[^>]*required').test(cadastro));
    }
    console.log('OK: edicao com/sem projeto por ID, retorno de erro sem redirecionamento, listagem Sem projeto, opcao vazia selecionavel e obrigatoriedades restantes preservadas. Sem rede/banco.');
})().catch(erro => { console.error(erro); process.exitCode = 1; });
