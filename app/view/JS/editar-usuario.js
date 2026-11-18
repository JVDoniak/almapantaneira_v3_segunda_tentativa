// ==========================================================
// editar-usuario.js
// Tela de edição de usuário: carrega os dados atuais no
// formulário e envia as alterações para o PHP.
// ==========================================================

// ----------------------------------------------------------
// Função que busca no PHP os dados do usuário pelo ID
// e preenche os campos do formulário.
// ----------------------------------------------------------
async function carregarDadosUsuario(id_usuario) {
    try {
        // Chama a action via GET, enviando o ID na URL (?id=...)
        const resposta = await fetch('../../actions/editar-usuario.php?id=' + id_usuario);

        // Converte a resposta do PHP (JSON) em objeto JavaScript
        const usuario = await resposta.json();

        // O PHP devolve "status" dentro do JSON: 200 = encontrou o usuário
        if (usuario.status == 200) {
            // Preenche cada campo do formulário com o valor vindo do banco
            document.getElementById("id_usuario").value = usuario.data.id_usuario; // campo hidden (necessário para o UPDATE)
            document.getElementById("nome").value = usuario.data.nome;
            document.getElementById("email").value = usuario.data.email;
            document.getElementById("telefone").value = usuario.data.telefone;
            document.getElementById("cpf").value = usuario.data.cpf;
            document.getElementById("cargo").value = usuario.data.tipo; // no banco a coluna se chama "tipo"

            // A senha NÃO é preenchida: o PHP não a envia e os campos ficam vazios.
            // Vazio = mantém a senha atual.
        } else {
            // Usuário não existe: mostra a mensagem do PHP e volta para a listagem
            alert(usuario.msg);
            window.location.href = './listagem-usuario.php';
        }

    } catch (error) {
        // Erro de rede, de caminho do fetch ou JSON inválido
        console.error("Erro ao carregar usuário:", error);
    }
}



    // ------------------------------------------------------
    // Lê o ID da URL (edicao-usuario.php?id=9) e carrega os dados
    // ------------------------------------------------------
    const parametros = new URLSearchParams(window.location.search);
    const id_usuario = parametros.get("id");

    carregarDadosUsuario(id_usuario);

    // ------------------------------------------------------
    // Evento de envio do formulário (clique em "Salvar").
    // O <form> precisa ter id="formEdicaoUsuario".
    // ------------------------------------------------------
    const formulario = document.getElementById("formEdicaoUsuario");

    if (formulario) {
        formulario.addEventListener("submit", async function (event) {

            // Impede o envio normal do formulário (que recarregaria a página)
            event.preventDefault();

            // Captura todos os campos do formulário (usa o atributo "name" de cada input)
            const formData = new FormData(formulario);

            try {
                // Envia os dados via POST para o PHP fazer o UPDATE no banco
                const response = await fetch('../../actions/editar-usuario.php', {
                    method: 'POST',
                    body: formData
                });

                // Converte a resposta do PHP (JSON) em objeto JavaScript
                const result = await response.json();

                // O "status" (200 ou 400) vem DENTRO do JSON, não no HTTP
                if (result.status == 400) {
                    // Erro: mostra a mensagem e permanece na tela de edição
                    alert(result.msg);
                } else {
                    // Sucesso: mostra a mensagem e volta para a listagem
                    alert(result.msg);
                    window.location.href = './listagem-usuario.php';
                }
            }
            catch (error) {
                // Erro de rede, caminho errado ou o PHP devolveu algo que não é JSON
                console.log(error);
            }
        });

    }
