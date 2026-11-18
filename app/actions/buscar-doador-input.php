<?php


require_once __DIR__ . '/../controller/Doador.php';

header('Content-Type: application/json; charset=utf-8');


// GET: devolve os fornecedores e projetos do banco para o autocompletar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $objDoador = new Doador();

    echo json_encode([
        'success'      => true,
        'doadores' => $objDoador->listarTodos()
    ]);
    exit;
}
