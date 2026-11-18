async function carregarDadosDespesa(id_despesa) {
    if (!id_despesa) {
        alert("ID da despesa não informado na URL!");
        window.location.href = './listagem-despesa.php';
        return;
    }

    try {
        const resposta = await fetch(`../../actions/editar-despesa.php?id_despesa=${id_despesa}`);
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
            const despesa = result.data;
            console.log("Dados recebidos para preenchimento:", despesa);

            // 1. ID Oculto
            const inputIdDespesa = document.getElementById("id_despesa");
            if (inputIdDespesa) inputIdDespesa.value = despesa.id_despesa || "";

            // Tipo e categoria são campos independentes; NULL legado permanece vazio.
            document.getElementById("id_categoria_despesa").value = despesa.id_categoria_despesa ?? "";
            document.getElementById("id_categoria_despesa").required = despesa.id_categoria_despesa !== null;
            document.getElementById("id_tipo_despesa").value = despesa.id_tipo_despesa ?? "";

            // 3. Fornecedor (texto + id)
            const inputFornecedor = document.getElementById("fornecedor");
            if (inputFornecedor) inputFornecedor.value = despesa.nome_fornecedor || "";

            const inputIdFornecedor = document.getElementById("id_fornecedor");
            if (inputIdFornecedor) inputIdFornecedor.value = despesa.id_fornecedor || "";

            // 4. Projeto (texto + id)
            const inputProjeto = document.getElementById("projeto");
            if (inputProjeto) inputProjeto.value = despesa.nome_projeto || "";

            const inputIdProjeto = document.getElementById("id_projeto");
            if (inputIdProjeto) inputIdProjeto.value = despesa.id_projeto || "";

            // 5. Campos Comuns
            const inputNome = document.getElementById("nome");
            if (inputNome) inputNome.value = despesa.nome || "";

            const inputValor = document.getElementById("valor");
            if (inputValor) inputValor.value = despesa.valor || "";

            const inputVencimento = document.getElementById("data_vencimento");
            if (inputVencimento) inputVencimento.value = despesa.data_vencimento || "";

            const inputDescricao = document.getElementById("descricao");
            if (inputDescricao) inputDescricao.value = despesa.descricao || "";

            // 6. Forma de Pagamento
            const inputPagamento = document.getElementById("forma_pagamento");
            if (inputPagamento) {
                inputPagamento.value = despesa.forma_pagamento || "";
                // Se a forma de pagamento não existir no select, cria a opção de forma limpa
                if (!inputPagamento.value && despesa.forma_pagamento) {
                    const optionPagamento = document.createElement('option');
                    optionPagamento.value = despesa.forma_pagamento;
                    optionPagamento.selected = true;
                    optionPagamento.innerText = despesa.forma_pagamento;
                    inputPagamento.appendChild(optionPagamento);
                }
            }

            // 7. Liga o autocompletar com os dados do banco
            ativarAutocomplete(
                inputFornecedor,
                inputIdFornecedor,
                document.getElementById("sugestoes_fornecedores"),
                result.fornecedores || [],
                "id_fornecedor"
            );

            ativarAutocomplete(
                inputProjeto,
                inputIdProjeto,
                document.getElementById("sugestoes_projetos"),
                result.projetos || [],
                "id_projeto"
            );

            document.getElementById("salvarDespesa").disabled = false;
            console.log("Preenchimento concluído com sucesso!");

        } else {
            alert("Erro ao carregar dados da despesa.");
            window.location.href = './listagem-despesa.php';
        }
    } catch (error) {
        console.error("Erro crítico na requisição:", error);
    }
}

window.addEventListener("DOMContentLoaded", function () {
    const parametros = new URLSearchParams(window.location.search);
    const id_despesa = parametros.get("id_despesa");
    carregarDadosDespesa(id_despesa);
});

// Manipulação do Envio do Formulário de Edição
const formulario = document.getElementById("formEdicaoDespesa");

if (formulario) {
    formulario.addEventListener("submit", async function (event) {
        event.preventDefault();
        if (document.getElementById("salvarDespesa").disabled) return;

        const fornecedor = document.getElementById("fornecedor");
        if (fornecedor.value.trim() && !document.getElementById("id_fornecedor").value) {
            alert("Selecione um fornecedor da lista.");
            fornecedor.focus();
            return;
        }

        const projeto = document.getElementById("projeto");
        if (projeto.value.trim() && !document.getElementById("id_projeto").value) {
            alert("Selecione um projeto da lista.");
            projeto.focus();
            return;
        }


        const formData = new FormData(formulario);

        try {
            const response = await fetch('../../actions/editar-despesa.php', {
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

            alert(result.msg || result.message);

            if (result.status === 200 || result.success) {
                window.location.href = './listagem-despesa.php';
            }
        } catch (error) {
            console.error("Erro no envio:", error);
        }
    });
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
        encontrados = itens
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
            const vazio = itens.length === 0
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
            const exato = itens.find(item => normalizarTexto(item.nome) === normalizarTexto(campoTexto.value));
            if (exato) {
                campoTexto.value = exato.nome;
                campoId.value = exato[chaveId];
            }
        }
        fechar();
    });
}
