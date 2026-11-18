<?php
$mensagensErro = [
    'email'      => 'Informe um e-mail válido.',
    'envio'      => 'Não foi possível enviar o e-mail agora. Tente novamente em alguns minutos.',
    'expirado'   => 'O código expirou. Solicite um novo código.',
    'tentativas' => 'Você errou o código muitas vezes. Solicite um novo código.',
    'sessao'     => 'Sua solicitação expirou. Informe o e-mail novamente.',
];
$erro = $mensagensErro[$_GET['erro'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style-unico.css">
    <title>Recuperação de Senha - Alma Pantaneira</title>
</head>

<body>

    <section id="principal">

        <div class="logo">
            <img src="../imgs/logo1.png" alt="Logo Médicos do Pantanal">
            <h1>Alma Pantaneira</h1>
        </div>

        <div class="form">

            <div class="header-recuperacao">
                <h2>Recuperação de Senha</h2>
                <p>Informe seu e-mail cadastrado para receber as instruções.</p>
            </div>

            <form action="../action/recuperar.php" method="POST">

                <div class="alert alert-warning <?php echo $erro ? 'visivel' : ''; ?>" role="alert">
                    <?php echo htmlspecialchars($erro ?? ''); ?>
                </div>

                <div class="input">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="exemplo@example.invalid" required>
                </div>

                <div class="entrar">
                    <button type="submit">Enviar</button>
                </div>

                <div class="btn-voltar">
                    <a href="login.php">
                        <button type="button">Voltar para o Login</button>
                    </a>
                </div>

            </form>

            <div class="exclamation">
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-circle" viewBox="0 0 16 16">
                    <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14m0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16"/>
                    <path d="M7.002 11a1 1 0 1 1 2 0 1 1 0 0 1-2 0M7.1 4.995a.905.905 0 1 1 1.8 0l-.35 3.507a.552.552 0 0 1-1.1 0z"/>
                </svg>
                <p>Se o e-mail estiver cadastrado, você receberá um código para redefinir sua senha.</p>
            </div>

        </div>

    </section>

</body>

</html>
