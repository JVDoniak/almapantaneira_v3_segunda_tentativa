document.addEventListener('DOMContentLoaded', async function() {
    const form = document.getElementById("formCadastro");
    const campoEstado = form.querySelector('#estado');
    const estates = campoEstado?.tagName === 'SELECT' ? await fetchEstados() : [];

    if (campoEstado?.tagName === 'SELECT') {
        form.querySelector('#estado').innerHTML = `${estates.map(estado => `<option value="${estado.nome}">${estado.nome}</option>`).join('')}`;
    }
   
    // Inicializa o histórico na primeira etapa ao carregar a página
    if (!history.state) {
        history.replaceState({ etapa: "tipo" }, "", window.location.pathname);
    }

    form.addEventListener('submit', async function(e) {
        e.preventDefault();

    // Seleciona todos os inputs do formulário que não sejam hidden
    const inputs = form.querySelectorAll('input:not([type="hidden"])');
    
    for (let input of inputs) {
        // Verifica se o campo está visível na tela (ignora os campos da outra aba que estão ocultos)
        const estaVisivel = input.offsetParent !== null;

        if (estaVisivel) {
            const valorTratado = input.value.trim();

            // Ignora o campo de complemento, pois ele é opcional
            const enderecoOpcional = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'].includes(input.id);
            if (!enderecoOpcional && !valorTratado) {
                const labelText = input.previousElementSibling ? input.previousElementSibling.innerText.replace('*', '').trim() : 'Campo';
                
                alert(`O campo "${labelText}" não pode ficar vazio ou conter apenas espaços.`);
                input.focus();
                return; // Interrompe o envio
            }
        }
    }
        const formData = new FormData(this);

        try {
            const response = await fetch('../../actions/cadastro-doador.php', {
                method: 'POST',
                body: formData
            });

            const resposta = await response.json();
            alert(resposta.message);

            if (resposta.success) {
                window.location.href = './listagem-doador.php';
            }
        } catch (error) {
            console.error("Erro ao cadastrar doador:", error);
        }
    });
});

// Funções de controle de visualização das etapas
function mostrarSelecao() {
    document.getElementById('step-tipo-doador').style.display = 'block';
    document.getElementById('step-formulario').style.display = 'none';
}

function mostrarFormulario(tipo) {
    document.getElementById('step-tipo-doador').style.display = 'none';
    document.getElementById('step-formulario').style.display = 'block';
    
    // Configura os campos visíveis conforme o tipo
    configurarCamposTipo(tipo);
}

// Função de usabilidade dos cards chamada ao clicar em Pessoa Física / Jurídica
function selecionarTipoDoador(tipoTexto) {
    const inputTipo = document.getElementById('tipo');
    
    // Define o valor interno ('pf' ou 'pj')
    const tipoValor = (tipoTexto === 'Pessoa Física') ? 'Pessoa Física' : 'Pessoa Jurídica';
    inputTipo.value = tipoValor;

    // Adiciona um novo estado no histórico do navegador antes de mudar de tela
    history.pushState(
        { etapa: "dados", tipo: tipoTexto },
        "",
        `?etapa=dados&tipo=${tipoValor}`
    );

    mostrarFormulario(tipoTexto);
}

function configurarCamposTipo(tipo) {
    const badgeTexto = document.getElementById('badge-texto');
    const badgeIcon = document.getElementById('badge-icon');
    const labelDoc = document.getElementById('label-doc');
    const inputDoc = document.getElementById('cpf_cnpj');
    const camposPF = document.querySelectorAll('.campo-pf');
    const camposPJ = document.querySelectorAll('.campo-pj');

    if (tipo === 'Pessoa Física' || tipo === 'Pessoa Física') {
        badgeTexto.innerText = 'Pessoa Física';
        badgeIcon.className = 'bi bi-person-check-fill';
        labelDoc.innerHTML = 'CPF <span style="color: red;">*</span>';
        inputDoc.placeholder = '000.000.000-00';

        camposPF.forEach(el => el.style.display = 'flex');
        camposPJ.forEach(el => el.style.display = 'none');
    } else {
        badgeTexto.innerText = 'Pessoa Jurídica';
        badgeIcon.className = 'bi bi-building-check';
        labelDoc.innerHTML = 'CNPJ <span style="color: red;">*</span>';
        inputDoc.placeholder = '00.000.000/0001-00';

        camposPF.forEach(el => el.style.display = 'none');
        camposPJ.forEach(el => el.style.display = 'flex');
    }
}

function reiniciarSelecao() {
    history.back();
}

// Ouve o clique no botão "Voltar" ou "Avançar" do navegador
window.addEventListener("popstate", (event) => {
    const state = event.state;

    if (!state || state.etapa === "tipo") {
        mostrarSelecao();
        return;
    }

    if (state.etapa === "dados") {
        mostrarFormulario(state.tipo);
    }
});

async function fetchEstados(){
    try {
        const resposta = await fetch(`https://servicodados.ibge.gov.br/api/v1/localidades/estados?orderBy=nome`);                
        const dados = await resposta.json();
        return dados;
    } catch (err){  
        console.error(err);
    }
}
