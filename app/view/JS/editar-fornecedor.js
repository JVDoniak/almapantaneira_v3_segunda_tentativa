async function carregarDadosFornecedor(id_fornecedor,id_endereco) {
    try {
    const resposta = await fetch('../../actions/editar-fornecedor.php?id_fornecedor='+id_fornecedor+'&id_endereco='+id_endereco);
    const result = await resposta.json();
 
        if(result.status == 200)
        {
            
            document.getElementById("id_fornecedor").value = result.data.id_fornecedor;
            document.getElementById("nome").value = result.data.nome;
            document.getElementById("razao_social").value = result.data.razao_social;
            document.getElementById("cpf_cnpj").value = formatarCpfCnpj(result.data.cpf_cnpj);
            document.getElementById("contato").value = result.data.contato;
            document.getElementById("telefone").value = formatarTelefone(result.data.telefone);
            document.getElementById("email").value = result.data.email;
            document.getElementById("id_endereco").value = result.data.id_endereco;
            document.getElementById("cep").value = formatarCep(result.data.cep);
            document.getElementById("logradouro").value = result.data.logradouro;
            document.getElementById("numero").value = result.data.numero;
            document.getElementById("complemento").value = result.data.complemento;
            document.getElementById("bairro").value = result.data.bairro;
            document.getElementById("cidade").value = result.data.cidade;
            document.getElementById("estado").value = result.data.estado;
        }
        else {
            alert(result.msg);
            window.location.href = './listagem-fornecedor.php';
        }
    } catch (error){
        console.error("ERRO AO CARREGAR LISTA:", error);
    }
}

window.addEventListener("DOMContentLoaded", function() {

    const parametros = new URLSearchParams(window.location.search);

    const id_fornecedor = parametros.get("id_fornecedor");
    const id_endereco = parametros.get("id_endereco");

    carregarDadosFornecedor(id_fornecedor,id_endereco);
});

let formulario = document.getElementById("formEdicaoFornecedor");

formulario.addEventListener("submit", async function (event) {
    
    event.preventDefault();

    const formData = new FormData(formulario);

    try {
        let response = await fetch('../../actions/editar-fornecedor.php' , {
            method: 'POST',
            body: formData
        });

        let result = await response.json();

        // console.log(result);

        if(result.status == 400) {
            alert(result.msg);
        } else {
            alert(result.msg);
            window.location.href = './listagem-fornecedor.php';
        }
    } 
    catch (error){
        console.log(error);
    }
});

function formatarCpfCnpj(valor) {
    if (!valor) return "";
    const limpo = String(valor).replace(/\D/g, "");

    if (limpo.length <= 11) {
        // Máscara de CPF: 000.000.000-00
        return limpo
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d)/, '$1.$2')
            .replace(/(\d{3})(\d{1,2})$/, '$1-$2');
    } else {
        // Máscara de CNPJ: 00.000.000/0001-00
        return limpo
            .replace(/^(\d{2})(\d)/, '$1.$2')
            .replace(/^(\d{2})\.(\d{3})(\d)/, '$1.$2.$3')
            .replace(/\.(\d{3})(\d)/, '.$1/$2')
            .replace(/(\d{4})(\d)/, '$1-$2')
            .substring(0, 18);
    }
}

function formatarTelefone(valor) {
    if (!valor) return "";
    const limpo = String(valor).replace(/\D/g, "");

    if (limpo.length === 11) {
        // Celular: (00) 00000-0000
        return limpo.replace(/^(\d{2})(\d{5})(\d+)/, '($1) $2-$3');
    } else if (limpo.length === 10) {
        // Fixo: (00) 0000-0000
        return limpo.replace(/^(\d{2})(\d{4})(\d+)/, '($1) $2-$3');
    }
    
    return valor; // Retorna o original caso não tenha o tamanho padrão
}

function formatarCep(valor) {
    if(!valor) return "";
    const limpo = String(valor).replace(/\D/g, "");
    return limpo
        .replace(/^(\d{5})(\d)/, "\$1-\$2")
        .substring(0, 9);
}