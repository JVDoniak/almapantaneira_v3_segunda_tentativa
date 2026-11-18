document.addEventListener('DOMContentLoaded', function() {
    document.getElementById("formCadastroFornecedor").addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

    try {
        const response = await fetch('../../actions/cadastro-fornecedor.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await response.json();
        alert(resultado.message);
        window.location.href = './listagem-fornecedor.php';
    } 
    catch (error) {
        alert ("Não foi possível cadastrar o fornecedor. Tente novamente.");
        console.log(error);
    }
    });

});