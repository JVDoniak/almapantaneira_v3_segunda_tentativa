async function listagemDoacao() {
    try {
        const resposta = await fetch('../../actions/listagem-doacao.php');
        const doacao = await resposta.json();

        console.log(doacao);

        const bodyTable = document.getElementById("bodyTable");

        if (doacao.length === 0) {
            bodyTable.innerHTML = "<p>Nenhuma doação encontrada.</p>";
            return;
        }
        
        doacao.forEach((doacao) => {
        const tr = document.createElement('tr');

        if (!doacao.status_doacao || doacao.status_doacao == 0) {
            tr.classList.add("linha-inativa");
        }
        console.log(doacao)

        tr.innerHTML = `
        <td>${doacao.nome_doador}</td>
        <td>${doacao.tipo}</td>
        <td>${formatarData(doacao.data_doacao)}</td> 
        <td>${doacao.projeto || "Sem projeto"}</td>
        <td>${formatarValor(doacao.valor)}</td>
        <td>
            <div class="table-actions-cell">
                <i class="bi bi-eye" title="Visualizar"></i>
                <a href="edicao-doacao.php?id=${doacao.id_doacao}">
                    <i class="bi bi-pencil-square" title="Editar"></i>
                </a>
                <label class="switch-component">
                    <input type="checkbox" class="status-toggle" ${doacao.status_doacao && doacao.status_doacao != 0 ? "checked" : ""}>
                    <span class="switch-slider"></span>
                </label>
            </div>
        </td>
        `;
                
    const checkbox = tr.querySelector(".status-toggle");

        checkbox.addEventListener("change", function () {
            const novoStatus = this.checked; 
            const acao = novoStatus ? "ativar" : "desativar";
            
            const desejaContinuar = confirm(`Deseja ${acao} esta doação?`);

            if (desejaContinuar) {
                fetch("../../actions/atualizar-status-doacao.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `id_doacao=${doacao.id_doacao}&status=${novoStatus ? 1 : 0}`
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
                        alert("Não foi possível atualizar a doação: " + data.mensagem);
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

    } catch (error) {
        console.error('Erro ao Carregar lista', error);
        document.getElementById('bodyTable').innerHTML =
        '<p>Não foi possível carregar as doações.</p>';
    }

    function formatarData(data) {
        if (!data) return "-";

        const [ano, mes, dia] = data.split("-");
        return `${dia}/${mes}/${ano}`;
    }

    function formatarValor(valor) {
        if (!valor) return "R$ 0,00";

        const numero = parseFloat(valor);

        return numero.toLocaleString("pt-BR", {
            style: "currency",
            currency: "BRL"
        });
    }
}

document.addEventListener("DOMContentLoaded", listagemDoacao);
