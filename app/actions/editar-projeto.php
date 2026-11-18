<?php
require '../controller/Projeto.php';
header('Content-Type: application/json');

// //capturando o que veio do JS via GET
// print_r($_GET);
// exit;

if( isset($_GET['id']) ){

    $id = $_GET['id']; 
    $objProjeto = new Projeto();
    $dados = $objProjeto->buscar_por_id($id,);

    if( $dados == false ){
         $array = [
                "status" => 400,
                "msg" => "Projeto não encontrado.",
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

//  && isset($_POST['cpf'])

if($_SERVER['REQUEST_METHOD'] == 'POST'){

     $id_projeto = $_POST['id_projeto'] ?? '';
     $nome = $_POST['nome'] ?? '';
     $responsavel = $_POST['responsavel'] ?? '';
     $local_projeto = $_POST['local_projeto'] ?? '';
     $tipo_projeto = $_POST['tipo_projeto'] ?? '';
     $data_inicio = $_POST['data_inicio'] ?? '';
     $data_termino = $_POST['data_termino'] ?? '';
     $valor_projeto = $_POST['valor_projeto'] ?? '';
     $andamento = $_POST['andamento'] ?? '';
     $descricao = $_POST['descricao'] ?? '';

     $objProjeto = new Projeto();
     $objProjeto->id_projeto = $id_projeto;
     $objProjeto->nome = $nome;
     $objProjeto->responsavel = $responsavel;
     $objProjeto->local_projeto = $local_projeto;
     $objProjeto->tipo_projeto = $tipo_projeto;
     $objProjeto->data_inicio = $data_inicio;
     $objProjeto->data_termino = $data_termino;
     $objProjeto->valor_projeto = $valor_projeto;
     $objProjeto->andamento = $andamento;
     $objProjeto->descricao = $descricao;

     try{
         $res = $objProjeto->editar();
         $array = [
             "status" => 200,
             "msg" => "Projeto atualizado com sucesso."
         ];
         echo json_encode($array);
     }
     catch(Exception $err){
       
         $array = [
             "status" => 400,
             "msg" => $err
         ];

         echo json_encode($array);
     }
}


?>