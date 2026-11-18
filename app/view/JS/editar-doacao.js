async function carregarDadosdoacao(id_doacao) {
    try {
        const resposta = await fetch('../../actions/editar-doacao.php?id=' + id_doacao);
        const doacao = await resposta.json();
        
        console.log(doacao);

        if (doacao.status == 200) {
            document.getElementById("id_doacao").value = doacao.data.id_doacao;
            document.getElementById("id_doador").value = doacao.data.id_doador;
            document.getElementById("nome_doador").value = doacao.data.nome;
            document.getElementById("tipo").value = doacao.data.tipo;
            document.getElementById("projeto").value = doacao.data.id_projeto ?? "";
            document.getElementById("descricao").value = doacao.data.descricao;
            document.getElementById("forma").value = doacao.data.forma_pagamento;
            document.getElementById("data_doacao").value = doacao.data.data_doacao;
            document.getElementById("valor").value = doacao.data.valor;
        } else {
            alert(doacao.msg);
            window.location.href = './listagem-doacao.php';
        }
    } catch (error) {
        console.error("ERRO AO CARREGAR DOAÇÃO:", error);
    }
}

window.addEventListener("DOMContentLoaded", function() {
    const parametros = new URLSearchParams(window.location.search);
    const id_doacao = parametros.get("id");
    carregarDadosdoacao(id_doacao);
});

let formulario = document.getElementById("form_edicao_doacao");

formulario.addEventListener("submit", async function (event) {
    event.preventDefault();

    const formData = new FormData(formulario);

    try {
        let response = await fetch('../../actions/editar-doacao.php', {
            method: 'POST',
            body: formData
        });

        let result = await response.json();

        if (!response.ok || result.status != 200) {
            alert(result.msg);
        } else {
            alert(result.msg);
            window.location.href = './listagem-doacao.php';
        }
    }
    catch (error) {
        console.log(error);
    }
});
