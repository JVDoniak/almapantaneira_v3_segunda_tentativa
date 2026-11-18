async function carregarDadosProjeto(id_projeto) {
    // console.log(id_projeto);
    try {
        const resposta = await fetch(`../../actions/editar-projeto.php?id=${id_projeto}`);
        const projeto = await resposta.json();

        // console.log(projeto);
     
        if (projeto.status == 200) {
            document.getElementById("id_projeto").value = projeto.data.id_projeto;
            document.getElementById("nome").value = projeto.data.nome;
            document.getElementById("responsavel").value = projeto.data.responsavel;
            document.getElementById("local_projeto").value = projeto.data.local_projeto;
            document.getElementById("tipo_projeto").value = projeto.data.tipo_projeto;
            document.getElementById("data_inicio").value = projeto.data.data_inicio;
            document.getElementById("data_termino").value = projeto.data.data_termino;
            document.getElementById("valor_projeto").value = projeto.data.valor_projeto;
            document.getElementById("andamento").value = projeto.data.andamento;
            document.getElementById("descricao").value = projeto.data.descricao;
        }
        else {
            alert(projeto.msg);
            window.location.href = './listagem-projeto.php';
        }
    } catch (error) {
        console.error("Erro ao carregar lista:", error);

    }
}
window.addEventListener("DOMContentLoaded", function () {

    const parametros = new URLSearchParams(window.location.search);

    const id_projeto = parametros.get("id_projeto");
    carregarDadosProjeto(id_projeto);
});



window.addEventListener("submit", async function (event) {

    let formulario = document.getElementById("form_editar_projeto");

    event.preventDefault();

    const formData = new FormData(formulario);

    let dados = Object.fromEntries(formData.entries());

    console.log(dados);
    try {
        let response = await fetch('../../actions/editar-projeto.php', {
            method: 'POST',
            body: formData
        });

        let result = await response.json();

        if (response.status == 400) {
            alert(result.msg);

        } else {
            alert(result.msg);
            window.location.href = './listagem-projeto.php';
        }
    }
    catch (error) {
        console.log(error);
    }
});

