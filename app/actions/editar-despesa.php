<?php
require_once __DIR__ . '/../controller/Despesa.php';
require_once __DIR__ . '/../controller/TipoDespesa.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $id_despesa = isset($_GET['id_despesa']) ? (int)$_GET['id_despesa'] : null;
    if ($id_despesa && $id_despesa > 0) {
        $despesa = new Despesa();
        $dados = $despesa->buscar_despesa_por_id($id_despesa);

        if (!empty($dados)) {
            echo json_encode([
                "status"       => 200,
                "data"         => $dados,
                "fornecedores" => $despesa->listarFornecedores(),
                "projetos"     => $despesa->listarProjetos()
            ]);
        } else {
            echo json_encode(["status" => 404, "msg" => "Despesa não encontrada."]);
        }
    } else {
        echo json_encode(["status" => 400, "msg" => "ID da despesa não informado."]);
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipoInformado = $_POST['id_tipo_despesa'] ?? '';
    $id_tipo_despesa = $tipoInformado === '' ? null : filter_var($tipoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $tipos = new TipoDespesa();
    if ($id_tipo_despesa === false || ($id_tipo_despesa !== null && !$tipos->existe($id_tipo_despesa))) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione um tipo de despesa válido.']);
        exit;
    }

    $id_despesa           = (int) ($_POST['id_despesa'] ?? 0);
    $categoriaInformada = $_POST['id_categoria_despesa'] ?? '';
    $id_categoria_despesa = $categoriaInformada === '' ? null : filter_var($categoriaInformada, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $fornecedorInformado  = $_POST['id_fornecedor'] ?? '';
    $projetoInformado     = $_POST['id_projeto'] ?? '';
    $id_fornecedor        = $fornecedorInformado === '' ? null : filter_var($fornecedorInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $id_projeto           = $projetoInformado === '' ? null : filter_var($projetoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

    if ($id_despesa <= 0) {
        echo json_encode(['status' => 400, 'msg' => 'ID da despesa ausente.']);
        exit;
    }

    if ($id_categoria_despesa === false || ($id_categoria_despesa !== null && !(new Despesa())->categoriaExiste($id_categoria_despesa))) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione a categoria da despesa.']);
        exit;
    }

    if ($id_fornecedor === false) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione um fornecedor da lista.']);
        exit;
    }

    if ($id_projeto === false) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione um projeto da lista.']);
        exit;
    }

    $objDespesa = new Despesa();
    $anterior = $objDespesa->buscar_despesa_por_id($id_despesa);
    if (!$anterior) {
        echo json_encode(['status' => 404, 'msg' => 'Despesa não encontrada.']);
        exit;
    }
    if (!array_key_exists('id_tipo_despesa', $_POST)) {
        $id_tipo_despesa = $anterior['id_tipo_despesa'] ?? null;
    }
    if (!array_key_exists('id_categoria_despesa', $_POST)) {
        $id_categoria_despesa = $anterior['id_categoria_despesa'];
    }
    if ($id_categoria_despesa === null && $anterior['id_categoria_despesa'] !== null) {
        echo json_encode(['status' => 400, 'msg' => 'Selecione a categoria da despesa.']);
        exit;
    }
    $objDespesa->id_despesa           = $id_despesa;
    $objDespesa->nome                 = trim($_POST['nome'] ?? '');
    $objDespesa->id_categoria_despesa = $id_categoria_despesa;
    $objDespesa->id_tipo_despesa      = $id_tipo_despesa;
    $objDespesa->id_fornecedor        = $id_fornecedor;
    $objDespesa->id_projeto           = $id_projeto;
    $objDespesa->valor                = (float) ($_POST['valor'] ?? 0);
    $objDespesa->data_vencimento      = $_POST['data_vencimento'] ?? '';
    $objDespesa->forma_pagamento      = $_POST['forma_pagamento'] ?? '';
    $objDespesa->descricao            = trim($_POST['descricao'] ?? '');

    $res = $objDespesa->editar();

    if ($res !== false) {
        echo json_encode(['status' => 200, 'msg' => 'Despesa atualizada com sucesso!']);
    } else {
        echo json_encode(['status' => 400, 'msg' => 'Erro ao atualizar os dados da despesa.']);
    }
    exit;
}
