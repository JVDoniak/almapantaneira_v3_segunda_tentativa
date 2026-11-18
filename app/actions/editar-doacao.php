<?php
require_once __DIR__ . '/../controller/Doacao.php';
require_once __DIR__ . '/../controller/RegraEnderecoDoador.php';
header('Content-Type: application/json');

if( isset($_GET['id']) ){

    $id = $_GET['id']; 
    $objUser = new Doacao();
    
    $dados = $objUser->buscarDoacaoPorId($id);

    if( $dados == false ){
         $array = [
                "status" => 400,
                "msg" => "Doação não encontrada.",
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

if(isset($_POST) && isset($_POST['id_doacao'])){

    $id_doacao = $_POST['id_doacao'];
    $tipo = $_POST['tipo'] ?? '';
    $descricao = $_POST['descricao'] ?? '';
    $valor = $_POST['valor'] ?? '';
    $forma_pagamento = $_POST['forma'] ?? '';
    $data_doacao = $_POST['data_doacao'] ?? '';
    $id_doador = $_POST['id_doador'] ?? '';
    $projetoInformado = $_POST['id_projeto'] ?? '';
    $id_projeto = $projetoInformado === '' ? null : filter_var($projetoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id_projeto === false) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione um projeto válido ou a opção Sem projeto.']);
        exit;
    }

    $objDoacao = new Doacao();
    $anterior = $objDoacao->buscarDoacaoPorId($id_doacao);
    if (!$anterior) {
        echo json_encode(['status' => 404, 'msg' => 'Doação não encontrada.']);
        exit;
    }
    // O campo de valor da edição é desabilitado e não integra o FormData.
    // Alterar o vínculo de projeto não deve apagar o valor da doação.
    if (!array_key_exists('valor', $_POST)) {
        $valor = $anterior['valor'];
    }
    $erroEndereco = RegraEnderecoDoador::erroDoacao($id_doador, $valor);
    if ($erroEndereco) {
        echo json_encode(['status' => 400, 'msg' => $erroEndereco]);
        exit;
    }
    $objDoacao->id_doacao = $id_doacao;
    $objDoacao->tipo = $tipo;
    $objDoacao->descricao = $descricao;
    $objDoacao->valor = $valor;
    $objDoacao->forma_pagamento = $forma_pagamento;
    $objDoacao->data_doacao = $data_doacao;
    $objDoacao->id_doador = $id_doador;
    $objDoacao->id_projeto = $id_projeto;

    try{
        $res = $objDoacao->editar();
        $array = [
            "status" => $res !== false ? 200 : 400,
            "msg" => $res !== false ? "Doação atualizada com sucesso." : "Não foi possível editar a doação."
        ];
    }
    catch(Exception $err){
        $array = [
            "status" => 400,
            "msg" => $err->getMessage()
        ];
    }
    echo json_encode($array);
}
