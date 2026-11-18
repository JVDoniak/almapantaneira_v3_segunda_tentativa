<?php

require '../controller/Fornecedor.php';
require '../controller/Endereco.php';

header('Content-Type: application/json');

function somenteNumeros($valor) {
    return preg_replace('/\D/', '', $valor ?? '');
}

if($_SERVER["REQUEST_METHOD"] === 'POST') {
    $endereco = new Endereco();

    $verificaCep = $endereco->buscarEndereco($_POST['cep']);

    if(empty($verificaCep)){
        
        $endereco->cep = somenteNumeros($_POST['cep']);
        $endereco->logradouro = $_POST['logradouro'];
        $endereco->numero = $_POST['numero'];
        $endereco->complemento = $_POST['complemento'];
        $endereco->bairro = $_POST['bairro'];
        $endereco->cidade = $_POST['cidade'];
        $endereco->estado = $_POST['estado'];

        $idEndereco = $endereco->cadastrar();

    } else {
        
        $idEndereco = $verificaCep["id_endereco"];
    }

    $nome = $_POST['nome'] ?? '';
    $razao_social = $_POST['razao_social'] ?? '';
    $cpf_cnpj = somenteNumeros($_POST['cpf_cnpj']) ?? '';
    $telefone = somenteNumeros($_POST['telefone']) ?? '';
    $email = $_POST['email'] ?? '';
    $contato = $_POST['contato'] ?? '';

    $objFornecedor = new Fornecedor();
    $objFornecedor->nome = $nome;
    $objFornecedor->razao_social = $razao_social;
    $objFornecedor->cpf_cnpj = $cpf_cnpj;
    $objFornecedor->telefone = $telefone;
    $objFornecedor->email = $email;
    $objFornecedor->contato = $contato;
    
    $objFornecedor->id_endereco = $idEndereco;

    $res = $objFornecedor->cadastrar();

    echo json_encode([
        'sucess' => $res,
        'message' => $res ? 'Fornecedor cadastrado com sucesso.' : 'Não foi possível cadastrar o fornecedor.'
    ]);
     
}