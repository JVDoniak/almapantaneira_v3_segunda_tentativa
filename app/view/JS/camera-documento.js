// NF-02 - Câmera de documento no cadastro de doação.
// Reaproveita o input #doc e a pré-visualização existente.
document.addEventListener('DOMContentLoaded', () => {
    const botao = document.getElementById('btnTirarFoto');
    const camera = document.getElementById('fotoDocumento');
    const documento = document.getElementById('doc');
    const mensagem = document.getElementById('mensagemFoto');
    if (!botao || !camera || !documento || !mensagem) return;

    botao.addEventListener('click', () => camera.click());

    camera.addEventListener('change', () => {
        const foto = camera.files?.[0];
        if (!foto) return;

        if (!foto.type.startsWith('image/')) {
            mensagem.textContent = 'Selecione uma imagem válida.';
            camera.value = '';
            return;
        }
        if (foto.size > 5 * 1024 * 1024) {
            mensagem.textContent = 'A foto deve ter no máximo 5 MB.';
            camera.value = '';
            return;
        }

        try {
            const arquivos = new DataTransfer();
            arquivos.items.add(foto);
            documento.files = arquivos.files;
            documento.dispatchEvent(new Event('change', { bubbles: true }));
            mensagem.textContent = 'Foto pronta para anexar: ' + foto.name;
        } catch (erro) {
            console.error('Falha ao anexar foto:', erro);
            mensagem.textContent = 'Este navegador não permitiu anexar a foto automaticamente.';
        }
        camera.value = '';
    });

    document.getElementById('removerArquivo')?.addEventListener('click', () => {
        mensagem.textContent = '';
    });
});
