// Carrega as opções dos filtros (categorias, formas de pagamento e projetos do banco)
async function carregarOpcoesFiltro() {
    try {
        const resposta = await fetch('../../actions/listagem-despesa.php?opcoes=1');
        const opcoes = await resposta.json();

        console.log(opcoes);

        preencherSelect("id_tipo_despesa", opcoes.tipos || [], "id_tipo_despesa", "descricao");
        preencherSelect("id_categoria_despesa", opcoes.categorias || [], "id_categoria_despesa", "descricao");
        preencherSelect("id_projeto", opcoes.projetos || [], "id_projeto", "nome");

        const formas = (opcoes.formas_pagamento || []).map(forma => ({ valor: forma, texto: nomeFormaPagamento(forma) }));
        preencherSelect("forma_pagamento", formas, "valor", "texto");

    } catch (error) {
        console.error("Erro ao carregar opções dos filtros:", error);
    }
}

function preencherSelect(idSelect, itens, chaveValor, chaveTexto) {
    const select = document.getElementById(idSelect);
    if (!select) return;

    itens.forEach(item => {
        const option = document.createElement("option");
        option.value = item[chaveValor];
        option.innerText = item[chaveTexto];
        select.appendChild(option);
    });
}

async function listagemDespesa(filtros = {}){
    const bodyTable = document.getElementById("bodyTable");

    try {
        // Envia só os filtros preenchidos
        const params = new URLSearchParams();
        Object.entries(filtros).forEach(([chave, valor]) => {
            if (valor !== null && String(valor).trim() !== "") {
                params.append(chave, String(valor).trim());
            }
        });

        const resposta = await fetch(`../../actions/listagem-despesa.php?${params.toString()}`);
        const despesas = await resposta.json();

        console.log(despesas);

        bodyTable.innerHTML = "";

        if(!Array.isArray(despesas) || despesas.length === 0){
            bodyTable.innerHTML = '<tr><td colspan="8">Nenhuma despesa encontrada.</td></tr>';
            return;
        }

        despesas.forEach((despesa) => {
        const tr = document.createElement('tr');

        if (!despesa.status_despesa || despesa.status_despesa == 0) {
            tr.classList.add("linha-inativa");
        }

        tr.innerHTML = `
        <td>${escaparHtml(despesa.nome_despesa)}</td>
        <td>${escaparHtml(despesa.nome_tipo) || "Não classificado"}</td>
        <td>${escaparHtml(despesa.nome_categoria)}</td>
        <td>${escaparHtml(despesa.nome_projeto) || "-"}</td>
        <td>${escaparHtml(nomeFormaPagamento(despesa.forma_pagamento))}</td>
        <td>${formatarData(despesa.data_vencimento ?? "")}</td>
        <td>${formatarValor(despesa.valor ?? "")}</td>
        <td>
            <div class="table-actions-cell">
                <i class="bi bi-eye" title="Visualizar"></i>
                <a href="edicao-despesa.php?id_despesa=${despesa.id_despesa}">
                    <i class="bi bi-pencil-square" title="Editar"></i>
                </a>
                <label class="switch-component">
                    <input type="checkbox" class="status-toggle" ${despesa.status_despesa && despesa.status_despesa != 0 ? "checked" : ""}>
                    <span class="switch-slider"></span>
                </label>
            </div>
        </td>
        `;

        const checkbox = tr.querySelector(".status-toggle");

            checkbox.addEventListener("change", function () {
                const novoStatus = this.checked; 
                const acao = novoStatus ? "ativar" : "desativar";
            

            const desejaContinuar = confirm(`Deseja ${acao} esta despesa?`);

            if (desejaContinuar) {
                fetch("../../actions/atualizar-status-despesa.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `id_despesa=${despesa.id_despesa}&status=${novoStatus ? 1 : 0}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        if (novoStatus) {
                            tr.classList.remove("linha-inativa");
                        } else {
                            tr.classList.add("linha-inativa");
                        }
                    } else {
                        alert("Não foi possível atualizar a despesa: " + data.mensagem);
                        this.checked = !novoStatus;
                    }
                })
                .catch(error => {
                    console.error("Erro detalhado:", error);
                    alert("Erro de comunicação: " + error.message);
                    this.checked = !novoStatus;
                });
            } else {
                this.checked = !novoStatus;
            }
        });

            bodyTable.appendChild(tr);
        });


    } catch (error){
        console.error("Erro ao carregar lista:", error);
        bodyTable.innerHTML = '<tr><td colspan="8">Erro ao carregar despesas.</td></tr>';
    }
}

function nomeFormaPagamento(forma) {
    const nomes = { pix: "PIX", cartao: "Cartão", boleto: "Boleto" };
    return nomes[String(forma || "").toLowerCase()] || (forma ?? "");
}

function formatarData(data) {
    if (!data) return "";

    const [ano, mes, dia] = String(data).substring(0, 10).split("-");
    return `${dia}/${mes}/${ano}`;
}

function formatarValor(valor) {
    if (!valor) return "R$ 0,00";

    return parseFloat(valor).toLocaleString("pt-BR", {
        style: "currency",
        currency: "BRL"
    });
}

function escaparHtml(texto) {
    return String(texto ?? "")
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;")
        .replace(/"/g, "&quot;");
}

window.addEventListener("DOMContentLoaded", function () {
    carregarOpcoesFiltro();
    listagemDespesa();

    const formFiltro = document.getElementById("formFiltroDespesa");

    if (formFiltro) {
        formFiltro.addEventListener("submit", function (event) {
            event.preventDefault();

            const filtros = Object.fromEntries(new FormData(formFiltro));

            if (filtros.data_inicio && filtros.data_fim && filtros.data_inicio > filtros.data_fim) {
                alert("A data de início não pode ser maior que a data de fim.");
                return;
            }

            listagemDespesa(filtros);
        });
    }

    const btnLimpar = document.getElementById("limparFiltros");

    if (btnLimpar && formFiltro) {
        btnLimpar.addEventListener("click", function () {
            formFiltro.reset();
            listagemDespesa();
        });
    }
});