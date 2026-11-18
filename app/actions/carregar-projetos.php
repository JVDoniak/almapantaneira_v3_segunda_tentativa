<?php

require '../controller/Projeto.php';

header('Content-Type: application/json');

$objProjeto = new Projeto();

$res = $objProjeto->buscarParaSelect();

echo json_encode($res ?: []);
