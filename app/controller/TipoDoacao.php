<?php

require_once __DIR__ . '/../model/Database.php';

class TipoDoacao
{
    public int $id_tipo_doacao;
    public string $descricao;

    public function cadastrar()
    {
        $db = new Database('tipo_doacao');

        $res = $db->insert([
            "descricao" => $this->descricao
        ]);

        return $res;
    }

    
    public function listarTodas() {

        $db = new Database('tipo_doacao');
        return $db->select();

    }
    
}
?>