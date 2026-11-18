// Carrega fornecedores e projetos do banco e liga o autocompletar
async function carregarOpcoesCadastro() {
    try {
        const resposta = await fetch('../../actions/cadastro-despesa.php');
        const textoResposta = await resposta.text();

        let result;
        try {
            result = JSON.parse(textoResposta);
        } catch (e) {
            console.error("Resposta do servidor não é JSON:", textoResposta);
            alert("Não foi possível carregar fornecedores e projetos. Tente novamente.");
            return;
        }

        console.log("Fornecedores e projetos:", result);

        if (!result || !Array.isArray(result.fornecedores) || !Array.isArray(result.projetos)) {
            console.error("Resposta sem fornecedores/projetos. Confira se actions/cadastro-despesa.php e controller/Despesa.php foram atualizados.", result);
            alert("Não foi possível carregar fornecedores e projetos.");
            return;
        }

        ativarAutocomplete(
            document.getElementById("fornecedor"),
            document.getElementById("id_fornecedor"),
            document.getElementById("sugestoes_fornecedores"),
            result.fornecedores,
            "id_fornecedor"
        );

        ativarAutocomplete(
            document.getElementById("projeto"),
            document.getElementById("id_projeto"),
            document.getElementById("sugestoes_projetos"),
            result.projetos,
            "id_projeto"
        );

    } catch (error) {
        console.error("Erro crítico na requisição:", error);
    }
}

window.addEventListener("DOMContentLoaded", carregarOpcoesCadastro);

// Manipulação do Envio do Formulário de Cadastro
const formulario = document.getElementById("formCadastroDespesa");

if (formulario) {
    formulario.addEventListener("submit", async function (event) {
        event.preventDefault();

        if (!validarFormularioDespesa()) return;

        const formData = new FormData(formulario);

        try {
            const response = await fetch('../../actions/cadastro-despesa.php', {
                method: 'POST',
                body: formData
            });

            const textoResposta = await response.text();
            let result;

            try {
                result = JSON.parse(textoResposta);
            } catch (e) {
                console.error("A resposta do POST não é um JSON válido:", textoResposta);
                alert("Não foi possível salvar a despesa. Tente novamente.");
                return;
            }

            alert(result.message || result.msg);

            if (result.success) {
                window.location.href = './listagem-despesa.php';
            }
        } catch (error) {
            console.error("Erro ao cadastrar despesa:", error);
        }
    });
}

function validarFormularioDespesa() {
    const nome = document.getElementById("nome");
    if (!nome.value.trim()) {
        alert('O campo "Nome da Despesa" não pode ficar vazio.');
        nome.focus();
        return false;
    }

    const fornecedor = document.getElementById("fornecedor");
    const idFornecedor = document.getElementById("id_fornecedor");
    if (fornecedor.value.trim() && !idFornecedor.value) {
        alert("Selecione um fornecedor válido da lista.");
        fornecedor.focus();
        return false;
    }

    const projeto = document.getElementById("projeto");
    if (projeto.value.trim() && !document.getElementById("id_projeto").value) {
        alert("Selecione um projeto da lista.");
        projeto.focus();
        return false;
    }

    if (!document.getElementById("id_categoria_despesa").value) {
        alert("Selecione a categoria da despesa.");
        return false;
    }

    const valor = document.getElementById("valor");
    if (!valor.value || parseFloat(valor.value) <= 0) {
        alert('Informe um valor válido.');
        valor.focus();
        return false;
    }

    const vencimento = document.getElementById("data_vencimento");
    if (!vencimento.value) {
        alert('Informe a data de vencimento.');
        vencimento.focus();
        return false;
    }

    const pagamento = document.getElementById("forma_pagamento");
    if (!pagamento.value) {
        alert('Selecione a forma de pagamento.');
        pagamento.focus();
        return false;
    }

    return true;
}

// =====================================================
// AUTOCOMPLETAR (fornecedor e projeto)
// Ao clicar no campo já mostra a lista do banco.
// Enquanto digita, a lista vai filtrando.
// Setas ↑ ↓ para navegar, Enter para escolher, Esc para fechar.
// =====================================================

function normalizarTexto(texto) {
    return String(texto || "")
        .normalize("NFD")
        .replace(/[̀-ͯ]/g, "")
        .toLowerCase()
        .trim();
}

function itemInativo(item) {
    const status = item.status_fornecedor ?? item.status_projeto;
    return status !== undefined && status !== null && Number(status) === 0;
}

function ativarAutocomplete(campoTexto, campoId, lista, itens, chaveId) {
    if (!campoTexto || !campoId || !lista) return;
    campoTexto._itensCadastro = itens;
    if (campoTexto._cadastroLigado) return;
    campoTexto._cadastroLigado = true;
    if (!campoTexto || !campoId || !lista) return;

    let encontrados = [];
    let posicao = -1;

    lista.style.cssText = "display:none; position:absolute; top:100%; left:0; right:0; z-index:50; background:#fff; border:1px solid #d1d5db; border-radius:8px; max-height:240px; overflow-y:auto; box-shadow:0 6px 16px rgba(0,0,0,.08);";

    function fechar() {
        lista.style.display = "none";
        lista.innerHTML = "";
        encontrados = [];
        posicao = -1;
    }

    function escolher(item) {
        campoTexto.value = item.nome;
        campoId.value = item[chaveId];
        fechar();
    }

    function destacar() {
        Array.from(lista.children).forEach((el, i) => {
            el.style.background = i === posicao ? "#eef2ff" : "#fff";
        });
        if (lista.children[posicao]) {
            lista.children[posicao].scrollIntoView({ block: "nearest" });
        }
    }

    function mostrarSugestoes() {
        lista.innerHTML = "";
        posicao = -1;

        const texto = normalizarTexto(campoTexto.value);

        // Sem texto: mostra todos. Com texto: filtra.
        // Ordem: quem começa com o texto, depois ativos, depois nome.
        encontrados = campoTexto._itensCadastro
            .filter(item => !texto || normalizarTexto(item.nome).includes(texto))
            .sort((a, b) => {
                const aComeca = texto && normalizarTexto(a.nome).startsWith(texto) ? 0 : 1;
                const bComeca = texto && normalizarTexto(b.nome).startsWith(texto) ? 0 : 1;
                return (aComeca - bComeca)
                    || (itemInativo(a) - itemInativo(b))
                    || String(a.nome).localeCompare(String(b.nome));
            })
            .slice(0, 50);

        if (encontrados.length === 0) {
            const vazio = campoTexto._itensCadastro.length === 0
                ? "Nenhum cadastro encontrado no banco"
                : "Nenhum resultado para \"" + campoTexto.value.trim() + "\"";
            lista.innerHTML = '<div style="padding:10px 12px; color:#6b7280;">' + vazio + '</div>';
            lista.style.display = "block";
            return;
        }

        encontrados.forEach((item) => {
            const div = document.createElement("div");
            div.textContent = item.nome + (itemInativo(item) ? " (inativo)" : "");
            div.style.cssText = "padding:10px 12px; cursor:pointer;" + (itemInativo(item) ? " color:#9ca3af;" : "");

            // mousedown roda antes do blur do campo
            div.addEventListener("mousedown", function (e) {
                e.preventDefault();
                escolher(item);
            });

            lista.appendChild(div);
        });

        lista.style.display = "block";
    }

    campoTexto.addEventListener("focus", mostrarSugestoes);

    campoTexto.addEventListener("input", function () {
        campoId.value = "";
        mostrarSugestoes();
    });

    campoTexto.addEventListener("keydown", function (e) {
        if (lista.style.display === "none" || encontrados.length === 0) return;

        if (e.key === "ArrowDown") {
            e.preventDefault();
            posicao = (posicao + 1) % encontrados.length;
            destacar();
        } else if (e.key === "ArrowUp") {
            e.preventDefault();
            posicao = posicao <= 0 ? encontrados.length - 1 : posicao - 1;
            destacar();
        } else if (e.key === "Enter") {
            e.preventDefault();
            escolher(encontrados[posicao >= 0 ? posicao : 0]);
        } else if (e.key === "Escape") {
            fechar();
        }
    });

    // Ao sair do campo: se o texto for exatamente um nome da lista, já seleciona
    campoTexto.addEventListener("blur", function () {
        if (!campoId.value && campoTexto.value.trim()) {
            const exato = campoTexto._itensCadastro.find(item => normalizarTexto(item.nome) === normalizarTexto(campoTexto.value));
            if (exato) {
                campoTexto.value = exato.nome;
                campoId.value = exato[chaveId];
            }
        }
        fechar();
    });
}
