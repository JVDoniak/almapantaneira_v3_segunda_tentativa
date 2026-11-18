// Cadastros de fornecedor/projeto sem perder a despesa em preenchimento.
document.addEventListener('DOMContentLoaded', () => {
    const modal = document.getElementById('modal');
    if (!modal) return;
    const conteudo = document.getElementById('conteudoModalCadastro');
    const status = document.getElementById('statusModalCadastro');
    const fechar = document.getElementById('btnFecharModalCadastro');
    let origem, sequencia = 0, salvando = false;
    function ocultar() {
        if (salvando) return;
        sequencia++;
        modal.hidden = true; modal.classList.remove('show');
        conteudo.replaceChildren(); status.textContent = ''; origem?.focus();
    }
    fechar.addEventListener('click', ocultar);
    modal.addEventListener('click', e => { if (e.target === modal) ocultar(); });
    document.addEventListener('keydown', e => { if (e.key === 'Escape' && !modal.hidden) ocultar(); });
    document.querySelectorAll('button[data-form]').forEach(botao => botao.addEventListener('click', async () => {
        const tipo = botao.dataset.form;
        if (!['fornecedor','projeto'].includes(tipo)) return;
        origem = botao; const atual = ++sequencia;
        modal.hidden = false; modal.classList.add('show'); conteudo.replaceChildren();
        document.getElementById('tituloModalCadastro').textContent = tipo === 'projeto' ? 'Cadastrar Projeto' : 'Cadastrar Fornecedor';
        status.textContent = 'Carregando formulário…';
        try {
            const resposta = await fetch(`../components/forms/formulario-cadastro-${tipo}.php`);
            if (!resposta.ok) throw Error('Formulário indisponível.');
            const html = await resposta.text();
            if (atual !== sequencia) return;
            conteudo.innerHTML = html;
            const form = conteudo.querySelector('form');
            if (!form) throw Error('Formulário indisponível.');
            status.textContent = '';
            const escopo = { getElementById: id => form.querySelector(`[name="${id}"]`), createElement: document.createElement.bind(document) };
            if (tipo === 'fornecedor') window.AlmaConsultaCnpj?.inicializar(escopo, window.fetch.bind(window));
            form.querySelector('input,select,textarea')?.focus();
            form.addEventListener('submit', async e => {
                e.preventDefault();
                if (salvando || !form.reportValidity()) return;
                if (tipo === 'fornecedor' && !window.AlmaConsultaCnpj.validarCnpj(form.elements.cpf_cnpj.value)) {
                    status.textContent = 'Informe um CNPJ válido.'; return;
                }
                const botaoSalvar = form.querySelector('button[type="submit"]');
                const campo = document.getElementById(tipo);
                const idsAntes = new Set((campo._itensCadastro || []).map(item => String(item[`id_${tipo}`])));
                salvando = true; botaoSalvar.disabled = true; fechar.disabled = true;
                let confirmado = false;
                try {
                    const resposta = await fetch(`../../actions/cadastro-${tipo}.php`, { method: 'POST', body: new FormData(form) });
                    const resultado = await resposta.json();
                    if (!resposta.ok || !(resultado.successo || resultado.sucess)) throw Error(resultado.message || 'Não foi possível cadastrar.');
                    confirmado = true;
                    const opcoesResposta = await fetch('../../actions/cadastro-despesa.php');
                    if (!opcoesResposta.ok) throw Error('Cadastro concluído. Atualize as opções antes de selecionar; não reenvie o cadastro.');
                    const opcoes = await opcoesResposta.json();
                    for (const [nome, itens] of [['fornecedor',opcoes.fornecedores],['projeto',opcoes.projetos]]) {
                        if (!Array.isArray(itens)) throw Error('Cadastro concluído. Reabra a tela para atualizar as opções; não reenvie.');
                        ativarAutocomplete(document.getElementById(nome), document.getElementById(`id_${nome}`), document.getElementById(nome === 'fornecedor' ? 'sugestoes_fornecedores' : 'sugestoes_projetos'), itens, `id_${nome}`);
                    }
                    const lista = tipo === 'fornecedor' ? opcoes.fornecedores : opcoes.projetos;
                    const novos = lista.filter(item => !idsAntes.has(String(item[`id_${tipo}`])) && item.nome === form.elements.nome.value.trim());
                    if (novos.length === 1) {
                        campo.value = novos[0].nome;
                        document.getElementById(`id_${tipo}`).value = novos[0][`id_${tipo}`];
                    }
                    salvando = false; ocultar();
                } catch (erro) {
                    status.textContent = confirmado ? 'Cadastro concluído. Selecione o registro pela lista quando as opções estiverem disponíveis; não reenvie.' : 'Não foi possível cadastrar. Confira os dados e tente novamente.';
                } finally {
                    salvando = false; fechar.disabled = false;
                    if (!confirmado) botaoSalvar.disabled = false;
                }
            });
        } catch (_) {
            if (atual === sequencia) status.textContent = 'Não foi possível carregar o formulário. Feche e tente novamente.';
        }
    }));
});
