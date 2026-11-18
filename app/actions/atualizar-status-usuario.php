<?php

require_once '../controller/Usuario.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = isset($_POST['id_usuario']) ? intval($_POST['id_usuario']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($id_usuario > 0) {
        // Instancia a sua controller
        $usuarioController = new Usuario();
        
        // Executa o método que criamos acima
        $resultado = $usuarioController->atualizarStatus($id_usuario, $status);

        if ($resultado === true) {
            echo json_encode(['sucesso' => true]);
            exit;
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o status no banco de dados.']);
            exit;
        }
    }
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos recebidos.']);