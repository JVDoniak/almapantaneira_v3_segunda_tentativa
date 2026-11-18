<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/Endereco.php';

class RegraEnderecoDoador
{
    const CAMPOS = ['cep', 'logradouro', 'numero', 'complemento', 'bairro', 'cidade', 'estado'];

    public static function centavos($valor)
    {
        if (!is_scalar($valor) || !preg_match('/^\d{1,8}(?:\.\d{1,2})?$/D', (string) $valor)) return null;
        $partes = explode('.', (string) $valor);
        return (int) $partes[0] * 100 + (int) str_pad($partes[1] ?? '', 2, '0');
    }

    public static function dados($entrada)
    {
        $dados = [];
        foreach (self::CAMPOS as $campo) {
            $valor = $entrada[$campo] ?? '';
            $dados[$campo] = is_string($valor) ? trim($valor) : '';
        }
        $dados['cep'] = preg_replace('/\D/', '', $dados['cep']);
        return $dados;
    }

    public static function informado($dados)
    {
        return count(array_filter($dados, static function ($valor) { return $valor !== ''; })) > 0;
    }

    public static function completo($dados)
    {
        if (!is_array($dados) || !preg_match('/^\d{8}$/D', (string) ($dados['cep'] ?? ''))) return false;
        foreach (['logradouro', 'numero', 'bairro', 'cidade', 'estado'] as $campo) {
            if (trim((string) ($dados[$campo] ?? '')) === '') return false;
        }
        return true;
    }

    public static function erroPreenchimento($dados)
    {
        if (!self::informado($dados)) return null;
        if (!self::completo($dados)) return 'Preencha o endereço completo ou deixe a seção de endereço vazia.';
        if (!Endereco::numeroValido($dados['numero'])) {
            return 'Informe o número do endereço com até 20 caracteres.';
        }
        return null;
    }

    public static function erroDoacao($idDoador, $valor)
    {
        $centavos = self::centavos($valor);
        if ($centavos === null || $centavos <= 0) return 'Informe um valor válido para a doação.';
        if ($centavos < 100000) return null;
        $db = new Database('doador');
        $stmt = $db->execute('SELECT e.cep, e.logradouro, e.numero, e.bairro, e.cidade, e.estado
            FROM doador d LEFT JOIN endereco e ON e.id_endereco = d.id_endereco WHERE d.id_doador = ?', [$idDoador]);
        $endereco = $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
        if (!$endereco || !self::completo($endereco)) {
            return 'Para doações a partir de R$ 1.000,00, complete o endereço no cadastro do doador antes de salvar a doação.';
        }
        return null;
    }

    public static function possuiDoacaoAlta($idDoador)
    {
        $db = new Database('doacao');
        $stmt = $db->execute('SELECT id_doacao FROM doacao WHERE id_doador = ? AND valor >= ? LIMIT 1', [$idDoador, '1000.00']);
        if (!$stmt) throw new RuntimeException('Não foi possível verificar as doações do doador.');
        return $stmt->fetchColumn() !== false;
    }

    public static function salvar($dados, $idEndereco = null)
    {
        if (!self::informado($dados)) return null;
        $endereco = new Endereco();
        foreach (self::CAMPOS as $campo) $endereco->$campo = $dados[$campo];
        if ($idEndereco) {
            $endereco->id_endereco = (int) $idEndereco;
            return $endereco->editar() !== false ? (int) $idEndereco : false;
        }
        $id = $endereco->cadastrar();
        return $id ? (int) $id : false;
    }
}
