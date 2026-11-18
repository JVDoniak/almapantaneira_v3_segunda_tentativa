async function carregarDadosDoador(id_doador) {
    if (!id_doador) {
        alert("ID do doador não informado na URL!");
        window.location.href = './listagem-doador.php';
        return;
    }

    try {
        const resposta = await fetch(`../../actions/editar-doador.php?id_doador=${id_doador}`);
        const textoResposta = await resposta.text();
        
        let result;
        try {
            result = JSON.parse(textoResposta);
            console.log(result);
        } catch (e) {
            console.error("Erro ao converter JSON:", textoResposta);
            return;
        }

        if (result && result.status === 200 && result.data) {
            const doador = result.data;
            console.log("Dados recebidos para preenchimento:", doador);

            // 1. IDs Ocultos
            const inputIdDoador = document.getElementById("id_doador");
            if (inputIdDoador) inputIdDoador.value = doador.id_doador || "";

            const inputIdEndereco = document.getElementById("id_endereco");
            if (inputIdEndereco) inputIdEndereco.value = doador.id_endereco || "";

            // 2. Define o tipo e ajusta o select de forma segura
            const tipoDoador = doador.tipo || "Pessoa Física";
            const selectTipo = document.getElementById("tipo");
            if (selectTipo) {
                if (String(tipoDoador).toLowerCase().includes('jurídica') || String(tipoDoador).toLowerCase() === 'Pessoa Jurídica') {
                    selectTipo.value = "Pessoa Jurídica";
                } else {
                    selectTipo.value = "Pessoa Física";
                }
            }

            // Aplica a visibilidade imediatamente com base no tipo recebido
            aplicarVisibilidadeTipo(tipoDoador);

            // 3. Preenchimento condicional dos campos de nome / razão social
            const isPj = String(tipoDoador).toLowerCase().includes('jurídica') || String(tipoDoador).toLowerCase() === 'Pessoa Jurídica';

            if (isPj) {
                const inputRazao = document.getElementById("razao_social");
                if (inputRazao) inputRazao.value = doador.razao_social || "";

                const inputFantasia = document.getElementById("nome_fantasia");
                if (inputFantasia) inputFantasia.value = doador.nome || ""; // Nome fantasia guardado na coluna nome

                const inputContato = document.getElementById("contato_empresa");
                if (inputContato) inputContato.value = doador.contato_empresa || "";
            } else {
                const inputNome = document.getElementById("nome");
                if (inputNome) inputNome.value = doador.nome || "";
            }

            // 4. Campos Comuns
            const inputEmail = document.getElementById("email");
            if (inputEmail) inputEmail.value = doador.email || "";

            // 5. Endereço
            const inputLogradouro = document.getElementById("logradouro");
            if (inputLogradouro) inputLogradouro.value = doador.logradouro || "";

            const inputNumero = document.getElementById("numero");
            if (inputNumero) inputNumero.value = doador.numero || "";

            const inputComplemento = document.getElementById("complemento");
            if (inputComplemento) inputComplemento.value = doador.complemento || "";

            const inputBairro = document.getElementById("bairro");
            if (inputBairro) inputBairro.value = doador.bairro || "";

            const inputCidade = document.getElementById("cidade");
            if (inputCidade) inputCidade.value = doador.cidade || "";

            const inputEstado = document.getElementById("estado");
            if (inputEstado) {
                inputEstado.value = doador.estado || "";
                // Se o estado não estiver pré-carregado no select, cria a opção de forma limpa
                if (!inputEstado.value && doador.estado) {
                    const optionEstado = document.createElement('option');
                    optionEstado.value = doador.estado;
                    optionEstado.selected = true;
                    optionEstado.innerText = doador.estado;
                    inputEstado.appendChild(optionEstado);
                }
            }

            // 6. Campos com Máscara (CPF/CNPJ, Telefone, CEP)
            const cpfCnpjInput = document.getElementById("cpf_cnpj");
            if (cpfCnpjInput) {
                cpfCnpjInput.value = doador.cpf_cnpj || "";
                cpfCnpjInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const telefoneInput = document.getElementById("telefone");
            if (telefoneInput) {
                telefoneInput.value = doador.telefone || "";
                telefoneInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            const cepInput = document.getElementById("cep");
            if (cepInput) {
                cepInput.value = doador.cep || "";
                cepInput.dispatchEvent(new Event('input', { bubbles: true }));
            }

            console.log("Preenchimento concluído com sucesso!");

        } else {
            alert("Erro ao carregar dados do doador.");
            window.location.href = './listagem-doador.php';
        }
    } catch (error) {
        console.error("Erro crítico na requisição:", error);
    }
}

// Função unificada e robusta para gerir a visibilidade dos campos
function aplicarVisibilidadeTipo(tipo) {
    const tipoStr = String(tipo || "").toLowerCase();
    const isPj = tipoStr === 'Pessoa Jurídica' || tipoStr.includes('jurídica');

    const camposPF = document.querySelectorAll('.campo-pf');
    const camposPJ = document.querySelectorAll('.campo-pj');
    const labelDoc = document.getElementById('label-doc');
    const inputDoc = document.getElementById('cpf_cnpj');

    if (isPj) {
        camposPF.forEach(el => el.style.display = 'none');
        camposPJ.forEach(el => el.style.display = 'flex');
        if (labelDoc) labelDoc.innerHTML = 'CNPJ <span style="color: red;">*</span>';
        if (inputDoc) inputDoc.placeholder = '00.000.000/0001-00';
    } else {
        camposPF.forEach(el => el.style.display = 'flex');
        camposPJ.forEach(el => el.style.display = 'none');
        if (labelDoc) labelDoc.innerHTML = 'CPF <span style="color: red;">*</span>';
        if (inputDoc) inputDoc.placeholder = '000.000.000-00';
    }
}

window.addEventListener("DOMContentLoaded", function () {
    const parametros = new URLSearchParams(window.location.search);
    const id_doador = parametros.get("id_doador");
    carregarDadosDoador(id_doador);

    // Garante que se o usuário alterar o select manualmente, os campos se adaptam
    const selectTipo = document.getElementById("tipo");
    if (selectTipo) {
        selectTipo.addEventListener("change", function() {
            aplicarVisibilidadeTipo(this.value);
        });
    }
});

// Manipulação do Envio do Formulário de Edição
const formulario = document.getElementById("formEdicaoDoador");

if (formulario) {
    formulario.addEventListener("submit", async function (event) {
        event.preventDefault();

        const formData = new FormData(formulario);

        try {
            const response = await fetch('../../actions/editar-doador.php', {
                method: 'POST',
                body: formData
            });

            const textoResposta = await response.text();
            let result;

            try {
                result = JSON.parse(textoResposta);
            } catch (e) {
                console.error("A resposta do POST não é um JSON válido:", textoResposta);
                alert("Não foi possível salvar os dados do doador. Tente novamente.");
                return;
            }

            alert(result.msg || result.message);

            if (result.status === 200 || result.success) {
                window.location.href = './listagem-doador.php';
            }
        } catch (error) {
            console.error("Erro no envio:", error);
        }
    });
}