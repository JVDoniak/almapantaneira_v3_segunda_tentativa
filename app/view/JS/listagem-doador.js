document.addEventListener("DOMContentLoaded", function() {
    const nomeInput = document.getElementById("nome");
    const cpfInput = document.getElementById("cpf_cnpj");
    const tipoSelect = document.getElementById("tipo");
    const statusSelect = document.getElementById("status_doador");
    const formFilter = document.querySelector(".filter-card form");
    const bodyTable = document.getElementById("bodyTable");

    async function buscarDados() {
        const nome = nomeInput ? nomeInput.value : "";
        const cpf = cpfInput ? cpfInput.value.replace(/\D/g, "") : "";
        const tipo = (tipoSelect && tipoSelect.value !== "selecione") ? tipoSelect.value : "";
        const status = (statusSelect && statusSelect.value !== "selecione") ? statusSelect.value : "";

        try {
            const params = new URLSearchParams({
                nome: nome,
                cpf: cpf,
                tipo: tipo,
                status: status
            });

            const resposta = await fetch(`../../actions/listagem-doador.php?${params.toString()}`);
            if (!resposta.ok) throw new Error("Erro na resposta do servidor");

            const doadores = await resposta.json();
            renderizarTabela(doadores);
        } catch (error) {
            console.error('Erro ao Carregar lista', error);
            if (bodyTable) {
                bodyTable.innerHTML = '<tr><td colspan="6" style="text-align:center; color:red;">Erro ao carregar doadores.</td></tr>';
            }
        }
    }

    function renderizarTabela(doadores) {
        if (!bodyTable) return;
        bodyTable.innerHTML = "";

        if (!doadores || doadores.length === 0) {
            bodyTable.innerHTML = `<tr><td colspan="6" style="text-align: center;">Nenhum doador encontrado.</td></tr>`;
            return;
        }

        doadores.forEach((doador) => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${(doador.tipo ?? "")}</td>
                <td>${(doador.nome != "" ? doador.nome : doador.razao_social)}</td>
                <td>${formatarCpfCnpj(doador.cpf_cnpj ?? "")}</td>
                <td>${formatarTelefone(doador.telefone ?? "")}</td>
                <td>${(doador.email ?? "")}</td>
                <td>
                    <div class="table-actions-cell">
                        <i class="bi bi-eye" title="Visualizar"></i>
                        <a href="edicao-doador.php?id_doador=${doador.id_doador}">
                            <i class="bi bi-pencil-square" title="Editar"></i>
                        </a>
                        <label class="switch-component">
                            <input type="checkbox" class="status-toggle" ${doador.status_doador && doador.status_doador != 0 ? "checked" : ""}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </td>
            `;

    const checkbox = tr.querySelector(".status-toggle");

        checkbox.addEventListener("change", function () {
            const novoStatus = this.checked; 
            const acao = novoStatus ? "ativar" : "desativar";
            
            const desejaContinuar = confirm(`Deseja ${acao} este doador?`);

            if (desejaContinuar) {
                fetch("../../actions/atualizar-status-doador.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `id_doador=${doador.id_doador}&status=${novoStatus ? 1 : 0}`
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
                        alert("Não foi possível atualizar o doador: " + data.mensagem);
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
    }

    // Carrega a tabela inicialmente ao abrir a página
    buscarDados();

    // Intercepta o botão de filtrar para atualizar a tabela sem refresh
    if (formFilter) {
        formFilter.addEventListener("submit", function(e) {
            e.preventDefault();
            buscarDados();
        });
    }
});

function formatarCpfCnpj(valor) {
    if (!valor) return "";
    
    const limpo = String(valor).replace(/\D/g, "");
    
    if (limpo.length <= 11) {
        return limpo
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')                         
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        return limpo
            .replace(/^(\d{2})(\d)/, '$1.$2')
            .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
            .replace(/\.(\d{3})(\d)/, '.$1/$2')            
            .replace(/(\d{4})(\d)/, '$1-$2')
            .substring(0, 18); 
    }
}
    
function formatarTelefone(valor) {
    if (!valor) return "";
    const limpo = String(valor).replace(/\D/g, "");
    
    if (limpo.length === 11) {
        return limpo.replace(/^(\d{2})(\d{5})(\d+)/, '($1) $2-$3');
    } else if (limpo.length === 10) {
        return limpo.replace(/^(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
    }
    
    return valor;
}