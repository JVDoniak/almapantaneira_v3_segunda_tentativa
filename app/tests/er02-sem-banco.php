<?php
// Teste isolado: substitui Database em memoria; nunca carrega a conexao PDO real.
class Database
{
    public static $ultima;
    public static $resultado = 1;
    public function __construct($table) {}
    public function insert($values) { self::$ultima = $values; return true; }
    public function update($values) { self::$ultima = $values; return true; }
    public function execute($query, $params = []) {
        self::$ultima = [$query, $params];
        return new class {
            public function fetchColumn() { return Database::$resultado; }
            public function fetchAll($mode) { return []; }
        };
    }
}
foreach (['Despesa', 'TipoDespesa'] as $classe) {
    $codigo = file_get_contents(__DIR__ . '/../controller/' . $classe . '.php');
    $codigo = preg_replace('/^<\?php\s*/', '', $codigo);
    $codigo = preg_replace('/^require_once[^;]+;\s*/', '', $codigo);
    eval($codigo);
}
function verificar($condicao, $mensagem) {
    if (!$condicao) throw new RuntimeException($mensagem);
    echo "OK: $mensagem\n";
}
$despesa = new Despesa();
$despesa->id_categoria_despesa = 37;
$despesa->id_tipo_despesa = 2;
$despesa->cadastrar();
verificar(Database::$ultima['id_categoria_despesa'] === 37 && Database::$ultima['id_tipo_despesa'] === 2, 'Cadastro preserva tipo e categoria independentes');
verificar(Database::$ultima['id_fornecedor'] === null && Database::$ultima['id_projeto'] === null, 'Fornecedor e projeto permanecem NULL');
$despesa->id_tipo_despesa = null;
$despesa->editar();
verificar(Database::$ultima['id_tipo_despesa'] === null && Database::$ultima['id_categoria_despesa'] === 37, 'Edicao preserva tipo NULL e categoria dinamica');
$despesa->buscarComFiltros(['id_categoria_despesa' => 37, 'id_tipo_despesa' => 2]);
verificar(Database::$ultima[1] === [37, 2], 'Filtros usam parametros separados');
verificar(strpos(Database::$ultima[0], 'LEFT JOIN tipo_despesa') !== false, 'Consulta mantem despesas sem tipo por LEFT JOIN');
$tipo = new TipoDespesa();
verificar($tipo->existe(1), 'Tipo existente e aceito');
Database::$resultado = false;
verificar(!$tipo->existe(9999), 'Tipo inexistente e rejeitado');
verificar(!$despesa->categoriaExiste(9999), 'Categoria inexistente e rejeitada');
echo "Nenhuma conexao com o banco foi aberta.\n";
