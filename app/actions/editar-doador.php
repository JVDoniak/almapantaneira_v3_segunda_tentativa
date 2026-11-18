<?php
require_once __DIR__ . '/../controller/Endereco.php';
require_once __DIR__ . '/../controller/Doador.php';
require_once __DIR__ . '/../controller/RegraEnderecoDoador.php';

header('Content-Type: application/json; charset=utf-8');

    function somenteNumeros($valor) {
        return preg_replace('/\D/', '', $valor ?? '');
    }

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_doador = isset($_GET['id_doador']) ? (int)$_GET['id_doador'] : null;
    if ($id_doador && $id_doador > 0) {
        $doador = new Doador();
        $dados = $doador->buscar_doador_com_endereco($id_doador);

        if (!empty($dados)) {
            echo json_encode(["status" => 200, "data" => $dados]);
        } else {
            echo json_encode(["status" => 404, "msg" => "Doador não encontrado."]);
        }
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_doador = filter_var($_POST['id_doador'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $objDoador = new Doador();
    $anterior = $id_doador ? $objDoador->buscar_doador_com_endereco($id_doador) : false;
    if (!$anterior || !is_array($anterior)) {
        echo json_encode(['status' => 400, 'msg' => 'Doador não encontrado.']);
        exit;
    }

    $dadosEndereco = RegraEnderecoDoador::dados($_POST);
    $erroEndereco = RegraEnderecoDoador::erroPreenchimento($dadosEndereco);
    try {
        if (!$erroEndereco && !RegraEnderecoDoador::informado($dadosEndereco)
            && RegraEnderecoDoador::possuiDoacaoAlta($id_doador)) {
            $erroEndereco = 'Mantenha o endereço completo: este doador possui doação de R$ 1.000,00 ou mais.';
        }
        if ($erroEndereco) {
            echo json_encode(['status' => 400, 'msg' => $erroEndereco]);
            exit;
        }
        $id_endereco = RegraEnderecoDoador::salvar($dadosEndereco, $anterior['id_endereco'] ?? null);
        if ($id_endereco === false) {
            echo json_encode(['status' => 400, 'msg' => 'Não foi possível salvar o endereço.']);
            exit;
        }

        $tipo = $_POST['tipo'] ?? $anterior['tipo'];
        $tipo = $tipo === 'pf' ? 'Pessoa Física' : ($tipo === 'pj' ? 'Pessoa Jurídica' : $tipo);
        $objDoador->id_doador = $id_doador;
        $objDoador->tipo = $tipo;
        $objDoador->cpf_cnpj = somenteNumeros($_POST['cpf_cnpj'] ?? '');
        $objDoador->telefone = somenteNumeros($_POST['telefone'] ?? '');
        $objDoador->email = $_POST['email'] ?? '';
        $objDoador->id_endereco = $id_endereco;
        if ($tipo === 'Pessoa Física') {
            $objDoador->nome = $_POST['nome'] ?? '';
            $objDoador->razao_social = null;
            $objDoador->contato_empresa = null;
        } else {
            $objDoador->nome = $_POST['nome_fantasia'] ?? '';
            $objDoador->razao_social = $_POST['razao_social'] ?? '';
            $objDoador->contato_empresa = $_POST['contato_empresa'] ?? '';
        }
        $res = $objDoador->editar();
        echo json_encode(['status' => $res !== false ? 200 : 400,
            'msg' => $res !== false ? 'Dados do doador e do endereço atualizados com sucesso.'
                : 'Não foi possível atualizar os dados do doador ou do endereço.']);
    } catch (RuntimeException $erro) {
        echo json_encode(['status' => 400, 'msg' => 'Não foi possível verificar as doações do doador. Tente novamente.']);
    }
    exit;
}
