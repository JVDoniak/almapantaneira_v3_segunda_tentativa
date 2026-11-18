<?php
require '../controller/Usuario.php';
header('Content-Type: application/json');

// Aplica a máscara 999.999.999-99 no CPF
function formatarCpf($cpf) {
    $cpf = preg_replace('/\D/', '', $cpf ?? '');
    if (strlen($cpf) !== 11) {
        return $cpf; // se estiver incompleto, devolve como está
    }
    return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $cpf);
}

// Aplica a máscara (99) 99999-9999 ou (99) 9999-9999 no telefone
function formatarTelefone($telefone) {
    $telefone = preg_replace('/\D/', '', $telefone ?? '');
    if (strlen($telefone) === 11) {
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    }
    if (strlen($telefone) === 10) {
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }
    return $telefone; // tamanho inesperado, devolve como está
}

if( isset($_GET['id']) ){

    $id = $_GET['id'];
    $objUser = new Usuario();
    $dados = $objUser->editar_por_id($id);

    if( $dados == false ){
        $array = [
            "status" => 400,
            "msg" => "Usuário inexistente!!",
        ];
    }else{
        // Aplica as máscaras antes de enviar os dados para o JS
        $dados['cpf']      = formatarCpf($dados['cpf'] ?? '');
        $dados['telefone'] = formatarTelefone($dados['telefone'] ?? '');

        // Não envia a senha para o navegador
        unset($dados['senha']);

        $array = [
            "status" => 200,
            "msg" => "Dados requisitados com sucesso!",
            "data" => $dados
        ];
    }
    echo json_encode($array);
    exit;
}

if(isset($_POST) && isset($_POST['cpf'])){

    $id_usuario = $_POST['id_usuario'];
    $nome      = $_POST['nome'] ?? '';
    $email     = $_POST['email'] ?? '';
    $senha     = $_POST['senha'] ?? '';
    $confirmar = $_POST['confirmarSenha'] ?? '';
    $tipo      = $_POST['cargo'] ?? '';

    // Deixa somente números (remove pontos, traços, parênteses e espaços)
    $cpf      = preg_replace('/\D/', '', $_POST['cpf'] ?? '');
    $telefone = preg_replace('/\D/', '', $_POST['telefone'] ?? '');

    // Valida o tamanho antes de salvar, para não gravar dado cortado
    if (strlen($cpf) !== 11 || strlen($telefone) < 10 || strlen($telefone) > 11) {
        echo json_encode([
            "status" => 400,
            "msg" => "CPF deve ter 11 dígitos e o telefone 10 ou 11 dígitos."
        ]);
        exit;
    }

    // Senha é opcional na edição: só valida se algum dos dois campos foi preenchido
    if ($senha !== '' || $confirmar !== '') {
        if ($senha !== $confirmar) {
            echo json_encode([
                "status" => 400,
                "msg" => "As senhas não coincidem."
            ]);
            exit;
        }
    }

    $objUsuario = new Usuario();
    $objUsuario->id_usuario = $id_usuario;
    $objUsuario->nome = $nome;
    $objUsuario->cpf = $cpf;
    $objUsuario->telefone = $telefone;
    $objUsuario->email = $email;
    $objUsuario->senha = $senha; // vazio = editar() mantém a senha atual
    $objUsuario->tipo = $tipo;

    try {
        $res = $objUsuario->editar();

        // execute() devolve false quando o UPDATE falha
        if ($res === false) {
            $array = [
                "status" => 400,
                "msg" => "Não foi possível editar o usuário."
            ];
        } else {
            $array = [
                "status" => 200,
                "msg" => "Usuário editado com sucesso!"
            ];
        }
        echo json_encode($array);
    } catch (Exception $err) {

        $array = [
            "status" => 400,
            "msg" => $err->getMessage()
        ];
    }
}
