// ==========================================================
// ESTILO DAS SUGESTÕES
// ==========================================================

const estiloSugestoes = document.createElement("style");

estiloSugestoes.innerHTML = `
        .autocomplete-container {
            position: relative;
        }

        .autocomplete-lista {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: #ffffff;
            border: 1px solid #cccccc;
            border-top: none;
            border-radius: 0 0 6px 6px;
            max-height: 200px;
            overflow-y: auto;
            z-index: 9999;
            display: none;
            box-shadow: 0 3px 8px rgba(0,0,0,0.15);
        }

        .autocomplete-item {
            padding: 12px;
            cursor: pointer;
            background: #ffffff;
            color: #222222;
            font-size: 14px;
        }

        .autocomplete-item:hover {
            background: #f1f1f1;
        }
    `;

document.head.appendChild(estiloSugestoes);

// CADASTRAR DOAÇÃO
document.getElementById("formCadastro").addEventListener('submit', async function(event){
    event.preventDefault();

    const idDoador = document.getElementById("id_doador").value;
    if (!/^[1-9]\d*$/.test(idDoador) || !document.getElementById("nome_doador").value.trim()) {
        alert("Selecione um doador da lista antes de salvar a doação.");
        document.getElementById("nome_doador").focus();
        return;
    }
    
    const formData = new FormData(this);

    try {
        // Mude a URL aqui para apontar para o seu Controller (adicionei a action de cadastrar)
        const response = await fetch('../../actions/cadastro-doacao.php', {
            method: 'POST',
            body: formData
        });
        
        const resultado = await response.json();
        alert(resultado.message);
    } catch (error) {
        alert("Erro ao cadastrar doação.");
        console.log(error);
    }
});



document.addEventListener('DOMContentLoaded', async function () {

        try {
        const resposta = await fetch("../../actions/buscar-doador-input.php");
        const textoResposta = await resposta.text();

        let result;
        try {
            result = JSON.parse(textoResposta);
        } catch (e) {
            console.error("Resposta do servidor não é JSON:", textoResposta);
            alert("Não foi possível carregar os doadores. Tente novamente.");
            return;
        }

        console.log("Doadores:", result);

        if (!result || !Array.isArray(result.doadores)) {
            console.error("Resposta sem doadores.", result);
            alert("Não foi possível carregar os doadores. Tente novamente.");
            return;
        }

        ativarAutocomplete(
            document.getElementById("nome_doador"),
            document.getElementById("id_doador"),
            document.getElementById("sugestoes_doadores"),
            result.doadores,
            "id_doador"
        );

    } catch (error) {
        console.error("Erro crítico na requisição:", error);
    }
   
});



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
    const status = item.status_doador;
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
