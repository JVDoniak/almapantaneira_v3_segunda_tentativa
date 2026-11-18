// document.getElementById("formCadastro").addEventListener("submit", async function (e) {
//     e.preventDefault();

//     const formData = new FormData(this);

//     try {
//         const response = await fetch("../action/login.php", {
//             method: "POST",
//             body: formData
//         });

//         // if (response.ok) {
//         //     window.location.href = "../../view/pages/dashboard.php";
//         // } else {
//         //     alert(resultado.mensagem || "Email ou senha inválidos.");
//         // }

//     } catch (error) {
//         alert("Erro na requisição");
//         console.error(error);
//     }
// });