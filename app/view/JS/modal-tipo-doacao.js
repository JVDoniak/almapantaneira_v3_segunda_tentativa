document.addEventListener('DOMContentLoaded', () => {

    const btnAdicionarTipo = document.getElementById('btnAdicionarTipo');
    const modalTipo = document.getElementById('modalTipoDoacao');
    const btnFecharModalTipo = document.getElementById('btnFecharModalTipo');
    const btnCancelarTipo = document.getElementById('btnCancelarTipo');


    // Modal inicia oculto
    modalTipo.classList.add('oculta');


    // Abrir modal
    btnAdicionarTipo.addEventListener('click', () => {
        modalTipo.classList.remove('oculta');
    });


    // Fechar modal pelo botão "X"
    btnFecharModalTipo.addEventListener('click', () => {
        modalTipo.classList.add('oculta');
    });


    // Fechar modal pelo botão "Cancelar"
    btnCancelarTipo.addEventListener('click', () => {
        modalTipo.classList.add('oculta');
    });



    modalTipo.addEventListener("submit", async function (event){
        
        event.preventDefault();

        let formulario = document.getElementById("formCadastroTipoDoacao");

        // if (descricao.value.trim() === '') {
        //     alert('Informe a descrição da categoria.');
        //     descricao.focus();
        //     return;
        // }

        const dados = new FormData(formulario);

        console.log(dados)

        try {

            const response = await fetch('../../actions/cadastro-tipo-doacao.php',
                {
                    method: 'POST',
                    body: dados
                }
            );

            const resultado = await response.json();

            if (resultado.success) {

                alert(resultado.message);
                formCadastroTipoDoacao.reset();
                modalTipo.classList.add('oculta');

            } else {

                alert(resultado.message);

            }

        } catch (erro) {

            console.error('Erro:', erro);

            alert('Erro ao conectar com o servidor.');

        }

    });

});

