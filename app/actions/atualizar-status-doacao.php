<?php
// atualizar-status.php

// Ajuste os caminhos de inclusão conforme a estrutura de pastas do seu projeto
require_once '../controller/Doacao.php'; // Nome do arquivo da sua controller

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_doacao = isset($_POST['id_doacao']) ? intval($_POST['id_doacao']) : 0;
    $status = isset($_POST['status']) ? intval($_POST['status']) : 0;

    if ($id_doacao > 0) {
        // Instancia a sua controller
        $doacaoController = new Doacao();
        
        // Executa o método que criamos acima
        $resultado = $doacaoController->atualizarStatus($id_doacao, $status);

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
exit;