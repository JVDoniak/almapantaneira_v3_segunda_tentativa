<?php

require_once "../controller/Doacao.php";

header("Content-Type: application/json; charset=utf-8");


$objDoacao = new Doacao();

$res = $objDoacao->buscarDoacoes();
// $res = $objDoacao->buscarPorStatus();

echo json_encode($res);