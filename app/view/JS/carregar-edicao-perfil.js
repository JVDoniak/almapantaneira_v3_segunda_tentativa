async function carregarDadosPerfil(id_user) {
    try {

        const perfil = await fetch(`../../actions/carregar-perfil.php?id_user=${id_user}`);
        const resposta = await perfil.json();

        console.log(resposta)
        if (resposta.status !== 200) {
            throw new Error(resposta.msg || "Erro ao carregar usuário.");
        }

        const usuario = resposta.data;
       
        document.getElementById("id_usuario").value = usuario.id_usuario;
        document.getElementById("nome").value = usuario.nome;
        document.getElementById("email").value = usuario.email;
        document.getElementById("tipo").value = usuario.tipo;
        document.getElementById("telefone").value = usuario.telefone;
        document.getElementById("cpf").value = usuario.cpf;
        document.getElementById("senha").value = 1234;
        document.getElementById("confirmarSenha").value = 1234;

        // Dentro do seu carregarDadosPerfil()
        const fotoPerfil = document.getElementById("fotoPerfil");
        const iconePerfil = document.getElementById("iconePerfil");

        if (usuario.foto && usuario.foto !== "") {
            // Se o banco já salvar com a barra inicial /almapantaneira/...
            fotoPerfil.src = usuario.foto; 
            fotoPerfil.style.display = "block";
            iconePerfil.style.display = "none";
        } else {
            fotoPerfil.src = "";
            fotoPerfil.style.display = "none";
            iconePerfil.style.display = "block";
        }

        // Preview da nova foto selecionada
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

    } catch (error) {
        console.error("Erro ao carregar perfil:", error);
    }
}

window.addEventListener("DOMContentLoaded", function () {
    const parametros = new URLSearchParams(window.location.search);
    const id_user = parametros.get("id");
    carregarDadosPerfil(id_user);
});