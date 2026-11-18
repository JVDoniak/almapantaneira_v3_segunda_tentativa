// Testa a validacao real do cadastro em DOM simulado, sem navegador ou rede.
const fs = require('fs');
const vm = require('vm');
const assert = require('assert/strict');
const campos = Object.fromEntries(Object.entries({
    nome: 'Medicamentos', fornecedor: '', id_fornecedor: '', projeto: '',
    id_projeto: '', id_categoria_despesa: '37', valor: '20',
    data_vencimento: '2026-10-31', forma_pagamento: 'Pix'
}).map(([id, value]) => [id, { value, focus() {} }]));
const contexto = {
    document: { getElementById: id => campos[id] || null },
    window: { addEventListener() {} }, alert() {}, console
};
vm.createContext(contexto);
vm.runInContext(fs.readFileSync('app/view/JS/cadastro-despesa.js', 'utf8'), contexto);
assert.equal(contexto.validarFormularioDespesa(), true);
campos.fornecedor.value = 'Texto sem selecao';
assert.equal(contexto.validarFormularioDespesa(), false);
campos.fornecedor.value = '';
campos.projeto.value = 'Texto sem selecao';
assert.equal(contexto.validarFormularioDespesa(), false);
campos.projeto.value = '';
campos.id_categoria_despesa.value = '';
assert.equal(contexto.validarFormularioDespesa(), false);
console.log('OK: categoria dinamica e opcionais; texto sem vinculo e categoria vazia rejeitados. Nenhuma rede utilizada.');
