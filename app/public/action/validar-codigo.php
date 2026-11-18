<?php

session_start();

// Quantas vezes o usuário pode errar o código antes de ter que pedir outro
const MAX_TENTATIVAS = 5;

// Depois de validar o código, quanto tempo ele tem para definir a nova senha (em minutos)
const MINUTOS_PARA_NOVA_SENHA = 10;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/recuperacao-codigo.php');
    exit;
}

if (empty($_SESSION['recuperacao'])) {
    header('Location: ../pages/recuperacao-senha.php?erro=sessao');
    exit;
}

$recuperacao = &$_SESSION['recuperacao'];

// Código expirado
if (time() > $recuperacao['expira_em']) {
    unset($_SESSION['recuperacao']);
    header('Location: ../pages/recuperacao-senha.php?erro=expirado');
    exit;
}

// Junta os 5 campos (aceita somente números)
$codigoInformado = '';
for ($i = 1; $i <= 5; $i++) {
    $codigoInformado .= preg_replace('/\D/', '', $_POST["codigo{$i}"] ?? '');
}

if (strlen($codigoInformado) !== 5) {
    header('Location: ../pages/recuperacao-codigo.php?erro=incompleto');
    exit;
}

$recuperacao['tentativas']++;

$codigoCorreto = $recuperacao['codigo'] !== null
    && hash_equals($recuperacao['codigo'], $codigoInformado);

if (!$codigoCorreto) {
    if ($recuperacao['tentativas'] >= MAX_TENTATIVAS) {
        unset($_SESSION['recuperacao']);
        header('Location: ../pages/recuperacao-senha.php?erro=tentativas');
        exit;
    }

    header('Location: ../pages/recuperacao-codigo.php?erro=invalido');
    exit;
}

// Código correto: libera a tela de nova senha
$recuperacao['validado']  = true;
$recuperacao['codigo']    = null; // o código não pode ser usado de novo
$recuperacao['expira_em'] = time() + (MINUTOS_PARA_NOVA_SENHA * 60);

session_regenerate_id(true);

header('Location: ../pages/recuperacao-nova-senha.php');
exit;
