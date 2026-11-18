/**
 * Controle do Menu Flyout da Sidebar
 * Abre ao clicar no item pai e fecha ao clicar fora
 */
document.addEventListener('DOMContentLoaded', () => {
    // CORREÇÃO: Agora mapeia a classe correta configurada no seu HTML/CSS (.menu-item)
    const flyoutContainers = document.querySelectorAll('.menu-item');

    flyoutContainers.forEach(container => {
        // Seleciona o link principal (botão) dentro deste contêiner
        const menuButton = container.querySelector('.borderButtons');

        if (menuButton) {
            menuButton.addEventListener('click', (event) => {
                // Impede que o link navegue/recarregue a página antes de abrir o menu
                event.preventDefault();

                // Fecha qualquer outro menu flyout que esteja aberto
                flyoutContainers.forEach(otherContainer => {
                    if (otherContainer !== container) {
                        otherContainer.classList.remove('active');
                    }
                });

                // Alterna a classe 'active' no contêiner atual (abre/fecha)
                container.classList.toggle('active');
            });
        }
    });

    // ------------------------------------------------------------------
    // Recolher / expandir a sidebar (a escolha fica salva no navegador)
    // ------------------------------------------------------------------
    const sidebar = document.getElementById('sidebar');
    const btnToggle = document.getElementById('btnToggleSidebar');

    function aplicarEstadoSidebar(recolhida) {
        sidebar.classList.toggle('recolhida', recolhida);

        const texto = recolhida ? 'Expandir menu' : 'Recolher menu';
        btnToggle.setAttribute('aria-expanded', String(!recolhida));
        btnToggle.setAttribute('aria-label', texto);
        btnToggle.title = texto;

        // Recolhida: o nome do item aparece ao passar o mouse no ícone
        sidebar.querySelectorAll('.borderButtons').forEach((link) => {
            const nome = link.querySelector('.menu-texto')?.textContent.trim() || '';
            if (recolhida) {
                link.title = nome;
                link.setAttribute('aria-label', nome);
            } else {
                link.removeAttribute('title');
                link.removeAttribute('aria-label');
            }
        });

        // Gráficos (Chart.js) e tabelas se ajustam à nova largura
        setTimeout(() => window.dispatchEvent(new Event('resize')), 260);
    }

    if (sidebar && btnToggle) {
        aplicarEstadoSidebar(sidebar.classList.contains('recolhida'));

        btnToggle.addEventListener('click', (event) => {
            event.stopPropagation();
            const recolhida = !sidebar.classList.contains('recolhida');
            aplicarEstadoSidebar(recolhida);
            flyoutContainers.forEach((c) => c.classList.remove('active'));

            try {
                localStorage.setItem('sidebarRecolhida', recolhida ? '1' : '0');
            } catch (e) { /* sem localStorage: só não lembra a escolha */ }
        });
    }

    // Escuta cliques na página inteira
    document.addEventListener('click', (event) => {
        flyoutContainers.forEach(container => {
            // Se o clique foi FORA do contêiner do menu, remove a classe 'active' para fechar
            if (!container.contains(event.target)) {
                container.classList.remove('active');
            }
        });
    });
});