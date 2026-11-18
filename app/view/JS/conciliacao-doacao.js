const doacoesMock = [
    { id_doacao: 101, nome_doador: "Exemplo 1",     email_doador: "demonstracao@example.invalid",   tipo: "Monetária",    projeto: "Expansão Médica",    forma_pagamento: "PIX",                    valor: 250.00,  data_doacao: "2026-10-16", descricao: "Contribuição mensal",                      comprovante: "comprovante-pix-101.pdf",  status: "pendente",   data_confirmacao: null, confirmado_por: null },
    { id_doacao: 102, nome_doador: "Exemplo 2",      email_doador: "demonstracao@example.invalid",      tipo: "Monetária",    projeto: "Saúde Comunitária",  forma_pagamento: "Transferência Bancária", valor: 1500.00, data_doacao: "2026-10-15", descricao: "Doação para mutirão de atendimento",       comprovante: "ted-102.pdf",              status: "pendente",   data_confirmacao: null, confirmado_por: null },
    { id_doacao: 103, nome_doador: "Exemplo 3", email_doador: "demonstracao@example.invalid",  tipo: "Insumos",      projeto: "Saúde Comunitária",  forma_pagamento: "Dinheiro",               valor: 800.00,  data_doacao: "2026-10-14", descricao: "Cestas básicas para famílias atendidas",   comprovante: null,                       status: "pendente",   data_confirmacao: null, confirmado_por: null },
    { id_doacao: 104, nome_doador: "Exemplo 4",      email_doador: "demonstracao@example.invalid",      tipo: "Monetária",    projeto: "Expansão Médica",    forma_pagamento: "Cartão",                 valor: 100.00,  data_doacao: "2026-10-12", descricao: "Doação avulsa",                            comprovante: "cartao-104.jpg",           status: "confirmada", data_confirmacao: "2026-10-13", confirmado_por: "Exemplo 5" },
    { id_doacao: 105, nome_doador: "Exemplo 6",   email_doador: "demonstracao@example.invalid",   tipo: "Monetária",    projeto: "Expansão Médica",    forma_pagamento: "PIX",                    valor: 50.00,   data_doacao: "2026-10-10", descricao: "Contribuição mensal",                      comprovante: "pix-105.png",              status: "pendente",   data_confirmacao: null, confirmado_por: null },
    { id_doacao: 106, nome_doador: "Exemplo 7",      email_doador: "demonstracao@example.invalid", tipo: "Medicamentos", projeto: "Saúde Comunitária", forma_pagamento: "Transferência Bancária", valor: 3200.00, data_doacao: "2026-10-08", descricao: "Lote de medicamentos para a unidade móvel", comprovante: "nf-106.pdf",               status: "confirmada", data_confirmacao: "2026-10-09", confirmado_por: "Exemplo 8" },
    { id_doacao: 107, nome_doador: "Exemplo 9",       email_doador: "demonstracao@example.invalid",      tipo: "Monetária",    projeto: "Expansão Médica",    forma_pagamento: "PIX",                    valor: 75.00,   data_doacao: "2026-10-05", descricao: "Doação em memória de familiar",           comprovante: "pix-107.pdf",              status: "confirmada", data_confirmacao: "2026-10-06", confirmado_por: "Exemplo 10" },
];

let doacoes = [];
let doacaoSelecionada = null;

// --------------------------------------------------------------------------
// Utilitários (mesmo formato usado em listagem-doacao.js)
// --------------------------------------------------------------------------
function formatarData(data) {
    if (!data) return "-";
    const [ano, mes, dia] = data.split("-");
    return `${dia}/${mes}/${ano}`;
}

function formatarValor(valor) {
    const numero = parseFloat(valor) || 0;
    return numero.toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

function hojeISO() {
    const d = new Date();
    const mes = String(d.getMonth() + 1).padStart(2, "0");
    const dia = String(d.getDate()).padStart(2, "0");
    return `${d.getFullYear()}-${mes}-${dia}`;
}

function mostrarToast(mensagem) {
    const toast = document.getElementById("toastConciliacao");
    toast.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${mensagem}`;
    toast.classList.add("visivel");
    clearTimeout(mostrarToast.timer);
    mostrarToast.timer = setTimeout(() => toast.classList.remove("visivel"), 3500);
}

// --------------------------------------------------------------------------
// Carregamento dos dados
// --------------------------------------------------------------------------
async function carregarDoacoes() {
    // PROTÓTIPO: dados fictícios.
    // Back-end: const resposta = await fetch('../../actions/listagem-conciliacao.php');
    //           doacoes = await resposta.json();
    doacoes = doacoesMock.map((d) => ({ ...d }));

    preencherFiltroProjetos();
    atualizarResumo();
    renderizarTabela();
}

function preencherFiltroProjetos() {
    const select = document.getElementById("projeto");
    const projetos = [...new Set(doacoes.map((d) => d.projeto).filter(Boolean))].sort();

    projetos.forEach((projeto) => {
        const option = document.createElement("option");
        option.value = projeto;
        option.textContent = projeto;
        select.appendChild(option);
    });
}

// --------------------------------------------------------------------------
// Cards de resumo
// --------------------------------------------------------------------------
function atualizarResumo() {
    const pendentes   = doacoes.filter((d) => d.status === "pendente");
    const confirmadas = doacoes.filter((d) => d.status === "confirmada");
    const somar = (lista) => lista.reduce((total, d) => total + parseFloat(d.valor || 0), 0);

    document.getElementById("resumoPendentesQtd").textContent     = pendentes.length;
    document.getElementById("resumoPendentesValor").textContent   = formatarValor(somar(pendentes));
    document.getElementById("resumoConfirmadasQtd").textContent   = confirmadas.length;
    document.getElementById("resumoConfirmadasValor").textContent = formatarValor(somar(confirmadas));
}

// --------------------------------------------------------------------------
// Filtros (no protótipo filtram no navegador; no back-end irão na consulta)
// --------------------------------------------------------------------------
function aplicarFiltros(lista) {
    const busca      = document.getElementById("buscar").value.trim().toLowerCase();
    const dataInicio = document.getElementById("data_inicio").value;
    const dataFim    = document.getElementById("data_fim").value;
    const projeto    = document.getElementById("projeto").value;
    const status     = document.getElementById("status").value;

    return lista.filter((d) =>
        (!busca      || d.nome_doador.toLowerCase().includes(busca)) &&
        (!dataInicio || d.data_doacao >= dataInicio) &&
        (!dataFim    || d.data_doacao <= dataFim) &&
        (!projeto    || d.projeto === projeto) &&
        (!status     || d.status === status)
    );
}

// --------------------------------------------------------------------------
// Tabela
// --------------------------------------------------------------------------
function renderizarTabela() {
    const bodyTable = document.getElementById("bodyTable");
    bodyTable.innerHTML = "";

    const lista = aplicarFiltros(doacoes);

    if (lista.length === 0) {
        bodyTable.innerHTML = `
            <tr>
                <td colspan="8" class="tabela-vazia">Nenhuma doação encontrada para os filtros selecionados.</td>
            </tr>`;
        return;
    }

    lista.forEach((doacao) => {
        const tr = document.createElement("tr");
        const pendente = doacao.status === "pendente";

        const comprovante = doacao.comprovante
            ? `<a href="#" class="link-comprovante" title="${doacao.comprovante}"><i class="bi bi-paperclip"></i> Ver</a>`
            : `<span class="sem-comprovante">Sem anexo</span>`;

        const status = pendente
            ? `<span class="status-badge pendente"><i class="bi bi-hourglass-split"></i> Pendente</span>`
            : `<span class="status-badge confirmada" title="Confirmada em ${formatarData(doacao.data_confirmacao)}"><i class="bi bi-check2"></i> Confirmada</span>`;

        const acoes = pendente
            ? `<i class="bi bi-eye" title="Visualizar" data-acao="ver"></i>
               <button type="button" class="btn-confirmar" data-acao="confirmar">
                   <i class="bi bi-check-lg"></i> Confirmar
               </button>`
            : `<i class="bi bi-eye" title="Visualizar" data-acao="ver"></i>
               <i class="bi bi-envelope" title="Reenviar recibo" data-acao="reenviar"></i>`;

        tr.innerHTML = `
            <td>${formatarData(doacao.data_doacao)}</td>
            <td>${doacao.nome_doador}</td>
            <td>${doacao.projeto || "Sem projeto"}</td>
            <td>${doacao.forma_pagamento}</td>
            <td class="valor">${formatarValor(doacao.valor)}</td>
            <td>${comprovante}</td>
            <td>${status}</td>
            <td><div class="table-actions-cell">${acoes}</div></td>
        `;

        tr.querySelectorAll("[data-acao]").forEach((el) => {
            el.addEventListener("click", () => {
                if (el.dataset.acao === "reenviar") {
                    reenviarRecibo(doacao);
                } else {
                    abrirModal(doacao);
                }
            });
        });

        bodyTable.appendChild(tr);
    });
}

// --------------------------------------------------------------------------
// Modal de confirmação
// --------------------------------------------------------------------------
const modal = document.getElementById("modalConciliacao");

function abrirModal(doacao) {
    doacaoSelecionada = doacao;
    const pendente = doacao.status === "pendente";

    document.querySelector("#modalConciliacao .modal-header h2").textContent =
        pendente ? "Confirmar Recebimento" : "Doação Confirmada";

    document.getElementById("detDoador").textContent    = doacao.nome_doador;
    document.getElementById("detEmail").textContent     = doacao.email_doador || "Não informado";
    document.getElementById("detTipo").textContent      = doacao.tipo;
    document.getElementById("detProjeto").textContent   = doacao.projeto || "Sem projeto";
    document.getElementById("detData").textContent      = formatarData(doacao.data_doacao);
    document.getElementById("detForma").textContent     = doacao.forma_pagamento;
    document.getElementById("detDescricao").textContent = doacao.descricao || "-";
    document.getElementById("detValor").textContent     = formatarValor(doacao.valor);

    document.getElementById("detComprovante").innerHTML = doacao.comprovante
        ? `<a href="#" class="link-comprovante"><i class="bi bi-paperclip"></i> ${doacao.comprovante}</a>`
        : `<span class="sem-comprovante">Nenhum arquivo anexado</span>`;

    document.getElementById("avisoSemComprovante").classList.toggle("oculta", !pendente || !!doacao.comprovante);
    document.getElementById("blocoEnvioRecibo").classList.toggle("oculta", !pendente);
    document.getElementById("btnConfirmarRecebimento").classList.toggle("oculta", !pendente);
    document.getElementById("btnReenviarRecibo").classList.toggle("oculta", pendente);
    document.getElementById("infoConfirmada").classList.toggle("oculta", pendente);
    document.getElementById("btnCancelarConciliacao").textContent = pendente ? "Cancelar" : "Fechar";

    // Sem e-mail cadastrado não há como enviar o recibo
    const checkRecibo = document.getElementById("enviarRecibo");
    checkRecibo.checked  = !!doacao.email_doador;
    checkRecibo.disabled = !doacao.email_doador;

    if (!pendente) {
        document.getElementById("textoConfirmada").textContent =
            `Recebimento confirmado em ${formatarData(doacao.data_confirmacao)} por ${doacao.confirmado_por}. ` +
            `Recibo enviado para ${doacao.email_doador}.`;
    }

    modal.classList.remove("oculta");
}

function fecharModal() {
    modal.classList.add("oculta");
    doacaoSelecionada = null;
}

function confirmarDoacao() {
    if (!doacaoSelecionada) return;

    const enviarRecibo = document.getElementById("enviarRecibo").checked;

    // PROTÓTIPO: atualiza só na tela.
    // Back-end: POST para '../../actions/confirmar-doacao.php' com id_doacao e enviar_recibo.
    doacaoSelecionada.status           = "confirmada";
    doacaoSelecionada.data_confirmacao = hojeISO();
    doacaoSelecionada.confirmado_por   = "Administrador";

    const nome  = doacaoSelecionada.nome_doador;
    const email = doacaoSelecionada.email_doador;

    fecharModal();
    atualizarResumo();
    renderizarTabela();

    mostrarToast(enviarRecibo
        ? `Doação de ${nome} confirmada. Recibo enviado para ${email}.`
        : `Doação de ${nome} confirmada.`);
}

function reenviarRecibo(doacao) {
    if (!confirm(`Reenviar o recibo para ${doacao.email_doador}?`)) return;

    // Back-end: POST para '../../actions/reenviar-recibo-doacao.php' com id_doacao.
    fecharModal();
    mostrarToast(`Recibo reenviado para ${doacao.email_doador}.`);
}

// --------------------------------------------------------------------------
// Eventos
// --------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    carregarDoacoes();

    document.getElementById("formFiltro").addEventListener("submit", (e) => {
        e.preventDefault();
        renderizarTabela();
    });

    document.getElementById("btnFecharModalConciliacao").addEventListener("click", fecharModal);
    document.getElementById("btnCancelarConciliacao").addEventListener("click", fecharModal);
    document.getElementById("btnConfirmarRecebimento").addEventListener("click", confirmarDoacao);
    document.getElementById("btnReenviarRecibo").addEventListener("click", () => reenviarRecibo(doacaoSelecionada));

    // Fecha clicando fora do conteúdo ou com ESC
    modal.addEventListener("click", (e) => {
        if (e.target === modal) fecharModal();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !modal.classList.contains("oculta")) fecharModal();
    });
});
