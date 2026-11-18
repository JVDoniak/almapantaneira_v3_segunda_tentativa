document.addEventListener('DOMContentLoaded', () => {
    inicializarMascara();
});

function inicializarMascara() {
    const cpfCnpjInput = document.getElementById('cpf_cnpj');
    const telefoneInput = document.getElementById('telefone');
    const cepInput = document.getElementById('cep');
    const emailInput = document.getElementById('email');

    if (cpfCnpjInput) {
        // Mantém a sua máscara existente
        cpfCnpjInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            if (value.length <= 11) {
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d)/, '$1.$2');
                value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            } else {
                value = value.slice(0, 14);
                value = value.replace(/^(\d{2})(\d)/, '$1.$2');
                value = value.replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3');
                value = value.replace(/\.(\d{3})(\d)/, '.$1/$2');
                value = value.replace(/(\d{4})(\d)/, '$1-$2');
            }
            e.target.value = value;
        });

        cpfCnpjInput.addEventListener('blur', (e) => {
            const valorLimpo = e.target.value.replace(/\D/g, '');
            
            if (!valorLimpo) return; 

            let valido = false;
            if (valorLimpo.length === 11) {
                valido = validarCPF(valorLimpo);
            } else if (valorLimpo.length === 14) {
                valido = validarCNPJ(valorLimpo);
            }

            if (!valido) {
                alert('CPF ou CNPJ inválido. Verifique os dados.');
                e.target.value = '';
                setTimeout(() => e.target.focus(), 10);
            }
        });
    }

    if (telefoneInput) {
        telefoneInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.slice(0, 11);

            if (value.length > 2 && value.length <= 6) {
                value = value.replace(/^(\d{2})(\d+)/, '($1) $2');

            } else if (value.length > 6 && value.length <= 10) {
                value = value.replace(/^(\d{2})(\d{4})(\d+)/, '($1) $2-$3');

            } else if (value.length === 11) {
                value = value.replace(/^(\d{2})(\d{5})(\d+)/, '($1) $2-$3');
            }
            e.target.value = value;

        });
    }

    if (cepInput) {
        cepInput.addEventListener('input', (e) => {
            let value = e.target.value.replace(/\D/g, '');
            value = value.slice(0, 8);
            value = value.replace(/^(\d{5})(\d)/, '$1-$2');
            e.target.value = value;
        });
    }

    if (emailInput) {
        emailInput.addEventListener('blur', (e) => {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (e.target.value && !emailPattern.test(e.target.value)) {
                alert('Informe um e-mail válido.');
                e.target.value = '';
                setTimeout(() => e.target.focus(), 10);
            }
        });
    }
}

function validarCPF(cpf) {
    if (/^(\d)\1+$/.test(cpf)) return false; 
    
    let soma = 0;
    let resto;

    for (let i = 1; i <= 9; i++) soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;

    soma = 0;
    for (let i = 1; i <= 10; i++) soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    resto = (soma * 10) % 11;
    if ((resto === 10) || (resto === 11)) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

function validarCNPJ(cnpj) {
    if (/^(\d)\1+$/.test(cnpj)) return false; 

    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    let resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(0))) return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    resultado = soma % 11 < 2 ? 0 : 11 - (soma % 11);
    if (resultado !== parseInt(digitos.charAt(1))) return false;

    return true;
}
