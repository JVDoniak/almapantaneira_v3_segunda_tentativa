<aside class="sidebar" id="sidebar">
    <button type="button" class="sidebar-toggle" id="btnToggleSidebar"
            aria-controls="sidebar" aria-expanded="true" aria-label="Recolher menu" title="Recolher menu">
        <i class="bi bi-chevron-double-left"></i>
    </button>

    <div class="logo">
        <a href="dashboard.php">
            <img src="../imgs/logo-alma-pantaneira.png" alt="Logo Alma Pantaneira">
        </a>
    </div>

    <nav class="menu" aria-label="Menu principal">
        <div class="divnav">
            <div class="menu-item">
                <a href="cadastro-doador.php" class="borderButtons doador">
                    <i class="bi bi-people-fill"></i>
                    <span class="menu-texto">Doadores</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Doadores</span>
                    <a href="cadastro-doador.php">Novo Doador</a>
                    <a href="listagem-doador.php">Listar Doadores</a>
                </div>
            </div>

            <div class="menu-item">
                <a href="cadastro-doacao.php" class="borderButtons doacao">
                    <i class="bi bi-heart-fill"></i>
                    <span class="menu-texto">Doações</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Doações</span>
                    <a href="cadastro-doacao.php">Nova Doação</a>
                    <a href="listagem-doacao.php">Listar Doações</a>
                    <a href="conciliacao-doacao.php">Conciliação de Doações</a>
                </div>
            </div>

            <div class="menu-item">
                <a href="cadastro-despesa.php" class="borderButtons despesa">
                    <i class="bi bi-wallet-fill"></i>
                    <span class="menu-texto">Despesas</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Despesas</span>
                    <a href="cadastro-despesa.php">Nova Despesa</a>
                    <a href="listagem-despesa.php">Listar Despesas</a>
                    <a href="conciliacao-despesas.php">Conciliação de Despesas</a>
                </div>
            </div>

            <div class="menu-item">
                <a href="cadastro-fornecedor.php" class="borderButtons fornecedor">
                    <i class="bi bi-wrench-adjustable"></i>
                    <span class="menu-texto">Fornecedores</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Fornecedores</span>
                    <a href="cadastro-fornecedor.php">Novo Fornecedor</a>
                    <a href="listagem-fornecedor.php">Listar Fornecedores</a>
                </div>
            </div>

            <div class="menu-item">
                <a href="cadastro-usuario.php" class="borderButtons usuario">
                    <i class="bi bi-person-fill"></i>
                    <span class="menu-texto">Usuários</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Usuários</span>
                    <a href="cadastro-usuario.php">Novo Usuário</a>
                    <a href="listagem-usuario.php">Listar Usuários</a>
                </div>
            </div>

            <div class="menu-item">
                <a href="cadastro-projeto.php" class="borderButtons projeto">
                    <i class="bi bi-folder-fill"></i>
                    <span class="menu-texto">Projetos</span>
                </a>
                <div class="flyout-menu">
                    <span class="flyout-titulo">Projetos</span>
                    <a href="cadastro-projeto.php">Novo Projeto</a>
                    <a href="listagem-projeto.php">Listar Projetos</a>
                    <a href="relatorio-projeto.php">Relatórios</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="logo-bottom">
        <img src="../imgs/logo-medicos-do-pantanal.png" alt="Logo Médicos do Pantanal">
    </div>
</aside>
<script>
    // Aplica o estado salvo (recolhido/expandido) antes da página aparecer, sem animação
    (function () {
        try {
            if (localStorage.getItem("sidebarRecolhida") === "1") {
                var sidebar = document.getElementById("sidebar");
                sidebar.classList.add("recolhida", "sem-transicao");
                setTimeout(function () { sidebar.classList.remove("sem-transicao"); }, 50);
            }
        } catch (e) { /* navegador sem localStorage: fica expandido */ }
    })();
</script>