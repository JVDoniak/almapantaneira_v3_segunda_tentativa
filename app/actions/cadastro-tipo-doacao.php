<?php

require "../controller/TipoDoacao.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $descricao = $_POST['descricao'] ?? '';

    if (empty(trim($descricao))) {
        echo json_encode([
            'success' => false,
            'message' => 'A descrição não foi informada.'
        ]);
        exit;
    }

    $objTipoDoacao = new TipoDoacao();

    $objTipoDoacao->descricao = trim($descricao);

    $res = $objTipoDoacao->cadastrar();

    echo json_encode([
        'success' => $res ? true : false,
        'message' => $res ? 'Cadastrado com sucesso' : 'Não foi possível cadastrar',
    ]);

    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método não permitido.'
]);