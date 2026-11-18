<?php

session_start();

require '../../controller/Login.php';

// Tempo para o usuário de primeiro acesso criar a nova senha (em minutos)
const MINUTOS_PRIMEIRO_ACESSO = 100;

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $email = $_POST['email'] ?? '';
    $senha = $_POST['senha'] ?? '';

    $objLogin = new Login();
    $objLogin->email = $email;
    $objLogin->senha = $senha;

    $usuario = $objLogin->entrar();

    if ($usuario) {

        session_regenerate_id(true);

        // Primeiro acesso: NÃO entra no sistema ainda.
        // Libera a tela de nova senha (a mesma da recuperação) e manda para lá.
        if ($objLogin->precisaTrocarSenha($usuario)) {
            unset($_SESSION["usuario_id"], $_SESSION["usuario_nome"], $_SESSION["usuario_email"]);

            $_SESSION['recuperacao'] = [
                'email'             => $usuario['email'],
                'id_user_recuperar' => $usuario['id_usuario'],
                'codigo'            => null,
                'tentativas'        => 0,
                'validado'          => true, // a senha atual já foi conferida no login
                'expira_em'         => time() + (MINUTOS_PRIMEIRO_ACESSO * 60),
                'primeiro_acesso'   => true,
            ];

            header("Location: ../pages/recuperacao-nova-senha.php");
            exit;
        }

        $_SESSION["usuario_id"] = $usuario["id_usuario"];
        $_SESSION["usuario_nome"] = $usuario["nome"];
        $_SESSION["usuario_email"] = $usuario["email"];
        $_SESSION['ultimo_acesso'] = time(); // Armazena o timestamp atual

        header("Location: ../../view/pages/dashboard.php");
        exit;

    } else {
        header("Location: ../pages/login.php?erro=1");
        exit;
    }
}