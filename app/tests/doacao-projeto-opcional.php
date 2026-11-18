<?php
// Sem conexao PDO: actions/controllers reais com Database simulado em subprocessos.
if (($argv[1] ?? '') === 'worker') {
    $caso = json_decode(base64_decode($argv[2]), true);
    class Database {
        public static $gravacao = null;
        public function __construct($table) {}
        public function insert($values) { self::$gravacao = $values; return !empty($GLOBALS['caso']['resultado']); }
        public function update($values) { self::$gravacao = $values; return !empty($GLOBALS['caso']['resultado']); }
        public function execute($sql, $params = []) {
            return new class($params) {
                private $params;
                public function __construct($params) { $this->params = $params; }
                public function fetchColumn() { return $this->params === [3] ? 3 : false; }
            };
        }
        public function select_doacao_doador($id) {
            return ['id_doacao' => 11, 'id_doador' => 3, 'nome' => 'Doador teste', 'tipo' => 'Financeira',
                'descricao' => 'Teste', 'valor' => '75.00', 'forma_pagamento' => 'PIX',
                'data_doacao' => '2026-11-02', 'id_projeto' => $GLOBALS['caso']['anterior'] ?? null,
                'projeto_nome' => null];
        }
    }
    $carregar = function ($arquivo) {
        $codigo = file_get_contents(__DIR__ . '/../' . $arquivo);
        $codigo = preg_replace('/^<\?php\s*/', '', $codigo);
        $codigo = preg_replace('/^\s*require(?:_once)?[^;]+;\s*$/m', '', $codigo);
        return $codigo;
    };
    eval($carregar('app/controller/RegraEnderecoDoador.php'));
    eval($carregar('app/controller/Doacao.php'));
    $_SERVER['REQUEST_METHOD'] = $caso['metodo'] ?? 'POST';
    $_GET = $caso['get'] ?? [];
    $_POST = $caso['post'];
    ob_start();
    register_shutdown_function(function () {
        $resposta = ob_get_clean();
        echo json_encode(['resposta' => json_decode($resposta, true), 'gravacao' => Database::$gravacao]);
    });
    eval($carregar('app/actions/' . $caso['action'] . '-doacao.php'));
    exit;
}

function verificar($condicao, $mensagem) {
    if (!$condicao) throw new RuntimeException($mensagem);
    echo "OK: $mensagem\n";
}
function executar($caso) {
    $processo = proc_open([PHP_BINARY, __FILE__, 'worker', base64_encode(json_encode($caso))],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    fclose($pipes[0]);
    $saida = stream_get_contents($pipes[1]); fclose($pipes[1]);
    $erros = stream_get_contents($pipes[2]); fclose($pipes[2]);
    $status = proc_close($processo);
    if ($status !== 0 || $erros !== '') throw new RuntimeException($erros . $saida);
    return json_decode($saida, true, 512, JSON_THROW_ON_ERROR);
}
$cadastro = ['id_doador' => '3', 'tipo' => 'Financeira', 'descricao' => 'Teste', 'valor' => '75.00',
    'forma_pagamento' => 'PIX', 'data_doacao' => '2026-11-02'];
$edicao = $cadastro + ['id_doacao' => '11', 'forma' => 'PIX'];
unset($edicao['valor']); // Campo disabled no formulario real.
$total = 0;
foreach (['cadastro' => $cadastro, 'editar' => $edicao] as $action => $post) {
    foreach (['vinculado' => '7', 'vazio' => '', 'ausente' => null] as $nome => $projeto) {
        $dados = $post;
        if ($nome !== 'ausente') $dados['id_projeto'] = $projeto;
        $res = executar(['action' => $action, 'post' => $dados, 'resultado' => true, 'anterior' => 7]);
        verificar($action === 'cadastro' ? $res['resposta']['success'] === true : $res['resposta']['status'] === 200,
            "$action $nome aceita a operacao");
        verificar($res['gravacao']['id_projeto'] === ($nome === 'vinculado' ? 7 : null), "$action $nome envia inteiro/NULL");
        verificar($res['gravacao']['valor'] === '75.00', "$action $nome preserva valor");
        $total++;
    }
    foreach (['abc', '0', '-1', ['7']] as $invalido) {
        $res = executar(['action' => $action, 'post' => $post + ['id_projeto' => $invalido], 'resultado' => true]);
        verificar($res['gravacao'] === null && ($action === 'cadastro' ? $res['resposta']['success'] === false : $res['resposta']['status'] === 400),
            "$action rejeita ID invalido sem gravar");
        $total++;
    }
}
// Vincular uma doacao originalmente sem projeto.
$res = executar(['action' => 'editar', 'post' => $edicao + ['id_projeto' => '7'], 'resultado' => true]);
verificar($res['gravacao']['id_projeto'] === 7, 'Edicao vincula registro antes sem projeto'); $total++;
foreach (['valor', 'forma_pagamento'] as $campo) {
    $dados = $cadastro; unset($dados[$campo]);
    $res = executar(['action' => 'cadastro', 'post' => $dados, 'resultado' => true]);
    verificar($res['gravacao'] === null && $res['resposta']['success'] === false, "Cadastro mantem validacao obrigatoria de $campo"); $total++;
}
foreach (['', '0', '-1', 'abc', '999', ['3'], null] as $doadorInvalido) {
    $dados = $cadastro;
    if ($doadorInvalido === null) unset($dados['id_doador']);
    else $dados['id_doador'] = $doadorInvalido;
    $res = executar(['action' => 'cadastro', 'post' => $dados, 'resultado' => true]);
    verificar($res['gravacao'] === null && $res['resposta']['success'] === false
        && $res['resposta']['message'] === 'Selecione um doador válido da lista.',
        'Cadastro rejeita doador ausente, invalido ou inexistente antes do INSERT');
    $total++;
}
foreach (['cadastro' => $cadastro, 'editar' => $edicao] as $action => $dados) {
    $res = executar(['action' => $action, 'post' => $dados, 'resultado' => false]);
    verificar($action === 'cadastro' ? $res['resposta']['success'] === false : $res['resposta']['status'] === 400,
        "$action comunica falha de persistencia"); $total++;
}
$res = executar(['action' => 'editar', 'metodo' => 'GET', 'get' => ['id' => 11], 'post' => [], 'resultado' => true]);
verificar($res['resposta']['status'] === 200 && $res['resposta']['data']['id_projeto'] === null, 'GET de edicao preserva projeto NULL'); $total++;

// Consultas reais, capturadas sem executar SQL ou chamar o construtor PDO.
require_once __DIR__ . '/../model/Database.php';
class ConsultaSimulada extends Database {
    public $sql;
    public $params;
    public function __construct() {}
    public function execute($query, $params = []) {
        $this->sql = $query; $this->params = $params;
        return new class {
            public function fetchAll($mode) { return [['projeto' => null]]; }
            public function fetch($mode) { return ['id_projeto' => null, 'projeto_nome' => null]; }
        };
    }
}
$db = new ConsultaSimulada();
$lista = $db->select_doacoes_with_projetos();
verificar(strpos($db->sql, 'LEFT JOIN projeto') !== false && $lista[0]['projeto'] === null, 'Listagem usa LEFT JOIN e retorna projeto NULL');
$db->select_doacao_doador(11);
verificar(strpos($db->sql, 'LEFT JOIN projeto') !== false && strpos($db->sql, 'doacao.id_projeto') !== false && $db->params === [11],
    'Consulta de edicao inclui ID, LEFT JOIN e parametro');
echo "$total cenarios de actions, 2 consultas capturadas; nenhum SQL executado.\n";
