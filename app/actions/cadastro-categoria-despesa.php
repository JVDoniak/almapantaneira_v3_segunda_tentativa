<?php

require "../controller/CategoriaDespesa.php";

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == 'POST') {

    $descricao = $_POST['descricao'] ?? '';

    if (!is_string($descricao) || empty(trim($descricao)) || mb_strlen(trim($descricao), 'UTF-8') > 100) {
        echo json_encode([
            'success' => false,
            'message' => 'Informe uma descrição de categoria com até 100 caracteres.'
        ]);
        exit;
    }

    $objCatedoriaDespesa = new CategoriaDespesa();

    $objCatedoriaDespesa->descricao = trim($descricao);

    $res = $objCatedoriaDespesa->cadastrar();

    echo json_encode([
        'success' => $res ? true : false,
        'categoria' => $res ? ['id_categoria_despesa' => (int) $res, 'descricao' => $objCatedoriaDespesa->descricao] : null,
        'message' => $res ? 'Cadastrado com sucesso' : 'Não foi possível cadastrar',
    ]);

    exit;
}

echo json_encode([
    'success' => false,
    'message' => 'Método não permitido.'
]);
