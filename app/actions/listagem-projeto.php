<?php

require '../controller/Projeto.php';

header('Content-Type: application/json; charset=utf-8');

$nome        = trim($_GET['nome_projeto'] ?? '');
$data_inicio = $_GET['data_inicio'] ?? '';
$tipo        = $_GET['tipo_projeto'] ?? '';
$status      = $_GET['status'] ?? '';

$objProjeto = new Projeto();

echo json_encode($objProjeto->buscarProjetosFiltrados($nome, $data_inicio, $tipo, $status));