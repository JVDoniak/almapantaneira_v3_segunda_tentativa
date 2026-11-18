<?php
require '../controller/Usuario.php';
header('Content-Type: application/json');

if(isset($_POST) && isset($_POST['informacaoPerfil'])){

    $objUsuario = new Usuario();
    $objUsuario->id_usuario = $_POST['id_usuario'];
    $objUsuario->nome = $_POST['nome'] ?? '';
    $objUsuario->telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');
    $senhaDigitada = $_POST['confirmarSenha'] ?? '';
    if ($senhaDigitada !== '1234') {
        // O usuário deseja alterar a senha
        $objUsuario->senha = password_hash($senhaDigitada, PASSWORD_DEFAULT);
    } else {
        // Mantém a senha atual
        $objUsuario->senha = null;
    }

    $path = ''; //variável com o caminho para salvar a foto

        // Processa o envio da foto, caso exista
    if (isset($_FILES['uploadFoto']) &&
        $_FILES['uploadFoto']['error'] !== UPLOAD_ERR_NO_FILE) {

        $arquivo = $_FILES['uploadFoto'];

        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            throw new Exception('Falha ao enviar a foto.');
        }

        $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'jfif'];

        $extensao = strtolower(
            pathinfo($arquivo['name'], PATHINFO_EXTENSION)
        );

        if (!in_array($extensao, $extensoesPermitidas, true)) {
            throw new Exception(
                'Formato de foto inválido. Utilize JPG, JPEG, PNG ou JFIF.'
            );
        }

        // Limita o tamanho do arquivo a 5 MB
        if ($arquivo['size'] > 8 * 1024 * 1024) {
            throw new Exception('A foto deve ter no máximo 5 MB.');
        }

        // Diretório de destino
        $pasta = $_SERVER['DOCUMENT_ROOT'] . '/almapantaneira/app/uploads/foto-perfil/';
        
        if (!is_dir($pasta)) {
            mkdir($pasta, 0755, true);
        }

        // Gera um nome único para o arquivo
        $novo_nome = 'foto-user-'. uniqid() . '.' . $extensao;

        $path = $pasta . $novo_nome;

        if (!move_uploaded_file(
            $arquivo['tmp_name'],
            $path
        )) {
            throw new Exception('Não foi possível salvar a foto.');
        }

        // Caminho público armazenado no banco de dados
        $objUsuario->foto =
            '/almapantaneira/app/uploads/foto-perfil/'
            . $novo_nome;
    }

    try{
        $res = $objUsuario->editarPerfil();
        $array = [
            "status" => 200,
            "msg" => "Usuário editado com sucesso!"
        ];
        echo json_encode($array);
    }
    catch(Exception $err){
        
        $array = [
            "status" => 400,
            "msg" => $err
        ];

        echo json_encode($array);
    }
}