const assert = require('assert/strict');
const { validarCnpj, mapear, inicializar } = require('../view/JS/consulta-cnpj.js');
const cnpj = '11222333000181';
const dados = { cnpj, razao_social: 'Empresa ficticia LTDA', nome_fantasia: 'Empresa ficticia', tipo_logradouro: 'Rua',
    logradouro: 'Teste', numero: '10', complemento: '', bairro: 'Centro', cep: '79000000', uf: 'MS', municipio: 'Campo Grande',
    email: 'empresa@example.invalid', telefones: [{ ddd: '67', numero: '999999999', is_fax: false }], QSA: [{ nome_socio: 'Nao exibir' }] };
function ambiente(formId = 'formCadastroFornecedor', tipo = 'Pessoa Jurídica') {
    const node = (id, value = '') => ({ id, value, tagName: 'INPUT', handlers: {}, options: [],
        addEventListener(event, fn) { this.handlers[event] = fn; }, setAttribute() {},
        dispatchEvent() {}, appendChild(option) { this.options.push(option); }, insertAdjacentElement() {} });
    const nodes = {};
    for (const id of ['cpf_cnpj','tipo','nome','nome_fantasia','razao_social','logradouro','numero','complemento','bairro','cep','estado','cidade','email','telefone']) nodes[id] = node(id);
    nodes.cpf_cnpj.value = cnpj; nodes.tipo.value = tipo;
    const form = { id: formId, handlers: {}, querySelectorAll() { return Object.values(nodes); },
        querySelector(selector) { return nodes[selector.slice(1)]; }, addEventListener(event, fn) { this.handlers[event] = fn; } };
    nodes.cpf_cnpj.closest = () => form;
    return { nodes, form, doc: { getElementById(id) { return nodes[id]; }, createElement() { return node(''); } } };
}
(async () => {
    assert(validarCnpj(cnpj)); assert(validarCnpj('11.222.333/0001-81'));
    for (const invalido of ['00000000000000', '11222333000180', '123', 'ABC12333000181']) assert(!validarCnpj(invalido));
    for (const form of ['formCadastroFornecedor','formEdicaoFornecedor','formCadastro','formEdicaoDoador']) {
        const a = ambiente(form); let request;
        const consulta = inicializar(a.doc, async (url, options) => { request = { url, options }; return { ok: true, json: async () => dados }; });
        await consulta.consultar();
        assert.equal(a.nodes.razao_social.value, dados.razao_social);
        assert.equal(a.nodes.estado.value, 'Mato Grosso do Sul');
        assert.equal(a.nodes[form.includes('Fornecedor') ? 'nome' : 'nome_fantasia'].value, dados.nome_fantasia);
        assert.equal(request.url, 'https://api.opencnpj.org/' + cnpj);
        assert.equal(request.options.credentials, 'omit'); assert(!('body' in request.options));
        assert(!consulta.status.textContent.includes('Nao exibir'));
        a.nodes.razao_social.value = 'Correcao manual';
        assert.equal(a.nodes.razao_social.value, 'Correcao manual');
    }
    for (const status of [404, 429, 503]) {
        const a = ambiente(); a.nodes.nome.value = 'Nome manual';
        const consulta = inicializar(a.doc, async () => ({ status, ok: false }));
        await consulta.consultar(); assert.equal(a.nodes.nome.value, 'Nome manual');
        assert(consulta.status.textContent.includes('manualmente')); assert(!a.form.handlers.submit);
    }
    const pf = ambiente('formCadastro','Pessoa Física'); let chamadas = 0;
    await inicializar(pf.doc, async () => { chamadas++; }).consultar(); assert.equal(chamadas, 0);
    const a = ambiente(); a.nodes.nome.value = 'Digitado antes';
    let liberar;
    const consulta = inicializar(a.doc, () => new Promise(resolve => { liberar = resolve; }));
    const pendente = consulta.consultar(); a.nodes.razao_social.value = 'Digitado durante';
    liberar({ ok: true, json: async () => dados }); await pendente;
    assert.equal(a.nodes.nome.value, 'Digitado antes'); assert.equal(a.nodes.razao_social.value, 'Digitado durante');
    const b = ambiente(); let concluir;
    const outra = inicializar(b.doc, () => new Promise(resolve => { concluir = resolve; }));
    const antiga = outra.consultar(); b.nodes.cpf_cnpj.value = '00000000000191'; b.nodes.cpf_cnpj.handlers.input();
    concluir({ ok: true, json: async () => dados }); await antiga; assert.equal(b.nodes.razao_social.value, '');
    const timeout = ambiente();
    const lenta = inicializar(timeout.doc, (_, options) => new Promise((_, reject) => {
        options.signal.addEventListener('abort', () => reject(new Error('timeout')));
    }), 5);
    await lenta.consultar(); assert(lenta.status.textContent.includes('manualmente'));
    const malformado = ambiente();
    await inicializar(malformado.doc, async () => ({ ok: true, json: async () => ({ cnpj: 'outro' }) })).consultar();
    assert.equal(malformado.nodes.razao_social.value, '');
    for (const numero of ['25', '1500', 'S/N']) assert.equal(mapear({ ...dados, numero }, true).campos.numero, numero);
    assert.equal(mapear({ ...dados, numero: '1'.repeat(21) }, true).campos.numero, '');
    
    console.log('OK: CNPJ/DV, quatro formularios, PF sem consulta, mapeamento minimo, edicao manual, 404/429/503, timeout, resposta obsoleta/invalida e numero incompativel. API simulada, sem rede.');
})().catch(error => { console.error(error); process.exitCode = 1; });
