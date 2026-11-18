<?php

session_start();

require_once __DIR__ . '/../../controller/Usuario.php';
require_once __DIR__ . '/../../controller/Email.php';

// Validade do código (em minutos)
const MINUTOS_VALIDADE_CODIGO = 15;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/recuperacao-senha.php');
    exit;
}

$email = trim($_POST['email'] ?? '');

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    header('Location: ../pages/recuperacao-senha.php?erro=email');
    exit;
}

// Descarta qualquer recuperação anterior
unset($_SESSION['recuperacao']);

$usuario = new Usuario();
$usuario->email = $email;

$codigo = null;
$userRecuperacao = $usuario->verificarEmail();
if ($userRecuperacao) {
    $codigo = (string) random_int(10000, 99999);

    $emailService = new Email();
    $enviado = $emailService->enviarCodigoRecuperacao($email, $codigo, MINUTOS_VALIDADE_CODIGO);

    if (!$enviado) {
        header('Location: ../pages/recuperacao-senha.php?erro=envio');
        exit;
    }
}

$_SESSION['recuperacao'] = [
    'email'      => $email,
    'codigo'     => $codigo,
    'expira_em'  => time() + (MINUTOS_VALIDADE_CODIGO * 60),
    'id_user_recuperar' => $userRecuperacao['id_usuario'],
    'tentativas' => 0,
    'validado'   => false,
];

header('Location: ../pages/recuperacao-codigo.php');
exit;
