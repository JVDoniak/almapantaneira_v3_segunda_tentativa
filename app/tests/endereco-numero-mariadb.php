<?php
// Operação pontual autorizada. Sem --apply, testa somente tabela TEMPORARY.
require_once __DIR__ . '/../utils/configLocal.php';
if (getenv('ALMA_MIGRATION_CONFIRM') !== 'ENDERECO_NUMERO') throw new RuntimeException('Ferramenta manual: exige configuração e confirmação explícita.');
$db = new PDO('mysql:host=' . almaConfiguracao('DB_HOST') . ';dbname=' . almaConfiguracao('DB_NAME') . ';charset=utf8mb4', almaConfiguracao('DB_USER'), almaConfiguracao('DB_PASS'), [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
$db->exec("SET SESSION sql_mode = 'STRICT_ALL_TABLES,NO_ENGINE_SUBSTITUTION'");
function exigir($ok, $mensagem) { if (!$ok) throw new RuntimeException($mensagem); }
function linhas($db, $tabela) {
    return $db->query("SELECT id_endereco, cep, logradouro, CAST(numero AS CHAR) AS numero, complemento, bairro, cidade, estado FROM `$tabela` ORDER BY id_endereco")->fetchAll(PDO::FETCH_ASSOC);
}
function resumo($linhas) { return hash('sha256', serialize($linhas)); }
$coluna = $db->query("SHOW COLUMNS FROM endereco LIKE 'numero'")->fetch(PDO::FETCH_ASSOC);
exigir($coluna['Type'] === 'tinyint(4)' && $coluna['Null'] === 'NO' && $coluna['Default'] === null, 'Estrutura inesperada: interrompido.');
exigir((int)$db->query("SELECT COUNT(*) FROM information_schema.TRIGGERS WHERE EVENT_OBJECT_SCHEMA=DATABASE() AND EVENT_OBJECT_TABLE='endereco'")->fetchColumn() === 0, 'Triggers exigem análise adicional.');
$estrutura = $db->query('SHOW CREATE TABLE endereco')->fetch(PDO::FETCH_NUM)[1];
exigir(strpos($estrutura, 'FOREIGN KEY') === false, 'FK de saída exige análise adicional.');
$antes = linhas($db, 'endereco');
$baseBackup = getenv('LOCALAPPDATA');
exigir(is_string($baseBackup) && is_dir($baseBackup), 'Diretório privado de backup indisponível.');
$pasta = $baseBackup . '/AlmaPantaneira/backups/endereco-' . date('Ymd-His') . '-' . bin2hex(random_bytes(4));
exigir(mkdir($pasta, 0700, true), 'Não foi possível criar diretório de backup.');
$sql = "SET NAMES utf8mb4;\n" . $estrutura . ";\n";
foreach ($antes as $linha) {
    $valores = array_map(static function ($v) { return $v === null ? 'NULL' : "X'" . bin2hex((string)$v) . "'"; }, array_values($linha));
    $sql .= 'INSERT INTO `endereco` (`' . implode('`,`', array_keys($linha)) . '`) VALUES (' . implode(',', $valores) . ");\n";
}
$arquivo = $pasta . '/endereco.sql';
exigir(file_put_contents($arquivo, $sql) === strlen($sql), 'Backup incompleto.');
exigir(hash_file('sha256', $arquivo) === hash('sha256', $sql), 'Checksum do backup divergente.');
// Restaura o arquivo efetivamente relido em tabela temporária da conexão.
$restaurado = str_replace('`endereco`', '`endereco_restauracao_teste`', file_get_contents($arquivo));
$restaurado = str_replace('CREATE TABLE ', 'CREATE TEMPORARY TABLE ', $restaurado);
$db->exec($restaurado);
exigir(resumo(linhas($db, 'endereco_restauracao_teste')) === resumo($antes), 'Restauração divergente.');
$migracao = file_get_contents(__DIR__ . '/../docs/migrations/20261102-endereco-numero-varchar.sql');
$db->exec(str_replace('`endereco`', '`endereco_restauracao_teste`', $migracao));
exigir(resumo(linhas($db, 'endereco_restauracao_teste')) === resumo($antes), 'Dados divergentes na migração isolada.');
require_once __DIR__ . '/../controller/Endereco.php';
foreach (['25', '1500', 'S/N'] as $numero) {
    exigir(Endereco::numeroValido($numero), 'Validação PHP rejeitou número permitido.');
    $stmt = $db->prepare("INSERT INTO endereco_restauracao_teste (cep,logradouro,numero,complemento,bairro,cidade,estado) VALUES ('79000000','Teste',?,'','Teste','Teste','MS')");
    $stmt->execute([$numero]);
    $id = $db->lastInsertId();
    $stmt = $db->prepare('SELECT numero FROM endereco_restauracao_teste WHERE id_endereco=?');
    $stmt->execute([$id]); exigir($stmt->fetchColumn() === $numero, 'PDO não preservou o texto.');
}
$resultado = ['backup' => $arquivo, 'sha256' => hash_file('sha256', $arquivo), 'registros' => count($antes), 'restauracao' => true, 'mariadb' => $db->query('SELECT VERSION()')->fetchColumn(), 'valores' => ['25','1500','S/N'], 'executada' => false];
if (in_array('--apply', $argv, true)) {
    // Impede alterações concorrentes entre a última comparação e a DDL.
    $db->exec('LOCK TABLES endereco WRITE');
    try {
        exigir(resumo(linhas($db, 'endereco')) === resumo($antes), 'Dados mudaram após o backup; migração interrompida.');
        exigir($db->query('SHOW CREATE TABLE endereco')->fetch(PDO::FETCH_NUM)[1] === $estrutura, 'Estrutura mudou após o backup.');
        $db->exec($migracao);
        exigir(resumo(linhas($db, 'endereco')) === resumo($antes), 'ALERTA: verificação pós-migração divergente. Interrompido, preservar backup.');
        $atual = $db->query("SHOW COLUMNS FROM endereco LIKE 'numero'")->fetch(PDO::FETCH_ASSOC);
        exigir($atual['Type'] === 'varchar(20)' && $atual['Null'] === 'NO', 'DDL não corresponde ao esperado.');
        $resultado['executada'] = true;
        $resultado['dados_preservados'] = true;
    } finally { $db->exec('UNLOCK TABLES'); }
}
file_put_contents($pasta . '/verificacao.json', json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
echo json_encode($resultado, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
