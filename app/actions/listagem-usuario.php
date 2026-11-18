<?php

require "../controller/Usuario.php";

header('Content-Type: application/json; charset=utf-8');

$nome  = isset($_GET['buscar_usuario']) ? trim($_GET['buscar_usuario']) : '';
$cargo = isset($_GET['cargo']) ? trim($_GET['cargo']) : '';
$email = isset($_GET['email']) ? trim($_GET['email']) : '';

$objUsuario = new Usuario();

$res = $objUsuario->buscarFiltrado(
    $nome,
    $cargo,
    $email
);

echo json_encode($res);
