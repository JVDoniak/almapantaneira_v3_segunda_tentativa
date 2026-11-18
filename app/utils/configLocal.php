<?php
// Configuração privada externa ao projeto ou variáveis do processo/Apache.
function almaConfiguracao($chave, $padrao = null)
{
    static $locais = null;
    if ($locais === null) {
        $locais = [];
        $arquivo = getenv('ALMA_CONFIG_FILE');
        if ($arquivo !== false && $arquivo !== '') {
            $real = realpath($arquivo);
            $raiz = realpath(dirname(__DIR__, 2));
            if (!$real || !is_readable($real)
                || strncasecmp(str_replace('\\', '/', $real), str_replace('\\', '/', $raiz) . '/', strlen($raiz) + 1) === 0) {
                throw new RuntimeException('Use um arquivo de configuração privado fora do projeto.');
            }
            $locais = json_decode(file_get_contents($real), true, 512, JSON_THROW_ON_ERROR);
            if (!is_array($locais)) throw new RuntimeException('Configuração local inválida.');
        }
    }
    $valor = getenv($chave);
    if ($valor === false) $valor = array_key_exists($chave, $locais) ? $locais[$chave] : $padrao;
    if ($valor === null || !is_scalar($valor)) throw new RuntimeException('Configure a variável ' . $chave . '.');
    return (string) $valor;
}
