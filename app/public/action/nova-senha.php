<?php

session_start();

require_once __DIR__ . '/../../controller/Usuario.php';

const TAMANHO_MINIMO_SENHA = 6;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../pages/recuperacao-nova-senha.php');
    exit;
}

$recuperacao = $_SESSION['recuperacao'] ?? null;

// Só pode trocar a senha quem validou o código (ou entrou no primeiro acesso) e dentro do prazo
if (empty($recuperacao['validado']) || time() > $recuperacao['expira_em']) {
    $primeiroAcesso = !empty($recuperacao['primeiro_acesso']);
    unset($_SESSION['recuperacao']);
    header($primeiroAcesso
        ? 'Location: ../pages/login.php?erro=expirado'
        : 'Location: ../pages/recuperacao-senha.php?erro=expirado');
    exit;
}

$senha          = $_POST['senha'] ?? '';
$confirmarSenha = $_POST['confirmar_senha'] ?? '';

if (strlen($senha) < TAMANHO_MINIMO_SENHA) {
    header('Location: ../pages/recuperacao-nova-senha.php?erro=tamanho');
    exit;
}

if ($senha !== $confirmarSenha) {
    header('Location: ../pages/recuperacao-nova-senha.php?erro=confirmacao');
    exit;
}

$usuario = new Usuario();
$usuario->email = $recuperacao['email'];
$usuario->senha = password_hash($confirmarSenha, PASSWORD_DEFAULT);
$id_usuario_alterar_senha = $_SESSION['recuperacao']['id_user_recuperar'];
$usuarioAtualizar = $usuario->atualizarSenha($id_usuario_alterar_senha);

if (!$usuarioAtualizar) {
    header('Location: ../pages/recuperacao-nova-senha.php?erro=banco');
    exit;
}

// Fim do fluxo: limpa os dados da recuperação
unset($_SESSION['recuperacao']);

header('Location: ../pages/login.php?sucesso=senha');
exit;