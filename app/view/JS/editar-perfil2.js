
const estilo = document.createElement('style');

estilo.textContent = `
                    .input-senha { position: relative; display: block; }
                    .input-senha input { width: 100%; box-sizing: border-box; padding-right: 42px; }
                    .input-senha .toggle-senha {
                    position: absolute; right: 12px; top: 50%;
                    transform: translateY(-50%);
                    background: transparent; border: none; box-shadow: none;
                    padding: 0; cursor: pointer; color: #666; font-size: 1.1rem;
                            }
                        `;
document.head.appendChild(estilo);


// ------------------------------------------------------
// Alterna entre mostrar e ocultar a senha (vale para os dois campos)
// ------------------------------------------------------
document.querySelectorAll('.toggle-senha').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.previousElementSibling; // input ao lado do botão
        if (!input) return;

        const icone = btn.querySelector('i');
        const oculta = input.type === 'password';

        input.type = oculta ? 'text' : 'password';                  // troca o tipo
        icone.className = oculta ? 'bi bi-eye' : 'bi bi-eye-slash'; // troca o ícone
        btn.setAttribute('aria-label', oculta ? 'Ocultar senha' : 'Mostrar senha');
    });
});

document.getElementById("editarSenha").addEventListener("click", () => {

    const senha = document.getElementById("senha");
    const confirmarSenha = document.getElementById("confirmarSenha");

    senha.disabled = false;
    confirmarSenha.disabled = false;

    senha.value = "";
    confirmarSenha.value = "";

    document.querySelectorAll(".toggle-senha").forEach(btn => {

    btn.style.display = "block";

});

senha.focus();
});

const fotoPerfil = document.getElementById("fotoPerfil");
const iconePerfil = document.getElementById("iconePerfil");

uploadFoto.addEventListener("change", function () {

    if (this.files && this.files[0]) {

        const reader = new FileReader();

        reader.onload = function (e) {

            fotoPerfil.src = e.target.result;

            fotoPerfil.style.display = "block";
            iconePerfil.style.display = "none";
        };

        reader.readAsDataURL(this.files[0]);
    }
});

document.getElementById("formEditarPerfil").addEventListener('submit', async function(e){
        e.preventDefault();

        const senha = document.getElementById('senha').value;
        const confirmar_senha = document.getElementById('confirmarSenha').value;
        if(senha != confirmar_senha){
            alert("As senhas não coincidem!!");
            return;
        }

    const formData = new FormData(this);

    try {
        const response = await fetch('../../actions/editar-perfil.php', {
            method: 'POST',
            body: formData
        });

        const resultado = await response.json();
        if(resultado.status === 200){
            alert("Perfil Editado com Sucesso!")
        }else{
            console.log(resultado);
            alert("Erro ao editar o perfil")
        }
    } catch (error) {
        alert("Erro na requsição");
        console.log(error);
    }


});
