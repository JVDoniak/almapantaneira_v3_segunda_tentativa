<?php

require_once __DIR__ . '/../model/Database.php';

class Projeto
{
    public int $id_projeto;
    public string $nome;
    public string $responsavel;
    public string $local_projeto;
    public string $tipo_projeto;
    public float $valor_projeto;
    public string $data_inicio;
    public string $data_termino;
    public string $andamento;
    public string $descricao;   
    public ?int $id_usuario;

    public function cadastrar()
    {
        $db = new Database('projeto');

        $res = $db->insert([
            "nome" => $this->nome,
            "responsavel" => $this->responsavel,
            "local_projeto" => $this->local_projeto,
            "tipo_projeto" => $this->tipo_projeto,
            "valor_projeto" => $this->valor_projeto,
            "data_inicio" => date($this->data_inicio),
            "data_termino" => date($this->data_termino),
            "andamento" => $this->andamento,
            "descricao" => $this->descricao,
        ]);

        return $res;
        }

        public function buscar_por_id($id_projeto)
         {
            $db = new Database('projeto');
            $res = $db->select_one_with_where("id_projeto = $id_projeto");
            return $res;
        }

        public function listarTodos() {
            $db = new Database('projeto');
            return $db->select();
        }
    
       public function buscarProjetoDoacao(){
        $db = new Database('projeto');

        $stmt = $db->select_projetos_with_doacao();

        $res = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return $res;
    }

       public function editar()
    {
        $db = new Database('projeto');

        $res = $db->update_in_id($this->id_projeto, [
            "nome" => $this->nome,
            "responsavel" => $this->responsavel,
            "local_projeto" => $this->local_projeto,
            "tipo_projeto" => $this->tipo_projeto,
            "data_inicio" => date($this->data_inicio),
            "data_termino" => date($this->data_termino),
            "andamento" => $this->andamento,
            "descricao" => $this->descricao
        ]);

        return $res;
    }

    public function editar_por_id($id_projeto)
    {
        $db = new Database('projeto');

        $res = $db->select_one_with_where("id_projeto = '{$id_projeto}'");
        
        if ($res){
            return $res;
        }else{
            return false;
        }
    }


    // Lista TODOS os projetos (com ou sem doação ainda) — usado para popular selects
    public function buscarParaSelect(){
        $db = new Database('projeto');

        $res = $db->select_all_with_where("status_projeto = 1", "id_projeto, nome, tipo_projeto, data_inicio, andamento, responsavel, local_projeto, status_projeto");

        return $res;
    }

    public function atualizarStatus($id_projeto, $status) 
    {
        $db = new Database('projeto');

        $res = $db->update_in_id($id_projeto, [
            "status_projeto" => $status
        ]);

        return $res;
    }

    public function buscarPorStatus() {
            $db = new Database('projeto');
            $order = "ORDER BY status_projeto DESC";
            $res = $db->select_order_by($order);
            return $res;
    }


    public function buscarProjetosFiltrados($nome = '', $data_inicio = '', $tipo = '', $status = '')
    {
        $db = new Database('projeto');

        return $db->buscarProjetosFiltrados($nome, $data_inicio, $tipo, $status);
    }

}

?>
