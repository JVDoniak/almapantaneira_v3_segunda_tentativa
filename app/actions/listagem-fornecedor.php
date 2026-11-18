<?php

require_once "../controller/Fornecedor.php";

// Define que a resposta será SEMPRE um JSON
header('Content-Type: application/json; charset=utf-8');

// Captura e limpa os filtros da requisição (PHP 7.4+)
$nome   = trim($_GET['nome_fornecedor'] ?? '');
$cnpj   = trim($_GET['cpf_cnpj'] ?? '');
$status = trim($_GET['status'] ?? '');

$objFornecedor = new Fornecedor();

// Realiza a busca no banco de dados
$res = $objFornecedor->buscar($nome, $cnpj, $status);

// Garante o retorno de um array válido e previne falhas de codificação
echo json_encode($res ?: [], JSON_UNESCAPED_UNICODE | JSON_INVALID_UTF8_SUBSTITUTE);
