
let input_cpf_cnpj = document.getElementById("cpf_cnpj");
let tipo_doador_select = document.getElementById("tipo_doador");
let nome_input = document.getElementById("nome_doador");
let label_nome = document.getElementById("label_nome_doador");

function mascararCPF(digitos) {
        return digitos
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    }

function mascararCNPJ(digitos) {
        return digitos
            .replace(/(\d{2})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1/$2')
            .replace(/(\d{4})(\d{1,2})$/, '$1-$2');
    }

function atualizarCamposDoador() {
        var tipo = tipo_doador_select.value;
        if (tipo === 'fisica') {
            label_cpf_cnpj.textContent = 'CPF do Doador';
            label_nome.textContent = 'Nome do Doador';
            nome_input.placeholder = 'Nome do doador';
        } else if (tipo === 'juridica') {
            label_cpf_cnpj.textContent = 'CNPJ da Empresa';
            label_nome.textContent = 'Razão Social';
            nome_input.placeholder = 'Razão Social';
        }
    }


input_cpf_cnpj.addEventListener('keypress', function(){

    const digitos = input_cpf_cnpj.value.replace(/\D/g, '').slice(0, 14);

    if (digitos.length === 0) {
            input_cpf_cnpj.value = '';
            tipo_doador_select.value = '';
            nome_input.value = '';

        } else if (digitos.length <= 11) {
            input_cpf_cnpj.value = mascararCPF(digitos);
            tipo_doador_select.value = 'fisica';

        } else {
            input_cpf_cnpj.value = mascararCNPJ(digitos);
            tipo_doador_select.value = 'juridica';
        }
})

  