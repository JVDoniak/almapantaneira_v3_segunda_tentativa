const camposCodigo = document.querySelectorAll('.input-codigo');

camposCodigo.forEach((campo, index) => {

    // Aceita só números e pula para o próximo campo
    campo.addEventListener('input', () => {
        campo.value = campo.value.replace(/\D/g, '').slice(0, 1);

        if (campo.value && index < camposCodigo.length - 1) {
            camposCodigo[index + 1].focus();
        }
    });

    // Backspace em campo vazio volta para o anterior
    campo.addEventListener('keydown', (e) => {
        if (e.key === 'Backspace' && campo.value === '' && index > 0) {
            camposCodigo[index - 1].focus();
        }
    });

    // Colar o código inteiro (ex: "12345") preenche todos os campos
    campo.addEventListener('paste', (e) => {
        e.preventDefault();
        const numeros = (e.clipboardData.getData('text') || '').replace(/\D/g, '').slice(0, camposCodigo.length);

        numeros.split('').forEach((numero, i) => {
            camposCodigo[i].value = numero;
        });

        const proximo = Math.min(numeros.length, camposCodigo.length - 1);
        camposCodigo[proximo].focus();
    });
});

// ---------- Tela da nova senha ----------
const formNovaSenha = document.getElementById('formNovaSenha');

if (formNovaSenha) {
    const alerta = document.getElementById('alertaNovaSenha');

    function mostrarAlerta(mensagem) {
        alerta.textContent = mensagem;
        alerta.classList.add('visivel');
    }

    formNovaSenha.addEventListener('submit', (e) => {
        const senha = document.getElementById('senha').value;
        const confirmarSenha = document.getElementById('confirmar_senha').value;

        if (senha.length < 6) {
            e.preventDefault();
            mostrarAlerta('A senha deve ter no mínimo 6 caracteres.');
            return;
        }

        if (senha !== confirmarSenha) {
            e.preventDefault();
            mostrarAlerta('As senhas não conferem.');
        }
    });
}