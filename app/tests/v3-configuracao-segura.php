<?php
// Fixtures sintéticas. Nenhuma conexão PDO ou envio SMTP é executado.
$caso = $argv[1] ?? '';
$chaves = ['DB_HOST','DB_NAME','DB_USER','DB_PASS','SMTP_HOST','SMTP_PORT','SMTP_USER','SMTP_PASS','SMTP_FROM','SMTP_FROM_NAME','ALMA_CONFIG_FILE'];
if ($caso === '') {
    foreach (['ambiente','externo','ausente','interno'] as $item) {
        $pipes = [];
        $p = proc_open([PHP_BINARY, __FILE__, $item], [1 => ['pipe','w'], 2 => ['pipe','w']], $pipes);
        if (!is_resource($p)) throw new RuntimeException('Subprocesso indisponível.');
        $saida = stream_get_contents($pipes[1]); $erros = stream_get_contents($pipes[2]);
        fclose($pipes[1]); fclose($pipes[2]);
        if (proc_close($p) !== 0) throw new RuntimeException('Falha no cenário: ' . $item);
        echo 'OK: configuração ' . $item . PHP_EOL;
    }
    exit;
}
foreach ($chaves as $chave) putenv($chave);
$fixture = ['DB_HOST'=>'host.example.invalid','DB_NAME'=>'banco_ficticio','DB_USER'=>'usuario_ficticio','DB_PASS'=>'fixture_sem_servico',
    'SMTP_HOST'=>'smtp.example.invalid','SMTP_PORT'=>'587','SMTP_USER'=>'usuario_ficticio','SMTP_PASS'=>'fixture_sem_servico','SMTP_FROM'=>'teste@example.invalid','SMTP_FROM_NAME'=>'Teste'];
$temporario = null;
if ($caso === 'ambiente') foreach ($fixture as $chave => $valor) putenv($chave . '=' . $valor);
if ($caso === 'externo') {
    $temporario = tempnam(sys_get_temp_dir(), 'alma-config-ficticia-');
    file_put_contents($temporario, json_encode($fixture));
    putenv('ALMA_CONFIG_FILE=' . $temporario);
    putenv('DB_NAME=precedencia_ambiente');
    $fixture['DB_NAME'] = 'precedencia_ambiente';
}
if ($caso === 'interno') putenv('ALMA_CONFIG_FILE=' . dirname(__DIR__) . '/.env.example');
require_once __DIR__ . '/../model/Database.php';
class BancoSemConexao extends Database { public function conectar() { return true; } }
try {
    if (in_array($caso, ['ausente','interno'], true)) {
        try { new BancoSemConexao('doacao'); } catch (RuntimeException $e) { exit(0); }
        throw new RuntimeException('Configuração insegura foi aceita.');
    }
    $banco = new BancoSemConexao('doacao');
    foreach (['local'=>'DB_HOST','db'=>'DB_NAME','user'=>'DB_USER','password'=>'DB_PASS'] as $propriedade=>$chave) {
        $p = new ReflectionProperty(Database::class, $propriedade); $p->setAccessible(true);
        if ($p->getValue($banco) !== $fixture[$chave]) throw new RuntimeException('Mapeamento de configuração inválido.');
    }
    $email = require __DIR__ . '/../utils/configEmail.php';
    foreach (['host'=>'SMTP_HOST','porta'=>'SMTP_PORT','usuario'=>'SMTP_USER','senha'=>'SMTP_PASS','remetente'=>'SMTP_FROM','nome_remetente'=>'SMTP_FROM_NAME'] as $propriedade=>$chave) {
        if ((string)$email[$propriedade] !== $fixture[$chave]) throw new RuntimeException('Interface SMTP incompatível.');
    }
    if (!is_file(__DIR__ . '/../vendor/autoload.php')) throw new RuntimeException('Dependência de e-mail ausente.');
} finally { if ($temporario !== null) unlink($temporario); }
