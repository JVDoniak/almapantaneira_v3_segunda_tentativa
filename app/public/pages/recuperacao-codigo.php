<?php
session_start();

// Só acessa esta tela quem informou o e-mail antes
if (empty($_SESSION['recuperacao'])) {
    header('Location: recuperacao-senha.php?erro=sessao');
    exit;
}

// Se o código já foi validado, segue para a nova senha
if (!empty($_SESSION['recuperacao']['validado'])) {
    header('Location: recuperacao-nova-senha.php');
    exit;
}

$mensagensErro = [
    'incompleto' => 'Preencha os 5 dígitos do código.',
    'invalido'   => 'Código inválido. Verifique e tente novamente.',
];
$erro = $mensagensErro[$_GET['erro'] ?? ''] ?? null;

$email = $_SESSION['recuperacao']['email'];
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style-unico.css">
    <script src="../js/recuperacao-senha.js" defer></script>
    <title>Código de Verificação - Alma Pantaneira</title>
</head>

<body>

    <section id="principal">

        <div class="logo">
            <img src="../imgs/logo1.png" alt="Logo Médicos do Pantanal">
            <h1>Alma Pantaneira</h1>
        </div>

        <div class="form">

            <div class="header-recuperacao">
                <h2>Código de Verificação</h2>
                <p>Digite o código de 5 dígitos enviado para <strong><?php echo htmlspecialchars($email); ?></strong></p>
            </div>

            <form id="formCodigo" action="../action/validar-codigo.php" method="POST">

                <div class="alert alert-warning <?php echo $erro ? 'visivel' : ''; ?>" role="alert">
                    <?php echo htmlspecialchars($erro ?? ''); ?>
                </div>

                <div class="codigo-inputs">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <input
                            type="text"
                            name="codigo<?php echo $i; ?>"
                            class="input-codigo"
                            maxlength="1"
                            inputmode="numeric"
                            pattern="[0-9]"
                            aria-label="Dígito <?php echo $i; ?> do código"
                            <?php echo $i === 1 ? 'autocomplete="one-time-code" autofocus' : 'autocomplete="off"'; ?>
                            required>
                    <?php endfor; ?>
                </div>

                <div class="entrar">
                    <button type="submit">Validar Código</button>
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
                <p>O código é válido por 15 minutos. Não recebeu? Confira a caixa de spam ou <a href="recuperacao-senha.php">solicite um novo código</a>.</p>
            </div>

        </div>

    </section>

</body>

</html>
