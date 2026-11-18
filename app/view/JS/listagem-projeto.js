async function listagemProjeto() {

    const nomeInput = document.getElementById("nome_projeto");
    const dataInput = document.getElementById("data_inicio");
    const tipoSelect = document.getElementById("tipo_projeto");
    const statusSelect = document.getElementById("status");
    const formFilter = document.getElementById("listar-projetos");
    const bodyTable = document.getElementById("bodyTable");

    function escapeHtml(texto) {
        const div = document.createElement("div");
        div.textContent = texto ?? "";
        return div.innerHTML;
    }

    function formatarData(data) {
        if (!data) return "";
        const [ano, mes, dia] = data.split("-");
        return `${dia}/${mes}/${ano}`;
    }

    function valorSelect(select) {
        return (select && select.value !== "selecione") ? select.value : "";
    }

    async function buscarDados() {
        const nome = nomeInput ? nomeInput.value.trim() : "";
        const dataInicio = dataInput ? dataInput.value : "";
        const tipo = valorSelect(tipoSelect);
        const status = valorSelect(statusSelect);

        if (nome.length > 0 && nome.length < 3 && !dataInicio && !tipo && !status) {
            return;
        }

        try {
            const params = new URLSearchParams({
                nome_projeto: nome,
                data_inicio: dataInicio,
                tipo_projeto: tipo,
                status: status
            });

            const resposta = await fetch(`../../actions/listagem-projeto.php?${params.toString()}`);
            if (!resposta.ok) throw new Error("Erro na resposta do servidor");

            const projetos = await resposta.json();
            renderizarTabela(projetos);
        } catch (error) {
            console.error("Erro ao Carregar lista", error);
            bodyTable.innerHTML = '<tr><td colspan="6" style="text-align:center; color:red;">Erro ao carregar projetos.</td></tr>';
        }
    }

    function renderizarTabela(projetos) {
        bodyTable.innerHTML = "";

        if (projetos.length === 0) {
            bodyTable.innerHTML = '<tr><td colspan="7" style="text-align:center;">Nenhum projeto encontrado.</td></tr>';
            return;
        }

        projetos.forEach((projeto) => {
            const tr = document.createElement("tr");

            if (!projeto.status_projeto || projeto.status_projeto == 0) {
                tr.classList.add("linha-inativa");
            }

            tr.innerHTML = `
                <td nowrap>${projeto.nome ?? ""}</td>
                <td nowrap>${projeto.tipo_projeto ?? ""}</td>
                <td nowrap>${formatarData(projeto.data_inicio) ?? ""}</td>
                <td nowrap>${projeto.responsavel ?? ""}</td>
                <td nowrap>${projeto.local_projeto ?? ""}</td>


                <td>
                <div class="table-actions-cell">
                    <a href="edicao-projeto.php?id_projeto=${projeto.id_projeto}">
                    <i class="bi bi-pencil-square" title="Editar"></i>
                    </a>
                    <label class="switch-component">
                    <input type="checkbox" class="status-toggle" ${projeto.status_projeto && projeto.status_projeto != 0 ? "checked" : ""}>
                    <span class="switch-slider"></span>
                    </label>
                </div>
                </td>
            `;
            function formatarData(data) {
            if (!data) return "-";

            const [ano, mes, dia] = data.split("-");
            return `${dia}/${mes}/${ano}`;
            }

            const checkbox = tr.querySelector(".status-toggle");

            checkbox.addEventListener("change", function () {
                const novoStatus = this.checked;
                const acao = novoStatus ? "ativar" : "desativar";
                
                const desejaContinuar = confirm(`Deseja ${acao} este projeto?`);

                if (!confirm(`Deseja realmente ${acao} este projeto?`)) {
                    this.checked = !novoStatus;
                    return;
                }

                fetch("../../actions/atualizar-status-projeto.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/x-www-form-urlencoded" },
                    body: `id_projeto=${projeto.id_projeto}&status=${novoStatus ? 1 : 0}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        tr.classList.toggle("linha-inativa", !novoStatus);
                    } else {
                        alert("Não foi possível atualizar o projeto: " + data.mensagem);
                        this.checked = !novoStatus;
                    }
                })
                .catch(error => {
                    console.error("Erro detalhado:", error);
                    alert("Erro de comunicação: " + error.message);
                    this.checked = !novoStatus;
                });
            });

            bodyTable.appendChild(tr);
        });
    }

    if (nomeInput) {
        nomeInput.addEventListener("input", () => {
            if (nomeInput.value.length >= 3 || nomeInput.value.length === 0) {
                buscarDados();
            }
        });
    }

    [dataInput, tipoSelect, statusSelect].forEach((campo) => {
        if (campo) campo.addEventListener("change", buscarDados);
    });

    if (formFilter) {
        formFilter.addEventListener("submit", (e) => {
            e.preventDefault();
            buscarDados();
        });
    }

    buscarDados();
}

window.addEventListener("DOMContentLoaded", listagemProjeto);