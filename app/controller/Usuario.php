<?php

require_once __DIR__ . '/../model/Database.php';
require_once __DIR__ . '/Email.php';

class Usuario
{
    public ?int $id_usuario = null;
    public string $nome;
    public string $cpf;
    public string $telefone;
    public string $email;
    public string $senha;
    public string $tipo;
    public ?string $foto;
    public string $status_usuario;


    public function cadastrar()
    {
        $db = new Database('usuario');

        $res = $db->insert([
            "nome" => $this->nome,
            "cpf" => $this->cpf,
            "telefone" => $this->telefone,
            "email" => $this->email,
            "senha" => $this->senha,
            "tipo" => $this->tipo,
            "status_usuario" => $this->status_usuario,
        ]);

        return $res;
    }

    public function buscar()
    {
        $db = new Database('usuario');

        $stmt = $db->select();

        return $stmt;
    }

    public function buscarFiltrado($nome = '', $cargo = '', $email = '')
    {
        $db = new Database('usuario');

        return $db->buscarUsuariosFiltrados(
            $nome,
            $cargo,
            $email
        );
    }

    // NOVO: remove acentos para a busca funcionar com "jose" e "José"
    private function removerAcentos($string)
    {
        $mapaAcentos = [
            'á' => 'a', 'à' => 'a', 'ã' => 'a', 'â' => 'a', 'ä' => 'a',
            'é' => 'e', 'è' => 'e', 'ê' => 'e', 'ë' => 'e',
            'í' => 'i', 'ì' => 'i', 'î' => 'i', 'ï' => 'i',
            'ó' => 'o', 'ò' => 'o', 'õ' => 'o', 'ô' => 'o', 'ö' => 'o',
            'ú' => 'u', 'ù' => 'u', 'û' => 'u', 'ü' => 'u',
            'ç' => 'c', 'ñ' => 'n'
        ];
        return strtr($string, $mapaAcentos);
    }

    public function buscar_por_id($id_user)
    {
        $db = new Database('usuario');
        $res = $db->select_all_with_where("id_usuario = $id_user");

        return $res;
    }

    
  public function editar()
    {
        $db = new Database('usuario');

        // Campos que sempre são atualizados
        $dados = [
            "id_usuario" => $this->id_usuario,
            "nome"       => $this->nome,
            "cpf"        => $this->cpf,
            "telefone"   => $this->telefone,
            "email"      => $this->email,
            "tipo"       => $this->tipo
        ];


        if (isset($this->senha) && $this->senha !== '') {
            $dados["senha"] = $this->senha;
        }

        $res = $db->update($dados);

        return $res;
    }

        
    public function editarPerfil()
    {
        $db = new Database('usuario');
        $dados = [
            "id_usuario" => $this->id_usuario,
            "nome" => $this->nome,
            "telefone" => $this->telefone
        ];

        // Adiciona a senha somente se houver uma nova senha
        if (!empty($this->senha)) {
            $dados["senha"] = $this->senha;
        }

        // Inclui a foto somente se houver uma nova imagem
        if (!empty($this->foto)) {
            $dados["foto"] = $this->foto;
        }

        $res = $db->update($dados);

        return $res;
    }

    public function carregarPerfil($id_user)
    {
        $db = new Database('usuario');

        $res = $db->select_perfil("id_usuario = '{$id_user}'");
        // print_r($res);
        if ($res){
            return $res;
        }else{
            return false;
        }
    }

    public function atualizarStatus($id_usuario, $status) 
    {
        $db = new Database('usuario');

        $res = $db->update_in_id($id_usuario, [
            "status_usuario" => $status
        ]);

        return $res !== false;
    }

    public function verificarEmail()
        {
            try {
                $emailSeguro = addslashes($this->email);

                $db  = new Database('usuario');
                $res = $db->select_one_with_where("email = '{$emailSeguro}'");

                return $res;
            } catch (Exception $e) {
                error_log('Erro ao verificar e-mail: ' . $e->getMessage());
                return false;
            }
        }

    public function atualizarSenha($id_user)
        {
            try {
                $emailSeguro = addslashes($this->email);

                $db = new Database('usuario');

                $result = $db->update([
                        'id_usuario' => $id_user,
                        'email'           => $emailSeguro,
                        'senha'           => $this->senha,
                        'primeiro_acesso' => 0
                    ]);

                return $result ? true : false;

            } catch (Exception $e) {
                error_log('Erro ao atualizar senha: ' . $e->getMessage());
                return false;
            }
        }

}
