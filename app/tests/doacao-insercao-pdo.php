<?php
// Exercita insert/execute reais com conexao simulada, sem abrir PDO nem gravar SQL.
require_once __DIR__ . '/../model/Database.php';
function conferir($ok, $mensagem) {
    if (!$ok) throw new RuntimeException($mensagem);
    echo "OK: $mensagem\n";
}
class StatementInsercaoSimulado {
    public $params;
    public $falhar = false;
    public function execute($params) {
        $this->params = $params;
        if ($this->falhar) {
            $erro = new PDOException('Falha simulada de FK doacao_ibfk_1', 23000);
            $erro->errorInfo = ['23000', 1452, 'Falha simulada'];
            throw $erro;
        }
        return true;
    }
}
class ConexaoInsercaoSimulada {
    public $sql;
    public $statement;
    public function __construct() { $this->statement = new StatementInsercaoSimulado(); }
    public function prepare($query) { $this->sql = $query; return $this->statement; }
}
$classe = new ReflectionClass(Database::class);
$db = $classe->newInstanceWithoutConstructor();
$conexao = new ConexaoInsercaoSimulada();
foreach (['conn' => $conexao, 'table' => 'doacao'] as $nome => $valor) {
    $prop = $classe->getProperty($nome);
    $prop->setAccessible(true);
    $prop->setValue($db, $valor);
}
foreach ([7, null] as $projeto) {
    $res = $db->insert(['id_doador' => 3, 'tipo' => '1', 'id_projeto' => $projeto,
        'descricao' => 'Teste ficticio', 'valor' => '1200.00',
        'forma_pagamento' => 'Transferência Bancária', 'data_doacao' => '2026-11-02']);
    conferir($res === $conexao->statement, 'insert retorna statement no sucesso');
    conferir($conexao->statement->params[0] === 3 && $conexao->statement->params[2] === $projeto,
        'execute recebe doador inteiro e projeto inteiro/NULL sem conversao');
    conferir($conexao->sql === 'INSERT INTO doacao (id_doador,tipo,id_projeto,descricao,valor,forma_pagamento,data_doacao) VALUES (?,?,?,?,?,?,?)',
        'SQL possui colunas e placeholders correspondentes');
}
$log = tempnam(sys_get_temp_dir(), 'alma-pdo-');
$anterior = ini_set('error_log', $log);
try {
    $conexao->statement->falhar = true;
    ob_start();
    $res = $db->insert(['id_doador' => 0, 'id_projeto' => null]);
    $saida = ob_get_clean();
    conferir($res === false && $saida === '', 'PDOException retorna false sem contaminar o JSON');
    $texto = file_get_contents($log);
    conferir(strpos($texto, 'SQLSTATE=23000; driver=1452') !== false,
        'Erro de FK fica registrado no servidor com SQLSTATE e codigo do driver');
} finally {
    ini_set('error_log', $anterior);
}
echo "Nenhuma conexao real; arquivo de log temporario: $log\n";
