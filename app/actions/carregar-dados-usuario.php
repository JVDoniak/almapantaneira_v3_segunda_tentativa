<?php
require_once "../public/includes/autenticacao.php";
require '../controller/Usuario.php';

$objUsuario = new Usuario();

$res = $objUsuario->buscar_por_id();

echo json_encode($res);