// ==========================================================================
// CONCILIAÇÃO DE DESPESAS - Alma Pantaneira
// --------------------------------------------------------------------------
// PROTÓTIPO (somente front-end): os dados abaixo são fictícios.
// Na etapa do back-end:
//   - carregarDespesas()    -> fetch('../../actions/listagem-conciliacao-despesa.php')
//   - confirmarPagamento()  -> POST para '../../actions/confirmar-pagamento-despesa.php'
//                              com id_despesa, data_pagamento, valor_pago e o
//                              comprovante (FormData, igual ao cadastro de despesa).
//
// No banco, a despesa só guarda "pendente" ou "paga".
// "Vencida" é calculada: pendente com data_vencimento anterior a hoje.
// ==========================================================================

// Datas fictícias relativas a hoje, para o protótipo sempre ter vencidas e a vencer
function diasAPartirDeHoje(dias) {
    const d = new Date();
    d.setDate(d.getDate() + dias);
    return dataISO(d);
}

const despesasMock = [
    { id_despesa: 201, nome: "Conta de energia - sede",       fornecedor: "Exemplo 1",              categoria: "Manutenção",             projeto: "Saúde Comunitária", forma_pagamento: "Boleto", valor: 486.90,  data_vencimento: diasAPartirDeHoje(-6), descricao: "Fatura de setembro",                         comprovante: "boleto-energia-set.pdf", status: "pendente" },
    { id_despesa: 202, nome: "Combustível unidade móvel",      fornecedor: "Exemplo 2",           categoria: "Transporte",             projeto: "Unidade Móvel",     forma_pagamento: "Cartao", valor: 750.00,  data_vencimento: diasAPartirDeHoje(-2), descricao: "Abastecimento para atendimento em Miranda",  comprovante: null,                      status: "pendente" },
    { id_despesa: 203, nome: "Compra de medicamentos",         fornecedor: "Exemplo 3",        categoria: "Medicamentos",           projeto: "Expansão Médica",   forma_pagamento: "Pix",    valor: 2340.00, data_vencimento: diasAPartirDeHoje(2),  descricao: "Reposição do estoque da unidade",            comprovante: "nf-farmacia-8812.pdf",   status: "pendente" },
    { id_despesa: 204, nome: "Cestas básicas",                 fornecedor: "Exemplo 4",   categoria: "Alimentação",            projeto: "Saúde Comunitária", forma_pagamento: "Boleto", valor: 1280.00, data_vencimento: diasAPartirDeHoje(5),  descricao: "Cestas para famílias atendidas",             comprovante: "boleto-cestas.pdf",      status: "pendente" },
    { id_despesa: 205, nome: "Material de escritório",         fornecedor: "Exemplo 5",        categoria: "Material de Escritório", projeto: "Educação em Saúde", forma_pagamento: "Pix",    valor: 189.50,  data_vencimento: diasAPartirDeHoje(12), descricao: "Papel, toner e canetas",                     comprovante: null,                      status: "pendente" },
    { id_despesa: 206, nome: "Manutenção do ar-condicionado",  fornecedor: "Exemplo 6",        categoria: "Manutenção",             projeto: "Expansão Médica",   forma_pagamento: "Pix",    valor: 350.00,  data_vencimento: diasAPartirDeHoje(-10), descricao: "Limpeza e recarga de gás",                  comprovante: "pix-climatiza.png",      status: "paga", data_pagamento: diasAPartirDeHoje(-11), valor_pago: 350.00, confirmado_por: "Exemplo 7" },
    { id_despesa: 207, nome: "Salário enfermeira",             fornecedor: "Exemplo 8",       categoria: "Salários",               projeto: "Unidade Móvel",     forma_pagamento: "Pix",    valor: 2900.00, data_vencimento: diasAPartirDeHoje(-15), descricao: "Competência de setembro",                   comprovante: "holerite-set.pdf",       status: "paga", data_pagamento: diasAPartirDeHoje(-15), valor_pago: 2900.00, confirmado_por: "Exemplo 9" },
    { id_despesa: 208, nome: "Internet sede",                  fornecedor: "Exemplo 10",            categoria: "Manutenção",             projeto: "Saúde Comunitária", forma_pagamento: "Boleto", valor: 129.90,  data_vencimento: diasAPartirDeHoje(-20), descricao: "Mensalidade",                               comprovante: "boleto-vivo.pdf",        status: "paga", data_pagamento: diasAPartirDeHoje(-18), valor_pago: 134.10, confirmado_por: "Exemplo 11" },
];

// Rótulos das formas de pagamento (os valores vêm do cadastro: Pix, Cartao, Boleto)
const FORMAS_PAGAMENTO = { Pix: "PIX", Cartao: "Cartão", Boleto: "Boleto" };

let despesas = [];
let despesaSelecionada = null;

// --------------------------------------------------------------------------
// Utilitários
// --------------------------------------------------------------------------
function dataISO(d) {
    const mes = String(d.getMonth() + 1).padStart(2, "0");
    const dia = String(d.getDate()).padStart(2, "0");
    return `${d.getFullYear()}-${mes}-${dia}`;
}

function hojeISO() {
    return dataISO(new Date());
}

function formatarData(data) {
    if (!data) return "-";
    const [ano, mes, dia] = data.split("-");
    return `${dia}/${mes}/${ano}`;
}

function formatarValor(valor) {
    return (parseFloat(valor) || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

function diasEntre(dataInicio, dataFim) {
    const umDia = 24 * 60 * 60 * 1000;
    return Math.round((new Date(dataFim + "T00:00:00") - new Date(dataInicio + "T00:00:00")) / umDia);
}

// pendente | vencida | paga
function statusDaDespesa(despesa) {
    if (despesa.status === "paga") return "paga";
    return despesa.data_vencimento < hojeISO() ? "vencida" : "pendente";
}

function mostrarToast(mensagem) {
    const toast = document.getElementById("toastConciliacao");
    toast.innerHTML = `<i class="bi bi-check-circle-fill"></i> ${mensagem}`;
    toast.classList.add("visivel");
    clearTimeout(mostrarToast.timer);
    mostrarToast.timer = setTimeout(() => toast.classList.remove("visivel"), 3500);
}

// --------------------------------------------------------------------------
// Carregamento
// --------------------------------------------------------------------------
async function carregarDespesas() {
    // PROTÓTIPO: dados fictícios.
    // Back-end: const resposta = await fetch('../../actions/listagem-conciliacao-despesa.php');
    //           despesas = await resposta.json();
    despesas = despesasMock.map((d) => ({ ...d }));

    preencherSelect("id_categoria_despesa", despesas.map((d) => d.categoria));
    preencherSelect("id_projeto", despesas.map((d) => d.projeto));
    atualizarResumo();
    renderizarTabela();
}

// Back-end: as opções virão do banco (igual cadastro-despesa.php)
function preencherSelect(id, valores) {
    const select = document.getElementById(id);
    [...new Set(valores)].sort().forEach((valor) => {
        const option = document.createElement("option");
        option.value = valor;
        option.textContent = valor;
        select.appendChild(option);
    });
}

// --------------------------------------------------------------------------
// Cards de resumo
// --------------------------------------------------------------------------
function atualizarResumo() {
    const hoje = hojeISO();
    const somar = (lista) => lista.reduce((t, d) => t + parseFloat(d.valor_pago ?? d.valor ?? 0), 0);

    const aPagar   = despesas.filter((d) => statusDaDespesa(d) !== "paga");
    const vencidas = despesas.filter((d) => statusDaDespesa(d) === "vencida");
    const proximas = despesas.filter((d) =>
        statusDaDespesa(d) === "pendente" && diasEntre(hoje, d.data_vencimento) <= 7);
    const pagas    = despesas.filter((d) => statusDaDespesa(d) === "paga");

    const preencher = (idQtd, idValor, lista) => {
        document.getElementById(idQtd).textContent = lista.length;
        document.getElementById(idValor).textContent = formatarValor(somar(lista));
    };

    preencher("resumoPendentesQtd", "resumoPendentesValor", aPagar);
    preencher("resumoVencidasQtd",  "resumoVencidasValor",  vencidas);
    preencher("resumoProximasQtd",  "resumoProximasValor",  proximas);
    preencher("resumoPagasQtd",     "resumoPagasValor",     pagas);
}

// --------------------------------------------------------------------------
// Filtros (no protótipo filtram no navegador; no back-end irão na consulta)
// --------------------------------------------------------------------------
function aplicarFiltros(lista) {
    const busca      = document.getElementById("buscar").value.trim().toLowerCase();
    const categoria  = document.getElementById("id_categoria_despesa").value;
    const projeto    = document.getElementById("id_projeto").value;
    const dataInicio = document.getElementById("data_inicio").value;
    const dataFim    = document.getElementById("data_fim").value;
    const status     = document.getElementById("status").value;

    return lista.filter((d) => {
        const st = statusDaDespesa(d);
        const statusOk =
            !status ||
            (status === "a_pagar" && st !== "paga") ||
            status === st;

        return statusOk &&
            (!busca      || d.nome.toLowerCase().includes(busca) || d.fornecedor.toLowerCase().includes(busca)) &&
            (!categoria  || d.categoria === categoria) &&
            (!projeto    || d.projeto === projeto) &&
            (!dataInicio || d.data_vencimento >= dataInicio) &&
            (!dataFim    || d.data_vencimento <= dataFim);
    });
}

// --------------------------------------------------------------------------
// Tabela (a pagar: vencimento mais próximo primeiro; pagas: mais recentes primeiro)
// --------------------------------------------------------------------------
function textoStatus(despesa) {
    const st = statusDaDespesa(despesa);
    if (st === "paga") {
        return `<span class="status-badge paga" title="Paga em ${formatarData(despesa.data_pagamento)}"><i class="bi bi-check2"></i> Paga</span>`;
    }
    if (st === "vencida") {
        const dias = diasEntre(despesa.data_vencimento, hojeISO());
        return `<span class="status-badge vencida" title="Venceu há ${dias} dia(s)"><i class="bi bi-exclamation-triangle-fill"></i> Vencida</span>`;
    }
    return `<span class="status-badge pendente"><i class="bi bi-hourglass-split"></i> Pendente</span>`;
}

function renderizarTabela() {
    const bodyTable = document.getElementById("bodyTable");
    bodyTable.innerHTML = "";

    const lista = aplicarFiltros(despesas).sort((a, b) => {
        const aPaga = a.status === "paga", bPaga = b.status === "paga";
        if (aPaga !== bPaga) return aPaga ? 1 : -1;
        return aPaga
            ? b.data_vencimento.localeCompare(a.data_vencimento)
            : a.data_vencimento.localeCompare(b.data_vencimento);
    });

    if (lista.length === 0) {
        bodyTable.innerHTML = `
            <tr>
                <td colspan="10" class="tabela-vazia">Nenhuma despesa encontrada para os filtros selecionados.</td>
            </tr>`;
        return;
    }

    lista.forEach((despesa) => {
        const tr = document.createElement("tr");
        const paga = despesa.status === "paga";

        if (statusDaDespesa(despesa) === "vencida") tr.classList.add("linha-vencida");

        const comprovante = despesa.comprovante
            ? `<a href="#" class="link-comprovante" title="${despesa.comprovante}"><i class="bi bi-paperclip"></i> Ver</a>`
            : `<span class="sem-comprovante">Sem anexo</span>`;

        const acoes = paga
            ? `<i class="bi bi-eye" title="Visualizar" data-acao="ver"></i>`
            : `<i class="bi bi-eye" title="Visualizar" data-acao="ver"></i>
               <button type="button" class="btn-confirmar" data-acao="confirmar">
                   <i class="bi bi-check-lg"></i> Confirmar
               </button>`;

        tr.innerHTML = `
            <td>${formatarData(despesa.data_vencimento)}</td>
            <td>
                <span class="despesa-nome">${despesa.nome}</span>
                <span class="despesa-fornecedor">${despesa.fornecedor}</span>
            </td>
            <td>${despesa.tipo || "Não classificado"}</td>
            <td>${despesa.categoria}</td>
            <td>${despesa.projeto}</td>
            <td>${FORMAS_PAGAMENTO[despesa.forma_pagamento] || despesa.forma_pagamento}</td>
            <td class="valor">${formatarValor(despesa.valor)}</td>
            <td>${comprovante}</td>
            <td>${textoStatus(despesa)}</td>
            <td><div class="table-actions-cell">${acoes}</div></td>
        `;

        tr.querySelectorAll("[data-acao]").forEach((el) => {
            el.addEventListener("click", () => abrirModal(despesa));
        });

        bodyTable.appendChild(tr);
    });
}

// --------------------------------------------------------------------------
// Modal de confirmação do pagamento
// --------------------------------------------------------------------------
const modal = document.getElementById("modalConciliacao");
const mostrar = (id, visivel) => document.getElementById(id).classList.toggle("oculta", !visivel);

function abrirModal(despesa) {
    despesaSelecionada = despesa;
    const st = statusDaDespesa(despesa);
    const paga = st === "paga";

    document.getElementById("tituloModal").textContent = paga ? "Despesa Paga" : "Confirmar Pagamento";

    document.getElementById("detNome").textContent       = despesa.nome;
    document.getElementById("detFornecedor").textContent = despesa.fornecedor || "-";
    document.getElementById("detTipo").textContent = despesa.tipo || "Não classificado";
    document.getElementById("detCategoria").textContent  = despesa.categoria;
    document.getElementById("detProjeto").textContent    = despesa.projeto;
    document.getElementById("detVencimento").textContent = formatarData(despesa.data_vencimento);
    document.getElementById("detForma").textContent      = FORMAS_PAGAMENTO[despesa.forma_pagamento] || despesa.forma_pagamento;
    document.getElementById("detDescricao").textContent  = despesa.descricao || "-";
    document.getElementById("detValor").textContent      = formatarValor(despesa.valor);

    document.getElementById("detComprovante").innerHTML = despesa.comprovante
        ? `<a href="#" class="link-comprovante"><i class="bi bi-paperclip"></i> ${despesa.comprovante}</a>`
        : `<span class="sem-comprovante">Nenhum arquivo anexado</span>`;

    mostrar("blocoComprovantePagamento", !!despesa.comprovante_pagamento);
    if (despesa.comprovante_pagamento) {
        document.getElementById("detComprovantePagamento").innerHTML =
            `<a href="#" class="link-comprovante"><i class="bi bi-paperclip"></i> ${despesa.comprovante_pagamento}</a>`;
    }

    // Vencida
    mostrar("avisoVencida", st === "vencida");
    if (st === "vencida") {
        const dias = diasEntre(despesa.data_vencimento, hojeISO());
        document.getElementById("textoVencida").textContent =
            `Venceu há ${dias} ${dias === 1 ? "dia" : "dias"}. Verifique se houve cobrança de juros ou multa.`;
    }

    // Formulário de pagamento (só para a pagar)
    const form = document.getElementById("formPagamento");
    form.reset();
    mostrar("formPagamento", !paga);
    mostrar("blocoAnexo", !paga);
    document.getElementById("labelAnexo").textContent = despesa.comprovante
        ? "Comprovante de Pagamento (opcional)"
        : "Comprovante de Pagamento";
    mostrar("avisoSemComprovante", !paga && !despesa.comprovante);
    mostrar("avisoDiferenca", false);
    mostrar("avisoErroForm", false);
    mostrar("btnConfirmarPagamento", !paga);

    if (!paga) {
        const dataPagamento = document.getElementById("data_pagamento");
        dataPagamento.value = hojeISO();
        dataPagamento.max = hojeISO(); // não confirma pagamento no futuro
        document.getElementById("valor_pago").value = parseFloat(despesa.valor).toFixed(2);
    }

    // Já paga
    mostrar("infoPaga", paga);
    if (paga) {
        const diferenca = parseFloat(despesa.valor_pago) - parseFloat(despesa.valor);
        const textoDiferenca = Math.abs(diferenca) >= 0.01
            ? ` Valor pago ${formatarValor(despesa.valor_pago)} (${diferenca > 0 ? "+" : "−"}${formatarValor(Math.abs(diferenca))}).`
            : ` Valor pago ${formatarValor(despesa.valor_pago)}.`;
        document.getElementById("textoPaga").textContent =
            `Pagamento confirmado em ${formatarData(despesa.data_pagamento)} por ${despesa.confirmado_por}.` + textoDiferenca;
    }

    document.getElementById("btnCancelarConciliacao").textContent = paga ? "Fechar" : "Cancelar";

    modal.classList.remove("oculta");
}

function fecharModal() {
    modal.classList.add("oculta");
    despesaSelecionada = null;
}

// Avisa quando o valor pago difere do valor da despesa (juros, multa, desconto)
function verificarDiferenca() {
    if (!despesaSelecionada) return;
    const valorPago = parseFloat(document.getElementById("valor_pago").value);
    const valorDespesa = parseFloat(despesaSelecionada.valor);
    const diferenca = valorPago - valorDespesa;

    if (isNaN(valorPago) || Math.abs(diferenca) < 0.01) {
        mostrar("avisoDiferenca", false);
        return;
    }

    document.getElementById("textoDiferenca").textContent = diferenca > 0
        ? `Valor pago ${formatarValor(diferenca)} acima do valor da despesa (juros ou multa).`
        : `Valor pago ${formatarValor(Math.abs(diferenca))} abaixo do valor da despesa (desconto).`;
    mostrar("avisoDiferenca", true);
}

// Atualiza o aviso de "sem comprovante" quando um arquivo é escolhido
function verificarAnexo() {
    if (!despesaSelecionada || despesaSelecionada.comprovante) return;
    const temArquivo = document.getElementById("comprovante_pagamento").files.length > 0;
    mostrar("avisoSemComprovante", !temArquivo);
}

function mostrarErro(mensagem) {
    document.getElementById("textoErroForm").textContent = mensagem;
    mostrar("avisoErroForm", true);
}

function confirmarPagamento() {
    if (!despesaSelecionada) return;

    const dataPagamento = document.getElementById("data_pagamento").value;
    const valorPago = parseFloat(document.getElementById("valor_pago").value);
    const arquivo = document.getElementById("comprovante_pagamento").files[0];

    if (!dataPagamento) return mostrarErro("Informe a data do pagamento.");
    if (dataPagamento > hojeISO()) return mostrarErro("A data do pagamento não pode ser no futuro.");
    if (isNaN(valorPago) || valorPago <= 0) return mostrarErro("Informe o valor pago.");
    if (arquivo && arquivo.size > 5 * 1024 * 1024) return mostrarErro("O comprovante deve ter no máximo 5MB.");

    // PROTÓTIPO: atualiza só na tela.
    // Back-end: montar um FormData com id_despesa, data_pagamento, valor_pago
    // e o arquivo, e enviar para '../../actions/confirmar-pagamento-despesa.php'.
    despesaSelecionada.status         = "paga";
    despesaSelecionada.data_pagamento = dataPagamento;
    despesaSelecionada.valor_pago     = valorPago;
    despesaSelecionada.confirmado_por = "Administrador";
    // Sem documento no cadastro: o arquivo vira o comprovante principal.
    // Com documento (NF/boleto): fica guardado como comprovante de pagamento.
    if (arquivo && !despesaSelecionada.comprovante) despesaSelecionada.comprovante = arquivo.name;
    else if (arquivo) despesaSelecionada.comprovante_pagamento = arquivo.name;

    const nome = despesaSelecionada.nome;

    fecharModal();
    atualizarResumo();
    renderizarTabela();
    mostrarToast(`Pagamento de "${nome}" confirmado (${formatarValor(valorPago)}).`);
}

// --------------------------------------------------------------------------
// Eventos
// --------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    carregarDespesas();

    const form = document.getElementById("formFiltroConciliacaoDespesa");
    form.addEventListener("submit", (e) => {
        e.preventDefault();
        renderizarTabela();
    });

    document.getElementById("limparFiltros").addEventListener("click", () => {
        form.reset();
        document.getElementById("status").value = "";
        renderizarTabela();
    });

    document.getElementById("valor_pago").addEventListener("input", verificarDiferenca);
    document.getElementById("comprovante_pagamento").addEventListener("change", verificarAnexo);

    document.getElementById("btnFecharModalConciliacao").addEventListener("click", fecharModal);
    document.getElementById("btnCancelarConciliacao").addEventListener("click", fecharModal);
    document.getElementById("btnConfirmarPagamento").addEventListener("click", confirmarPagamento);

    modal.addEventListener("click", (e) => {
        if (e.target === modal) fecharModal();
    });
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape" && !modal.classList.contains("oculta")) fecharModal();
    });
});
