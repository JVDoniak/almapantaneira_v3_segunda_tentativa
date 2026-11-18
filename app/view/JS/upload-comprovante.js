// =====================================================
// ELEMENTOS DO UPLOAD
// =====================================================

const uploadBox = document.getElementById("uploadBox");

const arquivoInput = document.getElementById("doc");

const uploadInicial = document.getElementById("uploadInicial");

const previewArquivo = document.getElementById("previewArquivo");

const previewConteudo = document.getElementById("previewConteudo");

const nomeArquivo = document.getElementById("nomeArquivo");

const tamanhoArquivo = document.getElementById("tamanhoArquivo");

const removerArquivo = document.getElementById("removerArquivo");


// =====================================================
// ABRIR SELETOR DE ARQUIVO
// =====================================================

uploadBox.addEventListener("click", function (e) {

    // Se clicou no botão remover,
    // não abre o seletor novamente
    if (e.target.closest("#removerArquivo")) {
        return;
    }

    arquivoInput.click();

});


// =====================================================
// QUANDO SELECIONAR UM ARQUIVO
// =====================================================

arquivoInput.addEventListener("change", function () {

    const arquivo = this.files[0];

    // Se nenhum arquivo foi selecionado
    if (!arquivo) {
        return;
    }


    // =================================================
    // LIMITE DE 5 MB
    // =================================================

    const tamanhoMaximo = 5 * 1024 * 1024;

    if (arquivo.size > tamanhoMaximo) {

        alert("O arquivo não pode ter mais de 5MB.");

        arquivoInput.value = "";

        return;
    }


    // =================================================
    // NOME DO ARQUIVO
    // =================================================

    nomeArquivo.textContent = arquivo.name;


    // =================================================
    // TAMANHO DO ARQUIVO
    // =================================================

    const tamanhoMB = arquivo.size / 1024 / 1024;

    tamanhoArquivo.textContent =
        tamanhoMB.toFixed(2) + " MB";


    // =================================================
    // LIMPAR PREVIEW ANTERIOR
    // =================================================

    previewConteudo.innerHTML = "";


    // =================================================
    // PREVIEW DE IMAGEM
    // =================================================

    if (arquivo.type.startsWith("image/")) {

        const imagem = document.createElement("img");

        imagem.classList.add("preview-imagem");

        imagem.src = URL.createObjectURL(arquivo);

        imagem.alt = "Preview do arquivo";


        previewConteudo.appendChild(imagem);

    }


    // =================================================
    // PREVIEW DE PDF
    // =================================================

    else if (arquivo.type === "application/pdf") {

        const documento = document.createElement("div");

        documento.classList.add("preview-documento");

        documento.innerHTML = `
            <i class="bi bi-file-earmark-pdf"></i>
            <span>PDF</span>
        `;


        previewConteudo.appendChild(documento);

    }


    // =================================================
    // PREVIEW DE ZIP
    // =================================================

    else if (
        arquivo.name.toLowerCase().endsWith(".zip")
    ) {

        const documento = document.createElement("div");

        documento.classList.add("preview-documento");

        documento.innerHTML = `
            <i class="bi bi-file-earmark-zip"></i>
            <span>ZIP</span>
        `;


        // Alteração visual do ZIP
        documento.style.backgroundColor = "#fff8e1";
        documento.style.borderColor = "#ffe082";
        documento.style.color = "#856404";


        previewConteudo.appendChild(documento);

    }


    // =================================================
    // MOSTRAR PREVIEW
    // =================================================

    uploadInicial.style.display = "none";

    previewArquivo.style.display = "flex";

});


// =====================================================
// REMOVER ARQUIVO
// =====================================================

removerArquivo.addEventListener("click", function (e) {

    // Impede o clique de abrir o seletor
    e.stopPropagation();


    // Limpa o input
    arquivoInput.value = "";


    // Limpa preview
    previewConteudo.innerHTML = "";


    // Limpa informações
    nomeArquivo.textContent = "";

    tamanhoArquivo.textContent = "";


    // Volta para tela inicial
    previewArquivo.style.display = "none";

    uploadInicial.style.display = "flex";

});