<?php
session_start();

$recuperacao = $_SESSION['recuperacao'] ?? null;

// Só acessa esta tela quem validou o código (ou entrou no primeiro acesso) e dentro do prazo
if (empty($recuperacao['validado']) || time() > $recuperacao['expira_em']) {
    $eraPrimeiroAcesso = !empty($recuperacao['primeiro_acesso']);
    unset($_SESSION['recuperacao']);
    header($eraPrimeiroAcesso ? 'Location: login.php?erro=expirado' : 'Location: recuperacao-senha.php?erro=expirado');
    exit;
}

// Mesmo formulário para os dois casos; só o texto muda
$primeiroAcesso = !empty($recuperacao['primeiro_acesso']);

$mensagensErro = [
    'tamanho'     => 'A senha deve ter no mínimo 6 caracteres.',
    'confirmacao' => 'As senhas não conferem.',
    'banco'       => 'Não foi possível alterar a senha. Tente novamente.',
];
$erro = $mensagensErro[$_GET['erro'] ?? ''] ?? null;
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style-unico.css">
    <script src="../js/recuperacao-senha.js" defer></script>
    <title>Nova Senha - Alma Pantaneira</title>
</head>

<body>

    <section id="principal">

        <div class="logo">
            <img src="../imgs/logo1.png" alt="Logo Médicos do Pantanal">
            <h1>Alma Pantaneira</h1>
        </div>

        <div class="form">

            <div class="header-recuperacao">
                <?php if ($primeiroAcesso): ?>
                    <h2>Primeiro Acesso</h2>
                    <p>Por segurança, crie uma nova senha antes de entrar no sistema.</p>
                <?php else: ?>
                    <h2>Nova Senha</h2>
                    <p>Crie uma nova senha para acessar o sistema.</p>
                <?php endif; ?>
            </div>

            <form id="formNovaSenha" action="../action/nova-senha.php" method="POST">

                <div id="alertaNovaSenha" class="alert alert-warning <?php echo $erro ? 'visivel' : ''; ?>" role="alert">
                    <?php echo htmlspecialchars($erro ?? ''); ?>
                </div>

                <div class="input">
                    <label for="senha">Nova Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Mínimo de 6 caracteres" minlength="6" autocomplete="new-password" required>
                </div>

                <div class="input">
                    <label for="confirmar_senha">Confirmar Senha</label>
                    <input type="password" id="confirmar_senha" name="confirmar_senha" placeholder="Digite a senha novamente" minlength="6" autocomplete="new-password" required>
                </div>

                <div class="entrar">
                    <button type="submit">Salvar Nova Senha</button>
                </div>

                <div class="btn-voltar">
                    <a href="login.php">
                        <button type="button">Cancelar</button>
                    </a>
                </div>

            </form>

        </div>

    </section>

</body>

</html>