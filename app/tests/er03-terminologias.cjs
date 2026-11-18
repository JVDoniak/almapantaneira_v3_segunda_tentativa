// Testes de comportamento: textos podem mudar; payloads e fluxos devem permanecer.
// Opcional: ALMA_ER03_BASELINE aponta para snapshot anterior desta etapa.
const fs = require('fs');
const vm = require('vm');
const assert = require('assert/strict');
const baseline = process.env.ALMA_ER03_BASELINE
    ? JSON.parse(fs.readFileSync(process.env.ALMA_ER03_BASELINE, 'utf8')).baseline : null;
const tick = () => new Promise(resolve => setImmediate(resolve));
function environment(responseSuccess = true, confirms = true) {
    const nodes = {}, callbacks = [], requests = [], alerts = [], rows = [];
    const element = () => ({
        value: '', checked: true, style: {}, handlers: {}, children: [], textContent: '',
        get innerHTML() { return this.html || this.textContent; },
        set innerHTML(text) { this.html = text; this.children = []; },
        classList: { add() {}, remove() {}, toggle() {} },
        addEventListener(name, callback) { this.handlers[name] = callback; },
        appendChild(child) { this.children.push(child); },
        querySelector() { return this.checkbox ||= element(); },
        setAttribute() {}, focus() {}
    });
    const get = id => nodes[id] ||= element();
    get('bodyTable').appendChild = row => rows.push(row);
    const pf = [element()], pj = [element()];
    const item = { id_despesa: 17, id_doacao: 18, id_doador: 3, id_fornecedor: 9, id_projeto: 7,
        nome: 'Teste', nome_despesa: 'Teste', nome_doador: 'Teste', tipo: 'Pessoa Física',
        cpf_cnpj: '00000000000', telefone: '', email: 'teste@example.invalid',
        status_despesa: 1, status_doacao: 1, status_doador: 1, status_fornecedor: 1, status_projeto: 1,
        tipo_projeto: 'Saúde', nome_tipo: 'Fixa', nome_categoria: 'Medicamentos',
        data_inicio: '2026-11-02', data_vencimento: '2026-11-02', data_doacao: '2026-11-02', valor: '10.00' };
    const context = {
        document: { getElementById: get, createElement: element, querySelector: () => get('filter'),
            querySelectorAll: selector => selector === '.campo-pf' ? pf : pj,
            addEventListener(name, callback) { if (name === 'DOMContentLoaded') callbacks.push(callback); } },
        window: { location: { href: '', search: '' }, addEventListener(name, callback) {
            if (name === 'DOMContentLoaded') callbacks.push(callback);
        } }, URLSearchParams,
        console: { log() {}, error() {} }, alert: text => alerts.push(text), confirm: () => confirms,
        FormData: class {},
        fetch: async (url, options) => {
            requests.push({ url, method: options?.method || 'GET', body: options?.body });
            return { ok: true, json: async () => options?.method === 'POST'
                ? { sucesso: responseSuccess, mensagem: 'Falha simulada' } : [item], text: async () => 'JSON inválido' };
        }
    };
    vm.createContext(context);
    return { context, get, callbacks, requests, alerts, rows, pf, pj };
}
async function statusScenario(source, module, mode, checked) {
    const env = environment(mode !== 'failure', mode !== 'cancel');
    vm.runInContext(source, env.context);
    if (module === 'despesa') await env.context.listagemDespesa();
    else for (const fn of env.callbacks) await fn();
    await tick(); await tick();
    assert.equal(env.rows.length, 1, module + ' deve renderizar fixture');
    const checkbox = env.rows[0].checkbox;
    checkbox.checked = checked;
    checkbox.handlers.change.call(checkbox);
    await tick(); await tick();
    const posts = env.requests.filter(r => r.method === 'POST');
    assert.equal(posts.length, mode === 'cancel' ? 0 : 1);
    if (posts.length) {
        const payload = new URLSearchParams(posts[0].body);
        assert.equal(payload.get('status'), checked ? '1' : '0');
        assert(posts[0].url.includes('atualizar-status-' + module + '.php'));
    }
    assert.equal(checkbox.checked, mode === 'success' ? checked : !checked);
    if (mode === 'failure') assert(env.alerts.some(text => text.includes('Falha simulada')));
    return { requests: env.requests, checked: checkbox.checked };
}
(async () => {
    let statusCases = 0;
    for (const module of ['despesa', 'doacao', 'fornecedor', 'doador', 'projeto']) {
        const file = 'app/view/JS/listagem-' + module + '.js';
        const current = fs.readFileSync(file, 'utf8');
        for (const mode of ['success', 'failure', 'cancel']) for (const checked of [false, true]) {
            const after = await statusScenario(current, module, mode, checked);
            if (baseline) {
                const before = await statusScenario(Buffer.from(baseline[file], 'base64').toString('utf8'), module, mode, checked);
                assert.deepEqual(after, before, 'Payload/fluxo mudou em ' + module);
            }
            statusCases++;
        }
    }
    for (const [file,fn] of [['cadastro-doador', 'configurarCamposTipo'], ['editar-doador', 'aplicarVisibilidadeTipo']]) {
        const env = environment();
        vm.runInContext(fs.readFileSync('app/view/JS/' + file + '.js', 'utf8'), env.context);
        for (const [tipo,documento] of [['Pessoa Física', 'CPF'], ['Pessoa Jurídica', 'CNPJ']]) {
            env.context[fn](tipo);
            assert(env.get('label-doc').innerHTML.startsWith(documento + ' <span'));
            assert(env.get('label-doc').innerHTML.includes('*'));
            assert.equal(env.pf[0].style.display, documento === 'CPF' ? 'flex' : 'none');
            assert.equal(env.pj[0].style.display, documento === 'CNPJ' ? 'flex' : 'none');
        }
    }
    const env = environment();
    vm.runInContext(fs.readFileSync('app/view/JS/editar-doador.js', 'utf8'), env.context);
    await env.get('formEdicaoDoador').handlers.submit({ preventDefault() {} });
    assert.equal(env.alerts[0], 'Não foi possível salvar os dados do doador. Tente novamente.');
    assert.equal(env.context.window.location.href, '');
    console.log('OK: ' + statusCases + ' cenários de ativação/erro/cancelamento, payloads 0/1 preservados; 4 cenários PF/PJ e falha de JSON sem redirecionamento.'
        + (baseline ? ' Comparação antes/depois aprovada.' : ''));
})().catch(error => { console.error(error); process.exitCode = 1; });
