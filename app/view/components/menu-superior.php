<header class="header">
    <nav class="header-right" aria-label="Menu superior">
        <a href="dashboard.php" class="dashboard">
            <i class="bi bi-grid-1x2-fill"></i>
            <span>Dashboard</span>
        </a>

        <a href="#" class="relatorio">
            <i class="bi bi-file-earmark-text-fill"></i>
            Relatórios
        </a>

        <a href="perfil.php?id=<?= $_SESSION['usuario_id']; ?>" class="perfil">
            <i class="bi bi-person-fill"></i>
            <span>Perfil</span>
        </a>

        <a href="../../public/action/logout.php" class="menu-item">
            <i class="bi bi-box-arrow-right"></i>
            <span>Sair</span>
        </a>

    </nav>
</header>