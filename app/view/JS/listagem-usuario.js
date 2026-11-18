async function listagemUsuario() {

    // ------------------------------------------------------------
    // Campos do filtro (os ids precisam ser iguais aos do listagem-usuario.php)
    // ------------------------------------------------------------
    const nomeInput = document.getElementById("buscar_usuario");
    const cargoSelect = document.getElementById("cargo");
    const emailInput = document.getElementById("email");
    const formFilter = document.getElementById("listar-usuarios");
    const bodyTable = document.getElementById("bodyTable");

    // ------------------------------------------------------------
    // Evita que texto com caracteres especiais (<, >, &) quebre o HTML
    // da tabela ou injete código (XSS) ao usar innerHTML
    // ------------------------------------------------------------
    function escaparHtml(texto) {
        const div = document.createElement("div");
        div.textContent = texto ?? "";
        return div.innerHTML;
    }

    // ------------------------------------------------------------
    // Busca os usuários no servidor enviando os três filtros juntos
    // (nome + cargo + email), assim eles se combinam entre si
    // ------------------------------------------------------------
    async function buscarDados() {
        // Lê o valor atual de cada campo (vazio se o campo não existir)
        const nome = nomeInput ? nomeInput.value : "";
        const email = emailInput ? emailInput.value : "";
        const cargo = cargoSelect ? cargoSelect.value : "";

        // CORREÇÃO: removido o addEventListener que ficava aqui dentro.
        // Ele registrava um novo listener a cada busca e causava
        // várias requisições por tecla digitada.

        try {
            // Monta os filtros na URL
            const params = new URLSearchParams({
                buscar_usuario: nome,
                cargo: cargo,
                email: email
            });

            const resposta = await fetch(`../../actions/listagem-usuario.php?${params.toString()}`);
            if (!resposta.ok) throw new Error("Erro na resposta do servidor");

            const usuarios = await resposta.json();
            console.log(usuarios);
            renderizarTabela(usuarios);

        } catch (error) {
            console.error("Erro ao carregar lista:", error);
            bodyTable.innerHTML = '<tr><td colspan="4" style="text-align:center; color:red;">Erro ao carregar usuários.</td></tr>';
        }
    }

    // ------------------------------------------------------------
    // Desenha as linhas da tabela com os usuários recebidos
    // ------------------------------------------------------------
    function renderizarTabela(usuarios) {
        // Limpa a tabela antes de desenhar o novo resultado
        bodyTable.innerHTML = "";

        // Mensagem quando o filtro não encontra ninguém
        if (usuarios.length === 0) {
            bodyTable.innerHTML = '<tr><td colspan="4" style="text-align:center;">Nenhum usuário encontrado!</td></tr>';
            return;
        }

        usuarios.forEach((usuario) => {
            const tr = document.createElement("tr");

            // Usuário desativado fica com a classe de linha inativa
            if (!usuario.status_usuario || usuario.status_usuario == 0) {
                tr.classList.add("linha-inativa");
            }

            tr.innerHTML = `
                <td>${escaparHtml(usuario.nome)}</td>
                <td>${escaparHtml(usuario.email)}</td>
                <td>${escaparHtml(usuario.tipo)}</td>
                <td>
                    <div class="table-actions-cell">
                        <a href="edicao-usuario.php?id=${usuario.id_usuario}">
                            <i class="bi bi-pencil-square" title="Editar"></i>
                        </a>
                        <label class="switch-component">
                            <input type="checkbox" class="status-toggle" ${usuario.status_usuario && usuario.status_usuario != 0 ? "checked" : ""}>
                            <span class="switch-slider"></span>
                        </label>
                    </div>
                </td>
            `;

            // --------------------------------------------------------
            // Toggle ativo/desativado: salva o novo status no banco
            // --------------------------------------------------------
    const checkbox = tr.querySelector(".status-toggle");

        checkbox.addEventListener("change", function () {
            const novoStatus = this.checked;
            const acao = novoStatus ? "ATIVAR" : "DESATIVAR";

            // Pede confirmação antes de alterar
            const desejaContinuar = confirm(`Deseja ${acao} este Usuário?`);

            if (desejaContinuar) {
                fetch("../../actions/atualizar-status-usuario.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/x-www-form-urlencoded",
                    },
                    body: `id_usuario=${usuario.id_usuario}&status=${novoStatus ? 1 : 0}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.sucesso) {
                        // Atualiza a aparência da linha
                        if (novoStatus) {
                            tr.classList.remove("linha-inativa");
                        } else {
                            tr.classList.add("linha-inativa");
                        }
                    } else {
                        // Erro no banco: volta o toggle ao estado anterior
                        alert("Erro ao atualizar no banco de dados: " + data.mensagem);
                        this.checked = !novoStatus;
                    }
                })
                .catch(error => {
                    // Erro de comunicação: volta o toggle ao estado anterior
                    console.error("Erro detalhado:", error);
                    alert("Erro de comunicação: " + error.message);
                    this.checked = !novoStatus;
                });
            } else {
                // Usuário cancelou: volta o toggle ao estado anterior
                this.checked = !novoStatus;
            }
        });

            bodyTable.appendChild(tr);
        });
    }

    // ------------------------------------------------------------
    // Eventos que disparam a busca
    // ------------------------------------------------------------

    // Busca enquanto digita o nome (3+ letras ou campo vazio)
    if (nomeInput) {
        nomeInput.addEventListener("input", () => {
            if (nomeInput.value.length >= 3 || nomeInput.value.length === 0) {
                buscarDados();
            }
        });
    }

    // NOVO: busca enquanto digita o email (3+ letras ou campo vazio)
    if (emailInput) {
        emailInput.addEventListener("input", () => {
            if (emailInput.value.length >= 3 || emailInput.value.length === 0) {
                buscarDados();
            }
        });
    }

    // Busca ao trocar o cargo
    if (cargoSelect) {
        cargoSelect.addEventListener("change", buscarDados);
    }

    // Busca ao clicar em Filtrar, sem recarregar a página
    if (formFilter) {
        formFilter.addEventListener("submit", (e) => {
            e.preventDefault();
            buscarDados();
        });
    }

    // Carrega a lista ao abrir a página
    buscarDados();
}

// Inicia tudo quando o HTML terminar de carregar
window.addEventListener("DOMContentLoaded", listagemUsuario);