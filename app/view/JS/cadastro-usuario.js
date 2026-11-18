document.addEventListener('DOMContentLoaded', function () {

    // ------------------------------------------------------
    // Envio do formulário
    // ------------------------------------------------------
    const formulario = document.getElementById("formCadastroUsuario");

    if (formulario) {
        formulario.addEventListener('submit', async function (e) {

            // Impede o envio tradicional (que recarregaria a página)
            e.preventDefault();

            // Captura todos os campos do formulário (usa o atributo "name")
            const formData = new FormData(this);

            try {
                // Envia os dados via POST para a action do PHP
                const response = await fetch('../../actions/cadastro-usuario.php', {
                    method: 'POST',
                    body: formData
                });

                // Converte a resposta do PHP (JSON) em objeto JavaScript
                const resultado = await response.json();

                // Erro: status 400 ou success false (o PHP devolve "success")
                if (resultado.status == 400 || resultado.success === false) {
                    alert(resultado.msg || resultado.message);
                } else {
                    // Sucesso: mostra a mensagem e vai para a listagem
                    alert(resultado.msg || resultado.message);
                    window.location.href = './listagem-usuario.php';
                }

            } catch (error) {
                // Erro de rede, caminho errado ou falha ao processar o JSON
                console.error("Erro na requisição:", error);
                alert("Erro ao tentar realizar o cadastro.");
            }
        });
    }
});