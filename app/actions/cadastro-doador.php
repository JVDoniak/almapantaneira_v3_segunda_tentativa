<?php
require_once __DIR__ . '/../controller/Doador.php';
require_once __DIR__ . '/../controller/Endereco.php'; 
require_once __DIR__ . '/../controller/RegraEnderecoDoador.php';

header('Content-Type: application/json; charset=utf-8');

function somenteNumeros($valor) {
    return preg_replace('/\D/', '', $valor ?? '');
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {

    $dadosEndereco = RegraEnderecoDoador::dados($_POST);
    $erroEndereco = RegraEnderecoDoador::erroPreenchimento($dadosEndereco);
    if ($erroEndereco) {
        echo json_encode(['success' => false, 'message' => $erroEndereco]);
        exit;
    }
    $idEndereco = RegraEnderecoDoador::salvar($dadosEndereco);
    if ($idEndereco === false) {
        echo json_encode(['success' => false, 'message' => 'Erro ao cadastrar o endereço.']);
        exit;
    }

// Define o tipo enviado pelo formulário
    $tipo = $_POST['tipo'] ?? 'Pessoa Física';

    $objDoador = new Doador();
    $objDoador->tipo          = $tipo;
    $objDoador->cpf_cnpj      = somenteNumeros($_POST['cpf_cnpj']);
    $objDoador->telefone      = somenteNumeros($_POST['telefone']);
    $objDoador->email         = $_POST['email'] ?? '';
    $objDoador->id_endereco   = $idEndereco;
    
    // Tratamento inteligente para a coluna 'nome'
    if ($tipo === 'Pessoa Física') {
        // Se for Pessoa Física, pega o campo 'nome' padrão
        $objDoador->nome            = $_POST['nome'] ?? '';
        $objDoador->razao_social    = null;
        $objDoador->contato_empresa = null;
    } else {
        // Se for Pessoa Jurídica, pega o 'nome_fantasia' e joga na propriedade 'nome'
        $objDoador->nome            = $_POST['nome_fantasia'] ?? '';
        $objDoador->razao_social    = $_POST['razao_social'] ?? '';
        $objDoador->contato_empresa = $_POST['contato_empresa'] ?? '';
    }

    $res = $objDoador->cadastrar();

    echo json_encode([
        'success' => (bool)$res,
        'message' => $res ? 'Doador cadastrado com sucesso.' : 'Não foi possível cadastrar o doador.'
    ]);
    exit;
}
