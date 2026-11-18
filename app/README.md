# Alma Pantaneira - Layout PHP com HTML e CSS

Sistema de Gestão de Recursos Financeiros do Instituto Alma Pantaneira - Médicos do Pantanal

Projeto padronizado com `includes` em PHP para reaproveitar o menu lateral e o menu superior.

## Exemplo de página

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Alma Pantaneira</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="../css/estilo-padrao/global.css">
    <link rel="stylesheet" href="../css/estilo-padrao/layout.css">
    <link rel="stylesheet" href="../css/estilo-padrao/forms.css">
    <link rel="stylesheet" href="../css/estilo-padrao/buttons.css">
</head>
<body>
    <div class="app-layout">
        <?php include '../components/sidebar.php'; ?>
        <div class="app-main">
            <?php include '../components/menu-superior.php'; ?>
            <main class="main-content">

                <section class="content-header">
                    <div>
                        <h1> Título da Página </h1>
                        <p> Descrição breve da funcionalidade </p>
                    </div>
                    
                </section>

                <section class="card">
                    <!-- CONSTRUA SUA TELA AQUI!!!! -->
                    <!-- conteúdo da página -->
                </section>                


            </main>
        </div>
    </div>
</body>
</html>

## Execução local

Execute o projeto em um servidor XAMPP/PHP e acesse as páginas em:

```
http://localhost/project_base/app/view/pages/
```
