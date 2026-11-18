<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/style-unico.css">
    <title>Login - Alma Pantaneira</title>
</head>

<body>

    <section id="principal">

        <div class="logo">
            <img src="../imgs/logo1.png" alt="Logo Médicos do Pantanal">
            <h1>Alma Pantaneira</h1>
        </div>

        <div class="form">

            <form id="formCadastro" action="../action/login.php" method="POST">
                
                <div class="alert alert-sucesso <?php echo (isset($_GET['sucesso']) && $_GET['sucesso'] === 'senha') ? 'visivel' : ''; ?>" role="alert">
                    Senha alterada com sucesso! Faça login com a nova senha.
                </div>

                <div class="alert alert-warning <?php echo (isset($_GET['erro']) && $_GET['erro'] == 1) ? 'visivel' : ''; ?>" role="alert">
                    E-mail ou senha incorretos. Verifique seus dados!
                </div>

                <div class="input">
                    <label for="email">E-mail/CPF</label>
                    <input type="text" id="email" name="email" placeholder="Digite seu Email ou CPF">
                </div>
                <div class="input">
                    <label for="senha">Senha</label>
                    <input type="password" id="senha" name="senha" placeholder="Digite sua Senha">
                </div>

                <div class="esqueceu-senha">
                    <a href="recuperacao-senha.php">Esqueceu sua Senha?</a>
                </div>

                <div class="entrar">
                    <!-- Nota: Evite colocar <button> dentro de tag <a>, pois o botão já faz a submissão -->
                    <button name="REQUEST_METHOD" type="submit">Entrar</button>
                </div>

            </form>
            
        </div>

    </section>
</body>

</html>