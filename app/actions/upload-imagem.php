<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_FILES['fotoPerfil']) && $_FILES['fotoPerfil']['error'] === 0) {

        $pasta = '../view/pages/uploads/perfis/';

        if (!is_dir($pasta)) {
            mkdir($pasta, 0777, true);
        }

        $nomeArquivo = time() . '_' . $_FILES['fotoPerfil']['name'];

        move_uploaded_file(
            $_FILES['fotoPerfil']['tmp_name'],
            $pasta . $nomeArquivo
        );

        $_SESSION['fotoPerfil'] = $pasta . $nomeArquivo;
    }

    if (isset($_POST['removerFoto'])) {

        if (
            isset($_SESSION['fotoPerfil']) &&
            file_exists($_SESSION['fotoPerfil'])
        ) {
            unlink($_SESSION['fotoPerfil']);
        }

        unset($_SESSION['fotoPerfil']);

        header("Location: edicao-perfil.php");
        exit;
    }
}
?>