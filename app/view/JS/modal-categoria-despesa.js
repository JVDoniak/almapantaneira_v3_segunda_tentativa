document.addEventListener('DOMContentLoaded', () => {

    const btnAdicionarCategoria = document.getElementById('btnAdicionarCategoria');
    const modalCategoria = document.getElementById('modalCategoriaDespesa');
    const btnFecharModalCategoria = document.getElementById('btnFecharModalCategoria');
    const btnCancelarTCategoria = document.getElementById('btnCancelarCategoria');

    // Modal inicia oculto
    modalCategoria.classList.add('oculta');


    // Abrir modal
    btnAdicionarCategoria.addEventListener('click', () => {
        modalCategoria.classList.remove('oculta');
    });


    // Fechar modal pelo botão "X"
    btnFecharModalCategoria.addEventListener('click', () => {
        modalCategoria.classList.add('oculta');
    });


    // Fechar modal pelo botão "Cancelar"
    btnCancelarTCategoria.addEventListener('click', () => {
        modalCategoria.classList.add('oculta');
    });



    modalCategoria.addEventListener("submit", async function (event){
        
        event.preventDefault();

        let formulario = document.getElementById("formCadastroCategoria");

        // if (descricao.value.trim() === '') {
        //     alert('Informe a descrição da categoria.');
        //     descricao.focus();
        //     return;
        // }

        const dados = new FormData(formulario);

        console.log(dados)

        try {

            const response = await fetch('../../actions/cadastro-categoria-despesa.php',
                {
                    method: 'POST',
                    body: dados
                }
            );

            const resultado = await response.json();

            if (resultado.success) {

                alert(resultado.message);
                const categoria = resultado.categoria;
                const select = document.getElementById('id_categoria_despesa');
                const option = document.createElement('option');
                option.value = categoria.id_categoria_despesa;
                option.textContent = categoria.descricao;
                select.appendChild(option);
                select.value = String(categoria.id_categoria_despesa);
                formulario.reset();
                modalCategoria.classList.add('oculta');

            } else {

                alert(resultado.message);

            }

        } catch (erro) {

            console.error('Erro:', erro);

            alert('Erro ao conectar com o servidor.');

        }

    });

});

