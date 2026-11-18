<?php

require_once __DIR__ . '/../model/Database.php';

class Login {
    public string $email;
    public string $senha;

    public function entrar(){
        $db = new Database("usuario");

        
        $user = $db->select_one_with_where("email = '{$this->email}'");

                if ($user) {
                    if (password_verify($this->senha, $user['senha'])) {
                        
                        return $user;

                    } else {

                        return false;

                    }
                } else {
                    return false;
                }
    }

    public function precisaTrocarSenha(array $usuario): bool
    {
        return (int) ($usuario['primeiro_acesso'] ?? 0) === 1;
    }
}

?>