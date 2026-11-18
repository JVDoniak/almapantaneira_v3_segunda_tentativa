<?php
require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/Endereco.php';

class Doador {
    public $id_doador;
    public $tipo;
    public $nome;
    public $cpf_cnpj;
    public $contato_empresa;
    public $razao_social;
    public $nome_fantasia;
    public $telefone;
    public $email;
    public $id_usuario;
    public $id_endereco;
    public $status_doador;

    public function cadastrar() {
        $db = new Database('doador');

        return $db->insert([
            'tipo'            => $this->tipo,
            'nome'            => $this->nome,
            'cpf_cnpj'        => $this->cpf_cnpj,
            'contato_empresa' => $this->contato_empresa,
            'razao_social'    => $this->razao_social,
            'telefone'        => $this->telefone,
            'email'           => $this->email,
            'id_endereco'     => $this->id_endereco
        ]);
    }
    public function editar() {
        $db = new Database('doador');
        
        return $db->update([
            'id_doador'       =>$this->id_doador,
            'tipo'            => $this->tipo,
            'nome'            => $this->nome,
            'cpf_cnpj'        => $this->cpf_cnpj,
            'contato_empresa' => $this->contato_empresa,
            'razao_social'    => $this->razao_social,
            'telefone'        => $this->telefone,
            'email'           => $this->email,
            'id_endereco'     => $this->id_endereco
        ]);
    }
    public function buscar(){
        $db = new Database('doador');

        $stmt = $db->select();

        return $stmt;
    }

    public function buscarDoadorCpfCnpj($cpf_cnpj){
        $db = new Database('doador');

        $stmt = $db->select_all_with_where("cpf_cnpj = $cpf_cnpj");

        return $stmt;
    }
    
    public function listarTodos() {
        $db = new Database('doador');
        return $db->select();
    }

    public function buscar_doador_com_endereco($id_doador) {
        $db = new Database('doador');
        return $db->select_doadores_with_enderecos($id_doador);
    }

    public function atualizarStatus($id_doador, $status) 
    {
        $db = new Database('doador');

        $res = $db->update_in_id($id_doador, [
            "status_doador" => $status
        ]);

        return $res;
    }

    public function buscarPorStatus() {
            $db = new Database('doador');

            $order = "ORDER BY status_doador DESC";

            return $db->select_order_by($order);
        }

    public function listarComFiltros($filtros = []) {
        $db = new Database('doador');
        
        $where = [];
        
        if (!empty($filtros['nome']))
            $where['nome'] = addslashes($filtros['nome']);
        
        if (!empty($filtros['cpf_cnpj']))
            $where['cpf_cnpj'] = preg_replace('/\D/', '', $filtros['cpf_cnpj']);

        if (!empty($filtros['tipo']))
            $where['tipo'] = ($filtros['tipo'] === 'Pessoa Física') ? 'Pessoa Física' : 'Pessoa Jurídica';

        $where['status_doador'] = $filtros['status_doador'];

        return $db->select_doadores_com_filtros($where);
    }
}