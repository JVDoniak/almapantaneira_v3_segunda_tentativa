<?php
require '../controller/Endereco.php';
require '../controller/Fornecedor.php';

header('Content-Type: Application/json');

    function somenteNumeros($valor) {
        return preg_replace('/\D/', '', $valor ?? '');
    }

if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    $id_fornecedor = $_GET['id_fornecedor'] ?? null;
    $id_endereco = $_GET['id_endereco'] ?? null;

    if ($id_fornecedor && $id_endereco) {
        $fornecedor = new Fornecedor();
        $dados = $fornecedor->buscarFornecedorComEndereco($id_fornecedor, $id_endereco);

        if (!empty($dados)) {
            echo json_encode(["status" => 200, "data" => $dados]);
        } else {
            echo json_encode(["status" => 404, "msg" => "Fornecedor ou endereço não encontrado."]);
        }
        exit;
    }

    echo json_encode(["status" => 400, "msg" => "Parâmetros inválidos."]);
    exit;
}



if (isset($_POST) && isset($_POST['cpf_cnpj'])) {

    $id_fornecedor = $_POST['id_fornecedor'];
    $nome = $_POST['nome'] ?? '';
    $razao_social = $_POST['razao_social'] ?? '';
    $cpf_cnpj = somenteNumeros($_POST['cpf_cnpj']) ?? '';
    $contato = $_POST['contato'] ?? '';
    $telefone = somenteNumeros($_POST['telefone']) ?? '';
    $email = $_POST['email'] ?? '';
    $id_endereco = $_POST['id_endereco'];
    $cep = somenteNumeros($_POST['cep']) ?? '';
    $logradouro = $_POST['logradouro'] ?? '';
    $numero = $_POST['numero'] ?? '';
    $complemento = $_POST['complemento'] ?? '';
    $bairro = $_POST['bairro'] ?? '';
    $cidade = $_POST['cidade'] ?? '';
    $estado = $_POST['estado'] ?? '';

    $objFornecedor = new Fornecedor();
    $objFornecedor->id_fornecedor = $id_fornecedor;
    $objFornecedor->nome = $nome;
    $objFornecedor->razao_social = $razao_social;
    $objFornecedor->cpf_cnpj = somenteNumeros($_POST['cpf_cnpj']);
    $objFornecedor->telefone = somenteNumeros($_POST['telefone']);
    $objFornecedor->email = $email;
    $objFornecedor->contato = $contato;
    $objFornecedor->id_endereco = $id_endereco;


    $objEndereco = new Endereco();
    $objEndereco->id_endereco = $id_endereco;
    $objEndereco->cep = somenteNumeros($_POST['cep']);
    $objEndereco->logradouro = $logradouro;
    $objEndereco->numero = $numero;
    $objEndereco->complemento = $complemento;
    $objEndereco->bairro = $bairro;
    $objEndereco->cidade = $cidade;
    $objEndereco->estado = $estado;
    $objEndereco->id_fornecedor = $id_fornecedor;

    try {

        $res1 = $objFornecedor->editar();
        $res2 = $objEndereco->editar();

        if($res1 && $res2){

            $array = [
                "status" => 200,
                "msg" => 'Fornecedor atualizado com sucesso.'
            ];

        }else{
            $array = [
                "status" => 400,
                "msg" => 'Não foi possível atualizar o fornecedor.'
            ];
        }
       
        echo json_encode($array);

    } 
    catch (Exception $err) {
        $array = [
            "status" => 400,
            "msg" => $err
        ];
        echo json_encode($array);
    }
}