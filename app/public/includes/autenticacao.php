<?php
session_start();

// Tempo limite de inatividade em segundos (exemplo: 15 minutos = 900 segundos)
// $tempo_limite = 60; 

if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../../pages/login.php");
    exit;
}

// Verifica se o tempo limite foi excedido por inatividade
// if (isset($_SESSION['ultimo_acesso']) && (time() - $_SESSION['ultimo_acesso'] > $tempo_limite)) {
//     // Destrói a sessão e redireciona para o login
//     session_unset();
//     session_destroy();
//     header("Location: ../../pages/login.php?erro=expirado");
//     exit;
// }

// // Atualiza o tempo do último acesso com o momento atual
// $_SESSION['ultimo_acesso'] = time();