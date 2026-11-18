<?php
require_once __DIR__ . '/../model/Database.php';

// Fixa/Variável: classificação provisória, sujeita à validação da cliente.
class TipoDespesa
{
    public function listarTodas()
    {
        $db = new Database('tipo_despesa');
        return $db->select_all_with_where('1 = 1 ORDER BY id_tipo_despesa', 'id_tipo_despesa, descricao');
    }

    public function existe($id)
    {
        $db = new Database('tipo_despesa');
        $stmt = $db->execute('SELECT id_tipo_despesa FROM tipo_despesa WHERE id_tipo_despesa = ?', [$id]);
        return $stmt && $stmt->fetchColumn() !== false;
    }
}
