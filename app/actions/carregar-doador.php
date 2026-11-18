<?php

require '../controller/Doador.php';

header('Content-Type: application/json');

if(isset($_GET['cpf_cnpj'])){

    $cpf_cnpj = $_GET['cpf_cnpj'];

    $objDoador = new Doador();

    $res = $objDoador->buscarDoadorCpfCnpj($cpf_cnpj);

    echo json_encode($res ?: []);
    
}
else{
    echo json_encode(["status" => false, "msg" => "Erro ao buscar doador"]);
}

