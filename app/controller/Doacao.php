<?php
require_once __DIR__ . '/../model/Database.php';


class Doacao {
    public ?int $id_doacao;
    public ?int $id_doador;
    public string $tipo;
    public ?int $id_projeto = null;
    public string $descricao;
    public string $valor;
    public string $forma_pagamento;
    public string $data_doacao;

    public function doadorExiste($idDoador)
    {
        $db = new Database('doador');
        $stmt = $db->execute('SELECT id_doador FROM doador WHERE id_doador = ?', [$idDoador]);
        return $stmt && $stmt->fetchColumn() !== false;
    }

    // Método para cadastrar a doação
    public function cadastrar() {
        $db = new Database('doacao');

        $res = $db->insert([
            "id_doador"       => $this->id_doador,
            "tipo"            => $this->tipo,
            "id_projeto"      => $this->id_projeto,
            "descricao"       => $this->descricao,
            "valor"           => $this->valor,
            "forma_pagamento" => $this->forma_pagamento,
            "data_doacao"     => $this->data_doacao
        ]);

        return $res;
    }


    
    public function buscarDoacoes(){

            $db = new Database('doacao');
            
            return $db->select_doacoes_with_projetos();
    }

//BUSCAR POR ID PARA EDITAR POR ID
    public function editar()
    {
        $db = new Database('doacao');

        $res = $db->update([
            "id_doacao" => $this->id_doacao,
            "tipo" => $this->tipo,
            "descricao" => $this->descricao,
            "valor" => $this->valor,
            "forma_pagamento" => $this->forma_pagamento,
            "data_doacao" => $this->data_doacao,
            "id_doador" => $this->id_doador,
            "id_projeto" => $this->id_projeto
        ]);

        return $res;
    }

    public function buscarDoacaoPorId($id_doacao)
    {
         $db = new Database('doacao');
            $res = $db->select_doacao_doador($id_doacao); 

            return $res ? $res : false;
    }

    public function atualizarStatus($id_doacao, $status) 
        {
            $db = new Database('doacao');

            $res = $db->update_in_id($id_doacao, [
                "status_doacao" => $status
            ]);

            return $res;
        }

    public function buscarPorStatus() {

        $db = new Database('doacao');

        $order = "ORDER BY status_doacao DESC";

        return $db->select_order_by($order);

    }

}
