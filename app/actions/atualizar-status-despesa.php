<?php
// atualizar-status.php

// Ajuste os caminhos de inclusão conforme a estrutura de pastas do seu projeto
require_once '../controller/Despesa.php'; // Nome do arquivo da sua controller

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_despesa = isset($_POST['id_despesa']) ? intval($_POST['id_despesa']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($id_despesa > 0) {
        // Instancia a sua controller
        $despesaController = new Despesa();
        
        // Executa o método que criamos acima
        $resultado = $despesaController->atualizarStatus($id_despesa, $status);

        if ($resultado !== false) {
            echo json_encode(['sucesso' => true]);
            exit;
        } else {
            echo json_encode(['sucesso' => false, 'mensagem' => 'Erro ao atualizar o status no banco de dados.']);
            exit;
        }
    }
}

echo json_encode(['sucesso' => false, 'mensagem' => 'Dados inválidos recebidos.']);