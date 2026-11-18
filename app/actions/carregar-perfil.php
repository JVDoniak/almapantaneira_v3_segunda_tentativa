<?php

require '../controller/Usuario.php';

if(isset($_GET['id_user'])){

    $id = $_GET['id_user'];
    $objUser = new Usuario();
    $dados = $objUser->carregarPerfil($id);

    if( $dados == false ){
         $array = [
                "status" => 400,
                "msg" => "Usuário inexistente!!",
            ];

        // se eu quiser debugar o código usa o EXIT e veja o erro no console
        // print_r($array);
        // exit;
    }else{
            $array = [
                "status" => 200,
                "msg" => "Dados requisitados com sucesso!",
                "data" => $dados
            ];
    }
    echo json_encode($array);
    //converte para json e devolve os dados para JS
    
}
else{
    echo json_encode(["status" => false, "msg" => "Erro ao buscar doador"]);
}

