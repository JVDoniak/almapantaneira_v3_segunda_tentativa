// ==========================================================================
// DASHBOARD - Alma Pantaneira
// --------------------------------------------------------------------------
// PROTÓTIPO: os dados abaixo são fictícios.
// Na etapa do back-end, a função buscarDados(filtros) passa a fazer:
//
//   const params = new URLSearchParams(filtros);
//   const resposta = await fetch('../../actions/dashboard.php?' + params);
//   return await resposta.json();
//
// e o PHP devolve um JSON neste formato (o resto do arquivo já usa ele):
//
// {
//   "resumo": { "receitas": 0, "despesas": 0, "saldo": 0,
//               "receitas_anterior": 0, "despesas_anterior": 0,
//               "pendentes_qtd": 0, "pendentes_valor": 0 },
//   "mensal": [ { "mes": "2026-10", "receitas": 0, "despesas": 0 } ],
//   "despesas_categoria": [ { "categoria": "Medicamentos", "total": 0 } ],
//   "receitas_projeto":   [ { "projeto": "Expansão Médica", "total": 0 } ],
//   "ultimas_doacoes":    [ { "data_doacao": "2026-10-16", "nome_doador": "",
//                             "projeto": "", "forma_pagamento": "",
//                             "valor": 0, "status": "pendente|confirmada" } ]
// }
// ==========================================================================

// Cores dos gráficos (validadas para daltonismo e contraste).
// Verde = receitas, azul = despesas, em todos os gráficos da tela.
const COR_RECEITAS = "#1f7a3f";
const COR_DESPESAS = "#e67e22";
const COR_TEXTO    = "#333333";
const COR_TEXTO_2  = "#666666";
const COR_GRADE    = "#eeeeee";

// --------------------------------------------------------------------------
// Dados fictícios (12 meses: nov/2025 a out/2026)
// --------------------------------------------------------------------------
const MOCK_MESES = ["2025-11", "2025-12", "2026-01", "2026-02", "2026-03", "2026-04",
                    "2026-05", "2026-06", "2026-07", "2026-08", "2026-09", "2026-10"];

const MOCK_RECEITAS_PROJETO = {
    "Expansão Médica":    [4200, 6800, 3900, 4100, 4600, 5200, 4800, 5100, 6200, 5400, 5900, 6400],
    "Saúde Comunitária":  [2600, 3900, 2200, 2500, 2700, 3100, 2900, 3300, 3600, 3200, 3500, 3800],
    "Unidade Móvel":      [1200, 2500,  900, 1100, 1400, 1600, 1500, 1800, 2100, 1900, 2000, 2300],
    "Educação em Saúde":  [ 500,  900,  400,  600,  700,  800,  700,  900, 1000,  800,  900, 1100],
};

const MOCK_DESPESAS_CATEGORIA = {
    "Medicamentos":          [2100, 2400, 2000, 2200, 2300, 2500, 2400, 2600, 2700, 2500, 2800, 2900],
    "Salários":              [2800, 4200, 2800, 2800, 2800, 2800, 2800, 2800, 2900, 2900, 2900, 2900],
    "Transporte":            [ 900, 1100,  800,  850,  950, 1000,  980, 1050, 1200, 1100, 1150, 1250],
    "Manutenção":            [ 400,  300,  650,  300,  500,  350,  700,  400,  450,  600,  380,  520],
    "Alimentação":           [ 600,  900,  550,  600,  650,  700,  680,  720,  800,  750,  780,  820],
    "Material de Escritório":[ 150,  120,  200,  130,  160,  140,  180,  150,  170,  160,  150,  190],
};

const MOCK_DOACOES = [
    { data_doacao: "2026-10-16", nome_doador: "Maria Silva Santos",     projeto: "Expansão Médica",   forma_pagamento: "PIX",                    valor: 250,  status: "pendente" },
    { data_doacao: "2026-10-15", nome_doador: "João Pereira Lima",      projeto: "Saúde Comunitária", forma_pagamento: "Transferência Bancária", valor: 1500, status: "pendente" },
    { data_doacao: "2026-10-14", nome_doador: "Mercado Bom Preço LTDA", projeto: "Saúde Comunitária", forma_pagamento: "Dinheiro",               valor: 800,  status: "pendente" },
    { data_doacao: "2026-10-12", nome_doador: "Ana Beatriz Costa",      projeto: "Expansão Médica",   forma_pagamento: "Cartão",                 valor: 100,  status: "confirmada" },
    { data_doacao: "2026-10-10", nome_doador: "Carlos Eduardo Rocha",   projeto: "Unidade Móvel",     forma_pagamento: "PIX",                    valor: 50,   status: "pendente" },
    { data_doacao: "2026-10-08", nome_doador: "Farmácia Pantanal",      projeto: "Saúde Comunitária", forma_pagamento: "Transferência Bancária", valor: 3200, status: "confirmada" },
    { data_doacao: "2026-10-05", nome_doador: "Luciana Ferreira",       projeto: "Educação em Saúde", forma_pagamento: "PIX",                    valor: 75,   status: "confirmada" },
    { data_doacao: "2026-09-28", nome_doador: "Rotary Club Corumbá",    projeto: "Unidade Móvel",     forma_pagamento: "Transferência Bancária", valor: 2000, status: "confirmada" },
];

// Simula a resposta do back-end para os filtros escolhidos
function montarMock(filtros) {
    const indices = indicesDoPeriodo(filtros);
    const anteriores = indices.map((i) => i - indices.length).filter((i) => i >= 0);

    const projetos = filtros.projeto ? [filtros.projeto] : Object.keys(MOCK_RECEITAS_PROJETO);
    const somaReceitas = (i) => projetos.reduce((t, p) => t + MOCK_RECEITAS_PROJETO[p][i], 0);

    // No protótipo, com um projeto selecionado as despesas são proporcionais
    // à participação dele nas receitas. No back-end virá o valor real.
    const totalGeralReceitas = (i) => Object.values(MOCK_RECEITAS_PROJETO).reduce((t, v) => t + v[i], 0);
    const fatorDespesa = (i) => filtros.projeto ? somaReceitas(i) / totalGeralReceitas(i) : 1;
    const somaDespesas = (i) =>
        Object.values(MOCK_DESPESAS_CATEGORIA).reduce((t, v) => t + Math.round(v[i] * fatorDespesa(i)), 0);

    const somar = (lista, fn) => lista.reduce((t, i) => t + fn(i), 0);

    const doacoesFiltradas = MOCK_DOACOES.filter((d) => !filtros.projeto || d.projeto === filtros.projeto);
    const pendentes = doacoesFiltradas.filter((d) => d.status === "pendente");

    const receitas = somar(indices, somaReceitas);
    const despesas = somar(indices, somaDespesas);

    return {
        resumo: {
            receitas,
            despesas,
            saldo: receitas - despesas,
            receitas_anterior: anteriores.length === indices.length ? somar(anteriores, somaReceitas) : null,
            despesas_anterior: anteriores.length === indices.length ? somar(anteriores, somaDespesas) : null,
            pendentes_qtd: pendentes.length,
            pendentes_valor: pendentes.reduce((t, d) => t + d.valor, 0),
        },
        mensal: indices.map((i) => ({
            mes: MOCK_MESES[i],
            receitas: somaReceitas(i),
            despesas: somaDespesas(i),
        })),
        despesas_categoria: Object.entries(MOCK_DESPESAS_CATEGORIA).map(([categoria, valores]) => ({
            categoria,
            total: somar(indices, (i) => Math.round(valores[i] * fatorDespesa(i))),
        })),
        receitas_projeto: projetos.map((projeto) => ({
            projeto,
            total: somar(indices, (i) => MOCK_RECEITAS_PROJETO[projeto][i]),
        })),
        ultimas_doacoes: doacoesFiltradas.slice(0, 5),
    };
}

function indicesDoPeriodo(filtros) {
    const quantidade = { mes: 1, "3_meses": 3, "6_meses": 6, ano: 12 }[filtros.periodo];

    if (quantidade) {
        return MOCK_MESES.map((_, i) => i).slice(-quantidade);
    }

    // Personalizado: meses entre as datas escolhidas
    const inicio = (filtros.data_inicio || MOCK_MESES[0]).slice(0, 7);
    const fim    = (filtros.data_fim || MOCK_MESES[MOCK_MESES.length - 1]).slice(0, 7);
    const lista  = MOCK_MESES.map((m, i) => (m >= inicio && m <= fim ? i : -1)).filter((i) => i >= 0);
    return lista.length ? lista : [MOCK_MESES.length - 1];
}

async function buscarDados(filtros) {
    // PROTÓTIPO: dados fictícios. Back-end: ver o comentário no topo do arquivo.
    return montarMock(filtros);
}

// --------------------------------------------------------------------------
// Formatação
// --------------------------------------------------------------------------
function formatarValor(valor) {
    return (Number(valor) || 0).toLocaleString("pt-BR", { style: "currency", currency: "BRL" });
}

function formatarValorCurto(valor) {
    const numero = Number(valor) || 0;
    if (Math.abs(numero) >= 1000) {
        return "R$ " + (numero / 1000).toLocaleString("pt-BR", { maximumFractionDigits: 1 }) + " mil";
    }
    return formatarValor(numero);
}

function formatarData(data) {
    if (!data) return "-";
    const [ano, mes, dia] = data.split("-");
    return `${dia}/${mes}/${ano}`;
}

function formatarMes(anoMes) {
    const nomes = ["jan", "fev", "mar", "abr", "mai", "jun", "jul", "ago", "set", "out", "nov", "dez"];
    const [ano, mes] = anoMes.split("-");
    return `${nomes[Number(mes) - 1]}/${ano.slice(2)}`;
}

function textoVariacao(atual, anterior) {
    if (anterior === null || anterior === undefined || anterior === 0) {
        return "Sem período anterior para comparar";
    }
    const pct = ((atual - anterior) / anterior) * 100;
    const seta = pct >= 0 ? "▲" : "▼";
    return `${seta} ${Math.abs(pct).toLocaleString("pt-BR", { maximumFractionDigits: 1 })}% vs período anterior`;
}

// --------------------------------------------------------------------------
// Cards
// --------------------------------------------------------------------------
function atualizarCards(resumo) {
    document.getElementById("cardReceitas").textContent = formatarValor(resumo.receitas);
    document.getElementById("cardDespesas").textContent = formatarValor(resumo.despesas);
    document.getElementById("cardSaldo").textContent    = formatarValor(resumo.saldo);
    document.getElementById("cardPendentes").textContent =
        `${resumo.pendentes_qtd} ${resumo.pendentes_qtd === 1 ? "doação" : "doações"}`;
    document.getElementById("cardPendentesValor").innerHTML =
        `${formatarValor(resumo.pendentes_valor)} · Conciliar <i class="bi bi-arrow-right"></i>`;

    document.getElementById("varReceitas").textContent = textoVariacao(resumo.receitas, resumo.receitas_anterior);
    document.getElementById("varDespesas").textContent = textoVariacao(resumo.despesas, resumo.despesas_anterior);
}

// --------------------------------------------------------------------------
// Gráficos (Chart.js)
// --------------------------------------------------------------------------
let graficoMensal = null;
let graficoCategorias = null;
let graficoProjetos = null;

const telaEstreita = () => window.innerWidth < 600;

// Escreve o valor e a porcentagem na ponta de cada barra horizontal
// (em tela estreita só o valor; a porcentagem continua no tooltip)
const pluginRotuloValor = {
    id: "rotuloValor",
    afterDatasetsDraw(chart) {
        const dados = chart.data.datasets[0].data;
        const total = dados.reduce((t, v) => t + v, 0);
        const { ctx } = chart;

        ctx.save();
        ctx.font = "600 12px Arial, Helvetica, sans-serif";
        ctx.fillStyle = COR_TEXTO;
        ctx.textBaseline = "middle";

        chart.getDatasetMeta(0).data.forEach((barra, i) => {
            const pct = total ? Math.round((dados[i] / total) * 100) : 0;
            const texto = telaEstreita()
                ? formatarValorCurto(dados[i])
                : `${formatarValorCurto(dados[i])} (${pct}%)`;
            ctx.fillText(texto, barra.x + 8, barra.y);
        });

        ctx.restore();
    },
};

function configurarChartJs() {
    Chart.defaults.font.family = "Arial, Helvetica, sans-serif";
    Chart.defaults.font.size = 12;
    Chart.defaults.color = COR_TEXTO_2;
    Chart.defaults.plugins.tooltip.backgroundColor = "#191919";
    Chart.defaults.plugins.tooltip.padding = 10;
    Chart.defaults.plugins.tooltip.boxPadding = 4;
    Chart.defaults.plugins.tooltip.usePointStyle = true;
}

// Mantém Receitas e Despesas lado a lado em cada mês, com 4px entre elas,
// qualquer que seja a quantidade de meses ou a largura da tela.
const LARGURA_BARRA_MENSAL = 32;
function ajustarAgrupamento(chart) {
    const meses = chart.data.labels.length || 1;
    const larguraArea = chart.chartArea ? chart.chartArea.width : chart.width;
    const larguraMes = larguraArea / meses;
    const larguraGrupo = 2 * LARGURA_BARRA_MENSAL + 4;
    chart.options.datasets.bar.categoryPercentage = Math.min(0.7, larguraGrupo / larguraMes);
}

function criarGraficoMensal() {
    graficoMensal = new Chart(document.getElementById("graficoMensal"), {
        type: "bar",
        data: {
            labels: [],
            datasets: [
                { label: "Receitas", data: [], backgroundColor: COR_RECEITAS, borderRadius: 4, maxBarThickness: LARGURA_BARRA_MENSAL },
                { label: "Despesas", data: [], backgroundColor: COR_DESPESAS, borderRadius: 4, maxBarThickness: LARGURA_BARRA_MENSAL },
            ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            interaction: { mode: "index", intersect: false },
            datasets: { bar: { categoryPercentage: 0.5, barPercentage: 0.94 } },
            onResize: (chart) => { ajustarAgrupamento(chart); chart.update("none"); },
            plugins: {
                legend: {
                    position: "top",
                    align: "start",
                    labels: { usePointStyle: true, pointStyle: "rectRounded", color: COR_TEXTO, padding: 16 },
                },
                tooltip: {
                    callbacks: {
                        label: (item) => ` ${item.dataset.label}: ${formatarValor(item.parsed.y)}`,
                        footer: (itens) => {
                            const receitas = itens.find((i) => i.datasetIndex === 0)?.parsed.y || 0;
                            const despesas = itens.find((i) => i.datasetIndex === 1)?.parsed.y || 0;
                            return `Saldo: ${formatarValor(receitas - despesas)}`;
                        },
                    },
                },
            },
            scales: {
                x: { grid: { display: false }, border: { color: COR_GRADE }, ticks: { color: COR_TEXTO_2 } },
                y: {
                    beginAtZero: true,
                    grid: { color: COR_GRADE },
                    border: { display: false },
                    ticks: { color: COR_TEXTO_2, maxTicksLimit: 6, callback: (v) => formatarValorCurto(v) },
                },
            },
        },
    });
}

function criarGraficoHorizontal(idCanvas, cor, nomeSerie) {
    return new Chart(document.getElementById(idCanvas), {
        type: "bar",
        data: {
            labels: [],
            datasets: [{ label: nomeSerie, data: [], backgroundColor: cor, borderRadius: 4, maxBarThickness: 22 }],
        },
        plugins: [pluginRotuloValor],
        options: {
            indexAxis: "y",
            responsive: true,
            maintainAspectRatio: false,
            layout: { padding: () => ({ right: telaEstreita() ? 76 : 110 }) },
            plugins: {
                legend: { display: false },
                tooltip: {
                    callbacks: {
                        label: (item) => {
                            const total = item.dataset.data.reduce((t, v) => t + v, 0);
                            const pct = total ? ((item.parsed.x / total) * 100).toFixed(1).replace(".", ",") : 0;
                            return ` ${formatarValor(item.parsed.x)} (${pct}% do total)`;
                        },
                    },
                },
            },
            scales: {
                x: { display: false, beginAtZero: true, grid: { display: false } },
                y: {
                    grid: { display: false },
                    border: { display: false },
                    ticks: {
                        color: COR_TEXTO,
                        // Em telas estreitas, encurta nomes longos (o nome completo aparece no tooltip)
                        callback: function (valor) {
                            const nome = this.getLabelForValue(valor);
                            const limite = telaEstreita() ? 14 : 30;
                            return nome.length > limite ? nome.slice(0, limite - 1) + "…" : nome;
                        },
                    },
                },
            },
        },
    });
}

// Ajusta a altura do gráfico horizontal pela quantidade de barras
function ajustarAltura(idArea, quantidade) {
    document.getElementById(idArea).style.height = `${Math.max(quantidade * 44 + 20, 140)}px`;
}

function atualizarGraficos(dados) {
    // Receitas x Despesas por mês
    graficoMensal.data.labels = dados.mensal.map((m) => formatarMes(m.mes));
    graficoMensal.data.datasets[0].data = dados.mensal.map((m) => m.receitas);
    graficoMensal.data.datasets[1].data = dados.mensal.map((m) => m.despesas);
    ajustarAgrupamento(graficoMensal);
    graficoMensal.update();

    // Despesas por categoria (maior para o menor)
    const categorias = [...dados.despesas_categoria].sort((a, b) => b.total - a.total);
    ajustarAltura("areaCategorias", categorias.length);
    graficoCategorias.data.labels = categorias.map((c) => c.categoria);
    graficoCategorias.data.datasets[0].data = categorias.map((c) => c.total);
    graficoCategorias.update();
    document.getElementById("totalCategorias").textContent =
        "Total: " + formatarValor(categorias.reduce((t, c) => t + c.total, 0));

    // Receitas por projeto (maior para o menor)
    const projetos = [...dados.receitas_projeto].sort((a, b) => b.total - a.total);
    ajustarAltura("areaProjetos", projetos.length);
    graficoProjetos.data.labels = projetos.map((p) => p.projeto);
    graficoProjetos.data.datasets[0].data = projetos.map((p) => p.total);
    graficoProjetos.update();
    document.getElementById("totalProjetos").textContent =
        "Total: " + formatarValor(projetos.reduce((t, p) => t + p.total, 0));
}

// --------------------------------------------------------------------------
// Filtros
// --------------------------------------------------------------------------
function lerFiltros() {
    return {
        periodo:     document.getElementById("periodo").value,
        data_inicio: document.getElementById("data_inicio").value,
        data_fim:    document.getElementById("data_fim").value,
        projeto:     document.getElementById("projeto").value,
    };
}

function preencherProjetos() {
    // Back-end: as opções virão do banco (lista de projetos)
    const select = document.getElementById("projeto");
    Object.keys(MOCK_RECEITAS_PROJETO).forEach((nome) => {
        const option = document.createElement("option");
        option.value = nome;
        option.textContent = nome;
        select.appendChild(option);
    });
}

function atualizarPeriodoExibido(mensal) {
    const texto = document.getElementById("periodoExibido");
    if (!mensal.length) { texto.textContent = ""; return; }
    const inicio = formatarMes(mensal[0].mes);
    const fim = formatarMes(mensal[mensal.length - 1].mes);
    texto.innerHTML = `<i class="bi bi-calendar3"></i> ${inicio === fim ? inicio : inicio + " a " + fim}`;
}

async function carregarDashboard() {
    try {
        const dados = await buscarDados(lerFiltros());
        atualizarPeriodoExibido(dados.mensal);
        atualizarCards(dados.resumo);
        if (graficoMensal) atualizarGraficos(dados);
    } catch (erro) {
        console.error("Erro ao carregar o dashboard", erro);
    }
}

// --------------------------------------------------------------------------
// Início
// --------------------------------------------------------------------------
document.addEventListener("DOMContentLoaded", () => {
    preencherProjetos();

    if (typeof Chart !== "undefined") {
        configurarChartJs();
        criarGraficoMensal();
        graficoCategorias = criarGraficoHorizontal("graficoCategorias", COR_DESPESAS, "Despesas");
        graficoProjetos   = criarGraficoHorizontal("graficoProjetos", COR_RECEITAS, "Receitas");
    } else {
        document.querySelectorAll(".grafico-area").forEach((area) => {
            area.innerHTML = `<p class="grafico-erro">Não foi possível carregar os gráficos.</p>`;
        });
    }

    // Datas só ficam ativas no período personalizado
    const periodo = document.getElementById("periodo");
    periodo.addEventListener("change", () => {
        const personalizado = periodo.value === "personalizado";
        document.getElementById("data_inicio").disabled = !personalizado;
        document.getElementById("data_fim").disabled = !personalizado;
    });

    document.getElementById("formFiltroDashboard").addEventListener("submit", (e) => {
        e.preventDefault();
        carregarDashboard();
    });

    carregarDashboard();
});