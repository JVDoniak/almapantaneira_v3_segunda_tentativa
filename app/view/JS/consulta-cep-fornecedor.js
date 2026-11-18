document.addEventListener('DOMContentLoaded', () => {
    const campoCep = document.getElementById('cep');
    const campoLogradouro = document.getElementById('logradouro');
    const campoBairro = document.getElementById('bairro');
    const campoCidade = document.getElementById('cidade');
    const campoEstado = document.getElementById('estado');

    function limpaFormularioCep() {
        campoLogradouro.value = '';
        campoBairro.value = '';
        campoCidade.value = '';
        campoEstado.value = '';
    }

    campoCep.addEventListener('change', async () => {
        const cep = campoCep.value.replace(/\D/g, '');

        // console.log(cep)

        if (cep.length === 8) {
            campoLogradouro.value = 'Buscando...';

            try {
                const resposta = await fetch(`https://viacep.com.br/ws/${cep}/json/`);                
                const dados = await resposta.json();
    

                if(dados.logradouro.length > 4){

                    campoLogradouro.value = dados.logradouro;
                    campoBairro.value = dados.bairro;
                    campoCidade.value = dados.localidade;
                    campoEstado.value = dados.estado;

                }
                
                // console.log(campoLogradouro);


            } catch (erro) {
                limpaFormularioCep();
                alert('Erro ao consultar o CEP.');
            }
        } else if (cep.length > 0) {
            limpaFormularioCep();
            alert('Formato de CEP inválido.');
        }
    });
});