<?php

require_once __DIR__ . '/../model/Database.php'; 

    class Endereco {

        public int $id_endereco;
        public string $cep;
        public string $logradouro;
        public string $numero;
        public string $complemento;
        public string $bairro;
        public string $cidade;
        public string $estado;
        public int $id_fornecedor;

        public static function numeroValido($numero) {
            return is_string($numero) && trim($numero) !== ''
                && preg_match('//u', $numero) === 1
                && preg_match_all('/./us', $numero) <= 20;
        }

        public function cadastrar(){
            if (!self::numeroValido($this->numero)) return false;
            $db = new Database('endereco');

            $id_endereco_cadastrado =  $db->insertLastId([
                                    'cep' => $this->cep,
                                    'logradouro' => $this->logradouro,
                                    'numero' => $this->numero,
                                    'complemento' => $this->complemento,
                                    'bairro' => $this->bairro,
                                    'cidade' => $this->cidade,
                                    'estado' => $this->estado
                                ]);

            return $id_endereco_cadastrado;
        }

        public function buscar()
        {
            $db = new Database('endereco');

            $stmt = $db->select();

            return $stmt;
        }

        public function editar()
        {
            if (!self::numeroValido($this->numero)) return false;

            $db = new Database('endereco');

            $res = $db->update([
                'id_endereco' => $this->id_endereco,
                'cep' => $this->cep,
                'logradouro' => $this->logradouro,
                'numero' => $this->numero,
                'complemento' => $this->complemento,
                'bairro' => $this->bairro,
                'cidade' => $this->cidade,
                'estado' => $this->estado
            ]);

            return $res;
        }

        public function buscar_por_id() 
        {

            $db = new Database('endereco');

            $res = $db->select_all_with_where("id_endereco = '{$_SESSION["endereco_id"]}'");

            return $res;
        }

        public function buscarEndereco($id_endereco){
            $db = new Database('endereco');

            $res = $db->select_one_with_where("id_endereco = {$id_endereco}");

            if ($res) {
                return $res;
            } else {
                return false;
            }
        }

        
    }   