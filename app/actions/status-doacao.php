<?php
require '../controller/Doacao.php';
header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $id     = $_POST['id'] ?? '';
    $status = $_POST['status'] ?? '';

    $objDoacao = new Doacao();
    $res = $objDoacao->alterarStatus($id, $status);

    echo json_encode([
        'success' => $res,
        'message' => $res ? 'Status atualizado' : 'Erro ao atualizar status'
    ]);
    exit;
}