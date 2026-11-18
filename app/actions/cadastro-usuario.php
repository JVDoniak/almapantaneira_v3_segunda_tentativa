<?php

require '../controller/Usuario.php';

header('Content-Type: application/json');

// Remove tudo que não for número (pontos, traços, parênteses, espaços)
function somenteNumeros($valor) {
    return preg_replace('/\D/', '', $valor ?? '');
}

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $nome      = $_POST['nome'] ?? '';
    $cpf       = somenteNumeros($_POST['cpf'] ?? '');       // limpa antes de atribuir ao objeto
    $telefone  = somenteNumeros($_POST['telefone'] ?? '');  // limpa antes de atribuir ao objeto
    $email     = $_POST['email'] ?? '';
    $senha = str_pad( (string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $tipo      = $_POST['tipo'] ?? '';
    $status_usuario = $_POST['status_usuario'];

    $objUsuario = new Usuario();
    $objUsuario->nome     = $nome;
    $objUsuario->cpf      = $cpf;
    $objUsuario->telefone = $telefone; 
    $objUsuario->email    = $email;
    $objUsuario->senha = password_hash($senha, PASSWORD_DEFAULT);
    $objUsuario->tipo     = $tipo;
    $objUsuario->status_usuario     = $status_usuario;

    $res = $objUsuario->cadastrar();

    if ($res) {

        // Envia a senha temporária somente após o cadastro
        $emailService = new Email();

        $enviado = $emailService->enviarSenhaPrimeiroAcesso($email, $senha);

        if ($enviado) {

            echo json_encode([
                'success' => $res,
                'message' => $res ? 'Cadastrado com Sucesso' : 'Não foi cadastrado'
            ]);
        } else {
            echo json_encode([
                'success' => $res,
                'message' => $res ? 'Cadastrado com Sucesso' : 'Não foi cadastrado'
            ]);
        }

    } else {
            echo json_encode([
                'success' => $res,
                'message' => $res ? 'Cadastrado com Sucesso' : 'Não foi cadastrado'
            ]);
    }

        exit;
    }