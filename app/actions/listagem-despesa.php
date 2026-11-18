<?php

require_once __DIR__ . '/../controller/Despesa.php';
require_once __DIR__ . '/../controller/TipoDespesa.php';

header('Content-Type: application/json; charset=utf-8');

$objDespesa = new Despesa();

// Opções dos filtros (tudo vem do banco)
if (isset($_GET['opcoes'])) {
    echo json_encode([
        'tipos'            => (new TipoDespesa())->listarTodas(),
        'categorias'       => $objDespesa->listarCategorias(),
        'formas_pagamento' => $objDespesa->listarFormasPagamento(),
        'projetos'         => $objDespesa->listarProjetos()
    ]);
    exit;
}

// Lista de despesas com os filtros aplicados
$filtros = [
    'id_tipo_despesa'      => (int) ($_GET['id_tipo_despesa'] ?? 0),
    'nome'                 => trim($_GET['nome'] ?? ''),
    'forma_pagamento'      => trim($_GET['forma_pagamento'] ?? ''),
    'id_categoria_despesa' => (int) ($_GET['id_categoria_despesa'] ?? 0),
    'id_projeto'           => (int) ($_GET['id_projeto'] ?? 0),
    'data_inicio'          => trim($_GET['data_inicio'] ?? ''),
    'data_fim'             => trim($_GET['data_fim'] ?? '')
];

$res = $objDespesa->buscarComFiltros($filtros);

echo json_encode($res ?: []);