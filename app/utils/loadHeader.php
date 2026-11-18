<?php 
function loadHeader($title, $scriptsExtras = []) {
    // Se por acaso vier nulo por algum motivo, garantimos que vira array
    if (!is_array($scriptsExtras)) {
        $scriptsExtras = [$scriptsExtras]; 
    }

    // Inclui a autenticação primeiro para garantir que executa sessões ou redirecionamentos antes de gerar HTML
    require_once '../../public/includes/autenticacao.php';

    // Inicia o buffer de saída para capturar o sidebar
    ob_start();
    include "../components/sidebar.php";
    $sidebar = ob_get_clean();

    // Inicia o buffer de saída para capturar o menu superior
    ob_start();
    include "../components/menu-superior.php";
    $menuSuperior = ob_get_clean();
    
    $scriptsHtml = '';

    foreach ($scriptsExtras as $script) {
        // echo '<script> ' . $script . '</script>';
        $scriptsHtml .= '<script src="' . $script . '" defer></script>' . "\n";
    }

    // Retorna a string HTML limpa concatenada com os componentes capturados
    return '<!DOCTYPE html> 
    <html lang="pt-br"> 
    <head> 
        <meta charset="UTF-8"> 
        <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
        <title>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</title> 
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/global.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/layout.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/forms.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/buttons.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/perfil.css"> 
        <link rel="stylesheet" href="../css/estilo-padrao/listagem.css">
        <link rel="stylesheet" href="../css/estilo-padrao/modal-categoria.css">
        <link rel="stylesheet" href="../css/dashboard.css">
        <script defer src="../js/sidebar.js"></script> ' . 
        $scriptsHtml . '
    </head> 
    <body> 
        <div class="app-layout"> ' 
            . $sidebar . 
            ' <div class="app-main"> ' 
            . $menuSuperior . 
            ' <main class="main-content">'; 
}