<?php

require_once __DIR__ . '/../model/Database.php';

    class Fornecedor {
        
        public ?int $id_fornecedor;
        public string $nome;
        public string $razao_social;
        public string $cpf_cnpj;
        public string $telefone;
        public string $email;
        public string $contato;
        public ?int $id_endereco;   


    public function cadastrar()
    {
        $db = new Database('fornecedor');

            $res = $db->insert([
                "nome" => $this->nome,
                "razao_social" => $this->razao_social,
                "cpf_cnpj" => $this->cpf_cnpj,
                "telefone" => $this->telefone,
                "email" => $this->email,
                "contato" => $this->contato,
                "id_endereco" => $this->id_endereco
            ]);   

            return $res;
        }	
        
        public function buscar($nome = '', $cnpj = '', $status = '') 
        {
            $db = new Database('fornecedor'); 
            
            return $db->buscarFornecedores($nome, $cnpj, $status);
        }

        public function buscarPorStatus() {

            $db = new Database('fornecedor');

            $order = "ORDER BY status_fornecedor DESC";

            return $db->select_order_by($order);

        }

        public function buscarFornecedorComEndereco($id_fornecedor, $id_endereco){
            $db = new Database('fornecedor');
            
            $stmt = $db->select_fornecedores_with_enderecos($id_fornecedor, $id_endereco);

            if ($stmt) {
                return $stmt->fetch(PDO::FETCH_ASSOC);
            }

            return [];
        }

        public function buscar_por_id()
        {
            $db = new Database('fornecedor');

            $res = $db->select_all_with_where("id_fornecedor = '{$_SESSION["fornecedor_id"]}'");

            return $res;
        }

        public function editar()
        {
            $db = new Database('fornecedor');

            $res = $db->update([
                "id_fornecedor" => $this->id_fornecedor,
                "nome" => $this->nome,
                "razao_social" => $this->razao_social,
                "cpf_cnpj" => $this->cpf_cnpj,
                "telefone" => $this->telefone,
                "email" => $this->email,
                "contato" => $this->contato,
                "id_endereco" => $this->id_endereco
            ]);

            return $res;
        }

        public function atualizarStatus($id_fornecedor, $status) 
        {
            $db = new Database('fornecedor');

            $res = $db->update_in_id($id_fornecedor, [
                "status_fornecedor" => $status
            ]);

            return $res;
        }
        
    }

