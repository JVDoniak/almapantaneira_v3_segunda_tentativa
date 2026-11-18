async function listagemFornecedor() {
    
    const nomeInput = document.getElementById("nome_fornecedor");
    const cnpjInput = document.getElementById("cpf_cnpj");
    const statusSelect = document.getElementById("status");
    const formFilter = document.getElementById("listar-fornecedores");
    const bodyTable = document.getElementById("bodyTable");

    // BUSCA POR NOME, CNPJ E STATUS FORNECEDOR
    async function buscarDados() {
        const nome = nomeInput ? nomeInput.value : "";
        const cnpj = cnpjInput ? cnpjInput.value : "";
        const status = (statusSelect && statusSelect.value !== "selecione") ? statusSelect.value : "";

        if (nome.length > 0 && nome.length < 3 && !cnpj && !status) {
            return;
        }

        try {
            
            const params = new URLSearchParams({
                nome_fornecedor: nome,
                cpf_cnpj: cnpj,
                status: status
            });

            
            const resposta = await fetch(`../../actions/listagem-fornecedor.php?${params.toString()}`);
            if (!resposta.ok) throw new Error("Erro na resposta do servidor");
            
            const fornecedores = await resposta.json();
            renderizarTabela(fornecedores);
        } catch (error) {
            console.error('Erro ao Carregar lista', error);
            bodyTable.innerHTML = '<tr><td colspan="7" style="text-align:center; color:red;">Erro ao carregar fornecedores.</td></tr>';
        }
    }
    // TERMINO DA BUSCA

    function renderizarTabela(fornecedores) {
        bodyTable.innerHTML = "";

        if (fornecedores.length === 0) {
            bodyTable.innerHTML = '<tr><td colspan="7" style="text-align:center;">Nenhum fornecedor encontrado.</td></tr>';
            return;
        }
        
        fornecedores.forEach((fornecedor) => {
            const tr = document.createElement("tr");
            
            if (!fornecedor.status_fornecedor || fornecedor.status_fornecedor == 0) {
                tr.classList.add("linha-inativa");
            }

            tr.innerHTML = `
                <td nowrap>${fornecedor.nome ?? ""}</td>
                <td nowrap>${fornecedor.razao_social ?? ""}</td>
                <td nowrap>${formatarCpfCnpj(fornecedor.cpf_cnpj ?? "")}</td>
                <td nowrap>${fornecedor.contato ?? ""}</td>
                <td nowrap>${formatarTelefone(fornecedor.telefone ?? "")}</td>
                <td nowrap>${fornecedor.email ?? ""}</td>
                <td>
                <div class="table-actions-cell">
                    <a href="edicao-fornecedor.php?id_fornecedor=${fornecedor.id_fornecedor}&id_endereco=${fornecedor.id_endereco}">
                    <i class="bi bi-pencil-square" title="Editar"></i>
                    </a>
                    <label class="switch-component">
                    <input type="checkbox" class="status-toggle" ${fornecedor.status_fornecedor && fornecedor.status_fornecedor != 0 ? "checked" : ""}>
                    <span class="switch-slider"></span>
                    </label>
                </div>
                </td>
            `;

    const checkbox = tr.querySelector(".status-toggle");

        checkbox.addEventListener("change", function () {
            const novoStatus = this.checked; 
            const acao = novoStatus ? "ativar" : "desativar";
            
            const desejaContinuar = confirm(`Deseja ${acao} este fornecedor?`);

            if (desejaContinuar) {
                fetch("../../actions/atualizar-status-fornecedor.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `id_fornecedor=${fornecedor.id_fornecedor}&status=${novoStatus ? 1 : 0}`
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
                        alert("Não foi possível atualizar o fornecedor: " + data.mensagem);
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

    if (nomeInput) {
        nomeInput.addEventListener("input", () => {
            if (nomeInput.value.length >= 3 || nomeInput.value.length === 0) {
                buscarDados();
            }
        });
    }

    
    if (cnpjInput) {
        
        cnpjInput.setAttribute("maxlength", "18");

        cnpjInput.addEventListener("input", (e) => {
            //console.log("teste")
            e.target.value = formatarCpfCnpj(e.target.value);
        });
    }

    if (formFilter) {
        formFilter.addEventListener("submit", (e) => {
            e.preventDefault(); 
            buscarDados();
        });
    }

    buscarDados();
}

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
        return limpo.replace(/^(\d{2})(\d{5})(\d+)/, '(\$1) \$2-\$3');
    } else if (limpo.length === 10) {
        return limpo.replace(/^(\d{2})(\d{4})(\d+)/, '(\$1) \$2-\$3');
    }
    
    return valor;
}

window.addEventListener("DOMContentLoaded", listagemFornecedor);

