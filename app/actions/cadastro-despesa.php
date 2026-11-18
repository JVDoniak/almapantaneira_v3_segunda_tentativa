<?php
require_once __DIR__ . '/../controller/Despesa.php';
require_once __DIR__ . '/../controller/TipoDespesa.php';

header('Content-Type: application/json; charset=utf-8');

// GET: devolve os fornecedores e projetos do banco para o autocompletar
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $objDespesa = new Despesa();

    echo json_encode([
        'success'      => true,
        'fornecedores' => $objDespesa->listarFornecedores(),
        'projetos'     => $objDespesa->listarProjetos()
    ]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $tipoInformado = $_POST['id_tipo_despesa'] ?? '';
    $id_tipo_despesa = $tipoInformado === '' ? null : filter_var($tipoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $tipos = new TipoDespesa();
    if ($id_tipo_despesa === false || ($id_tipo_despesa !== null && !$tipos->existe($id_tipo_despesa))) {
        echo json_encode(['success' => false, 'message' => 'Selecione um tipo de despesa válido.']);
        exit;
    }

    $nome                 = trim($_POST['nome'] ?? '');
    $id_categoria_despesa = filter_var($_POST['id_categoria_despesa'] ?? '', FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $fornecedorInformado  = $_POST['id_fornecedor'] ?? '';
    $projetoInformado     = $_POST['id_projeto'] ?? '';
    $id_fornecedor        = $fornecedorInformado === '' ? null : filter_var($fornecedorInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $id_projeto           = $projetoInformado === '' ? null : filter_var($projetoInformado, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $valor                = (float) ($_POST['valor'] ?? 0);
    $data_vencimento      = $_POST['data_vencimento'] ?? '';
    $forma_pagamento      = $_POST['forma_pagamento'] ?? '';
    $descricao            = trim($_POST['descricao'] ?? '');

    if ($nome === '') {
        echo json_encode(['success' => false, 'message' => 'Informe o nome da despesa.']);
        exit;
    }

    if ($id_categoria_despesa <= 0 || !(new Despesa())->categoriaExiste($id_categoria_despesa)) {
        echo json_encode(['success' => false, 'message' => 'Selecione a categoria da despesa.']);
        exit;
    }

    if ($id_fornecedor === false) {
        echo json_encode(['success' => false, 'message' => 'Selecione um fornecedor da lista.']);
        exit;
    }

    if ($id_projeto === false) {
        echo json_encode(['success' => false, 'message' => 'Selecione um projeto da lista.']);
        exit;
    }

    if ($valor <= 0) {
        echo json_encode(['success' => false, 'message' => 'Informe um valor válido.']);
        exit;
    }

    if ($data_vencimento === '') {
        echo json_encode(['success' => false, 'message' => 'Informe a data de vencimento.']);
        exit;
    }

    if ($forma_pagamento === '') {
        echo json_encode(['success' => false, 'message' => 'Selecione a forma de pagamento.']);
        exit;
    }

    // Upload do comprovante (opcional)
    $path = null;

    if (isset($_FILES['doc']) && $_FILES['doc']['error'] !== UPLOAD_ERR_NO_FILE) {
        $arquivo = $_FILES['doc'];

        if ($arquivo['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Falha ao enviar o comprovante.']);
            exit;
        }

        $extensao = strtolower(pathinfo($arquivo['name'], PATHINFO_EXTENSION));

        if (!in_array($extensao, ['pdf', 'jpg', 'jpeg', 'png'], true)) {
            echo json_encode(['success' => false, 'message' => 'Formato inválido. Envie PDF, JPG ou PNG.']);
            exit;
        }

        $pastaDocs = __DIR__ . '/../uploads/comprovantes/';

        if (!is_dir($pastaDocs)) {
            mkdir($pastaDocs, 0755, true);
        }

        $nomeArquivoFinal = 'doc-despesa-' . uniqid('', true) . '.' . $extensao;

        if (!move_uploaded_file($arquivo['tmp_name'], $pastaDocs . $nomeArquivoFinal)) {
            echo json_encode(['success' => false, 'message' => 'Não foi possível salvar o comprovante.']);
            exit;
        }

        $path = '/almapantaneira/app/uploads/comprovantes/' . $nomeArquivoFinal;
    }

    $objDespesa = new Despesa();
    $objDespesa->nome                 = $nome;
    $objDespesa->id_categoria_despesa = $id_categoria_despesa;
    $objDespesa->id_tipo_despesa      = $id_tipo_despesa;
    $objDespesa->id_fornecedor        = $id_fornecedor;
    $objDespesa->id_projeto           = $id_projeto;
    $objDespesa->valor                = $valor;
    $objDespesa->data_vencimento      = $data_vencimento;
    $objDespesa->forma_pagamento      = $forma_pagamento;
    $objDespesa->descricao            = $descricao;
    $objDespesa->anexo_nota           = $path;
    $objDespesa->data_lancamento      = date('Y-m-d H:i:s');

    $res = $objDespesa->cadastrar();

    echo json_encode([
        'success' => (bool) $res,
        'message' => $res ? 'Despesa cadastrada com sucesso.' : 'Não foi possível cadastrar a despesa.'
    ]);
    exit;
}
