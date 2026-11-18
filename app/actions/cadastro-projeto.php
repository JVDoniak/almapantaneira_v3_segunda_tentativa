<?php
session_start();
require '../controller/Projeto.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === 'POST'){
    $nome = $_POST['nome'] ?? '';
    $responsavel = $_POST['responsavel'] ?? '';
    $local_projeto = $_POST['local_projeto'] ?? '';
    $tipo_projeto = $_POST['tipo_projeto'] ?? '';
    $valor = $_POST['valor_projeto'] ?? '';
    $data_inicio = $_POST['data_inicio'] ?? '';
    $data_termino = $_POST['data_termino'] ?? '';
    $andamento = $_POST['andamento'] ?? '';
    $descricao = $_POST['descricao'] ?? '';

    $objProjeto = new Projeto();
    $objProjeto->nome = $nome;
    $objProjeto->responsavel = $responsavel;
    $objProjeto->local_projeto = $local_projeto;
    $objProjeto->tipo_projeto = $tipo_projeto;
    $objProjeto->valor_projeto = $valor;
    $objProjeto->data_inicio = $data_inicio;
    $objProjeto->data_termino = $data_termino;
    $objProjeto->andamento = $andamento;
    $objProjeto->descricao = $descricao;
    $objProjeto->id_usuario = $_SESSION["usuario_id"]; 

    
    $res = $objProjeto->cadastrar();

    echo json_encode([
        'successo' => $res,
        'message' => $res ? 'Projeto cadastrado com sucesso.' : 'Não foi possível cadastrar o projeto.'
    
    ]);

}

?>