document.querySelectorAll('.toggle-status').forEach(function (input) {
    input.addEventListener('change', async function () {
        const id = this.dataset.id;
        const status = this.checked ? 1 : 0;

        const formData = new FormData();
        formData.append('id', id);
        formData.append('status', status);

        try {
            const response = await fetch('../../actions/status-doacao.php', {
                method: 'POST',
                body: formData
            });

            const resultado = await response.json();

            if (!resultado.success) {
                alert(resultado.message);
                this.checked = !this.checked; // revierte el switch si falló
            }

        } catch (error) {
            alert('Erro na requisição');
            console.log(error);
            this.checked = !this.checked;
        }
    });
});