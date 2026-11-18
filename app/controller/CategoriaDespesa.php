<?php

require_once __DIR__ . '/../model/Database.php';

class CategoriaDespesa
{
    public int $id_categoria_despesa;
    public string $descricao;

    public function cadastrar()
    {
        $db = new Database('categoria_despesa');

        $res = $db->insert([
            "descricao" => $this->descricao
        ]);

        if (!$res) return false;
        $stmt = $db->execute('SELECT LAST_INSERT_ID()');
        return $stmt ? (int) $stmt->fetchColumn() : false;
    }

    
    public function listarTodas() {

        $db = new Database('categoria_despesa');
        return $db->select();

    }
}
?>