(function (global) {
    'use strict';
    const estados = { AC: 'Acre', AL: 'Alagoas', AP: 'Amapá', AM: 'Amazonas', BA: 'Bahia', CE: 'Ceará', DF: 'Distrito Federal', ES: 'Espírito Santo', GO: 'Goiás', MA: 'Maranhão', MT: 'Mato Grosso', MS: 'Mato Grosso do Sul', MG: 'Minas Gerais', PA: 'Pará', PB: 'Paraíba', PR: 'Paraná', PE: 'Pernambuco', PI: 'Piauí', RJ: 'Rio de Janeiro', RN: 'Rio Grande do Norte', RS: 'Rio Grande do Sul', RO: 'Rondônia', RR: 'Roraima', SC: 'Santa Catarina', SP: 'São Paulo', SE: 'Sergipe', TO: 'Tocantins' };
    const digitos = valor => String(valor || '').replace(/[.\/\-\s]/g, '');

    function validarCnpj(valor) {
        const cnpj = digitos(valor);
        if (!/^\d{14}$/.test(cnpj) || /^(\d)\1+$/.test(cnpj)) return false;
        for (let tamanho = 12; tamanho <= 13; tamanho++) {
            let soma = 0, peso = tamanho - 7;
            for (let i = 0; i < tamanho; i++) {
                soma += Number(cnpj[i]) * peso--;
                if (peso < 2) peso = 9;
            }
            const resto = soma % 11;
            if (Number(cnpj[tamanho]) !== (resto < 2 ? 0 : 11 - resto)) return false;
        }
        return true;
    }

    function mapear(dados, fornecedor) {
        const texto = chave => typeof dados[chave] === 'string' ? dados[chave].trim() : '';
        const limite = (valor, tamanho) => valor.length <= tamanho ? valor : '';
        const tipo = texto('tipo_logradouro'), rua = texto('logradouro');
        const telefone = Array.isArray(dados.telefones) ? dados.telefones.find(t => !t.is_fax && /^\d{10,11}$/.test(String(t.ddd || '') + String(t.numero || ''))) : null;
        const numero = texto('numero');
        const campos = {
            razao_social: limite(texto('razao_social'), 150),
            [fornecedor ? 'nome' : 'nome_fantasia']: limite(texto('nome_fantasia'), 100),
            logradouro: limite((tipo && rua && !rua.toUpperCase().startsWith(tipo.toUpperCase()) ? tipo + ' ' : '') + rua, 150),
            numero: Array.from(numero).length <= 20 ? numero : '',
            complemento: limite(texto('complemento'), 150), bairro: limite(texto('bairro'), 150),
            cidade: limite(texto('municipio'), 150), estado: estados[texto('uf')] || '',
            cep: /^\d{8}$/.test(texto('cep').replace(/\D/g, '')) ? texto('cep').replace(/\D/g, '') : '',
            email: /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(texto('email')) ? limite(texto('email'), 100) : '',
            telefone: telefone ? String(telefone.ddd) + String(telefone.numero) : ''
        };
        return { campos, numeroIncompativel: !!numero && !campos.numero };
    }

    function inicializar(doc, buscar, timeoutMs = 8000) {
        const campo = doc.getElementById('cpf_cnpj');
        if (!campo) return;
        const form = campo.closest('form');
        const fornecedor = !!form && /Fornecedor/.test(form.id);
        if (!form || (!fornecedor && !['formCadastro', 'formEdicaoDoador'].includes(form.id))) return;
        const status = doc.createElement('p');
        status.setAttribute('role', 'status'); status.setAttribute('aria-live', 'polite');
        campo.insertAdjacentElement('afterend', status);
        const automaticos = new Map();
        let sequencia = 0, controlador, ultimo = null;
        const pj = () => fornecedor || /jurídica|^pj$/iu.test(doc.getElementById('tipo')?.value || '');
        const cancelar = () => { sequencia++; controlador?.abort(); ultimo = null; status.textContent = ''; };
        campo.addEventListener('input', cancelar);
        doc.getElementById('tipo')?.addEventListener('change', cancelar);
        form.addEventListener('reset', cancelar);

        async function consultar() {
            if (!pj()) return;
            const cnpj = digitos(campo.value);
            if (!cnpj) return;
            if (!validarCnpj(cnpj)) { status.textContent = 'CNPJ inválido. Confira os 14 dígitos e os verificadores.'; return; }
            if (ultimo === cnpj) return;
            controlador?.abort();
            const requisicao = new global.AbortController(); controlador = requisicao;
            const atual = ++sequencia; ultimo = cnpj;
            const valores = new Map(Array.from(form.querySelectorAll('input,select')).map(e => [e.name || e.id, e.value]));
            const timer = global.setTimeout(() => requisicao.abort(), timeoutMs);
            status.textContent = 'Consultando CNPJ...';
            try {
                // Somente o CNPJ público é enviado; não enviar cookies ou dados do formulário.
                const resposta = await buscar('https://api.opencnpj.org/' + cnpj, {
                    signal: requisicao.signal, credentials: 'omit', referrerPolicy: 'no-referrer', headers: { Accept: 'application/json' }
                });
                if (atual !== sequencia) return;
                if (resposta.status === 404) { status.textContent = 'CNPJ não encontrado. Preencha os dados manualmente.'; ultimo = null; return; }
                if (!resposta.ok) throw new Error('Serviço indisponível');
                const dados = await resposta.json();
                if (atual !== sequencia || digitos(campo.value) !== cnpj || !pj()) return;
                if (!dados || dados.cnpj !== cnpj || typeof dados.razao_social !== 'string') throw new Error('Resposta incompatível');
                const resultado = mapear(dados, fornecedor);
                for (const [id, valor] of Object.entries(resultado.campos)) {
                    const destino = form.querySelector('#' + id) || form.querySelector('[name="' + id + '"]');
                    if (!destino || !valor || destino.value !== valores.get(id)) continue;
                    if (destino.value.trim() && automaticos.get(id) !== destino.value) continue;
                    if (destino.tagName === 'SELECT' && !Array.from(destino.options).some(o => o.value === valor)) {
                        const option = doc.createElement('option'); option.value = valor; option.textContent = valor; destino.appendChild(option);
                    }
                    destino.value = valor;
                    // Máscaras existentes reagem a input; não disparar a consulta de CEP por change.
                    destino.dispatchEvent(new global.Event('input', { bubbles: true }));
                    automaticos.set(id, destino.value);
                }
                status.textContent = 'Dados disponíveis preenchidos. Revise e ajuste antes de salvar.'
                    + (resultado.numeroIncompativel ? ' O formato do número não é suportado pelo campo atual; ele não foi preenchido.' : '');
            } catch (_) {
                if (atual === sequencia) { ultimo = null; status.textContent = 'Consulta de CNPJ indisponível. Você pode preencher os dados manualmente e salvar.'; }
            } finally { global.clearTimeout(timer); }
        }
        campo.addEventListener('blur', consultar);
        return { consultar, status };
    }
    const api = { validarCnpj, mapear, inicializar };
    if (global.document) global.AlmaConsultaCnpj = api;
    if (typeof module !== 'undefined' && module.exports) module.exports = api;
    if (global.document) global.document.addEventListener('DOMContentLoaded', () => inicializar(global.document, global.fetch.bind(global)));
})(typeof window !== 'undefined' ? window : globalThis);
