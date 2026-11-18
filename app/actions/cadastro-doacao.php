<?php

require_once __DIR__ . '/../controller/Doacao.php';
require_once __DIR__ . '/../controller/RegraEnderecoDoador.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    
    // 1. Recebe os dados enviados via FormData do formulário
    $cpf_cnpj        = $_POST['cpf_cnpj'] ?? '';
    $nomeDoador      = $_POST['nome_doador'] ?? '';
    $idDoador        = filter_var($_POST['id_doador'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $tipo            = $_POST['tipo'] ?? '';
    $projetoInformado = $_POST['id_projeto'] ?? '';
    $idProjeto       = $projetoInformado === '' ? null : filter_var($projetoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $descricao       = $_POST['descricao'] ?? '';
    $valor           = $_POST['valor'] ?? '';
    $formaPagamento  = $_POST['forma_pagamento'] ?? '';
    $dataDoacao      = $_POST['data_doacao'] ?? '';

    // 2. Validação de segurança dos campos obrigatórios
    if (empty($valor) || empty($formaPagamento)) {
        echo json_encode([
            'success' => false,
            'message' => 'Preencha todos os campos obrigatórios da doação.'
        ]);
        exit;
    }

    if ($idProjeto === false) {
        echo json_encode(['success' => false, 'message' => 'Selecione um projeto válido ou a opção Sem projeto.']);
        exit;
    }

    // 4. Instancia e cadastra a Doação
    $objDoacao = new Doacao();
    if ($idDoador === false || !$objDoacao->doadorExiste($idDoador)) {
        echo json_encode(['success' => false, 'message' => 'Selecione um doador válido da lista.']);
        exit;
    }
    $objDoacao->id_doador       = $idDoador;
    $erroEndereco = RegraEnderecoDoador::erroDoacao($idDoador, $valor);
    if ($erroEndereco) {
        echo json_encode(['success' => false, 'message' => $erroEndereco]);
        exit;
    }
    $objDoacao->tipo            = $tipo;
    $objDoacao->id_projeto      = $idProjeto;
    $objDoacao->descricao       = $descricao;
    $objDoacao->valor           = $valor;
    $objDoacao->forma_pagamento = $formaPagamento;
    $objDoacao->data_doacao     = $dataDoacao;

    // print_r($objDoacao);
    // exit;
    $res = $objDoacao->cadastrar();

       if ($res) {
        echo json_encode([
            'success' => true,
            'message' => 'Doação cadastrada com sucesso!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erro ao registrar a doação no banco de dados.'
        ]);
    }
    exit;
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Método de requisição inválido.'
    ]);
    exit;
}
