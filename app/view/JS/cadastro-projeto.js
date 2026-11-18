document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById("formCadastroProjeto");
    form.addEventListener('submit', async function(e) {
        e.preventDefault();

        const formData = new FormData(this);

        const loader = (() => {
        let el = null;

        function criar() {
            const style = document.createElement("style");
            style.textContent = `
                #loader {
                    position: fixed; inset: 0;
                    display: none; align-items: center; justify-content: center;
                    background: rgba(0, 0, 0, .4); z-index: 9999;
                }
                #loader.ativo { display: flex; }
                #loader::after {
                    content: ""; width: 48px; height: 48px;
                    border: 5px solid #fff; border-top-color: transparent;
                    border-radius: 50%; animation: girar .8s linear infinite;
                }
                @keyframes girar { to { transform: rotate(360deg); } }`;
                document.head.appendChild(style);

                el = document.createElement("div");
                el.id = "loader";
                document.body.appendChild(el);
            }

            return {
                mostrar() {
                    if (!el) criar();
                    el.classList.add("ativo");
                },
                esconder() {
                    if (el) el.classList.remove("ativo");
                },
            };
        })();

        loader.mostrar();
        try {
        const resposta = await fetch('../../actions/listagem-projeto.php');

        } finally {
            loader.esconder();
        }

        try{
            const response = await fetch('../../actions/cadastro-projeto.php', {
                method: 'POST',
                body: formData
            });
            const resultado = await response.json();
            console.log(resultado)
            alert(resultado.message);
            
            console.log('hello')
            form.reset();
            window.location.href='listagem-projeto.php'
        } catch (error) {
            //alert("Erro na requisição");
            console.log(error)
        }
    });
});

// document.querySelector("#btn-add-category").addEventListener('click', () => {
//     modal = document.querySelector("#modal")
//     document.querySelector("#modal").classList.add("show");

//     modal.querySelector('form').addEventListener('submit', (event) => {
//         event.preventDefault();

//         modal.classList.remove('show');
//     } )
// })