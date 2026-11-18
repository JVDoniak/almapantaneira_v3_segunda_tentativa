<?php


require_once __DIR__ . '/../controller/Doador.php';

header('Content-Type: application/json; charset=utf-8');

$filtros = [
    'nome'   => $_GET['nome'] ?? '',
    'cpf_cnpj'    => $_GET['cpf'] ?? '',
    'tipo'   => $_GET['tipo'] ?? '',
    'status_doador' =>  $_GET['status'] === "" ? 1 : intval($_GET['status'])
];

$objDoador = new Doador();
$res = $objDoador->listarComFiltros($filtros);

echo json_encode($res);