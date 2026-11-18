<?php

require_once "../components/header.php";
require_once "../../utils/loadFooter.php";


$scriptsPagina = [
    "../js/cadastro-categoria.js"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $categoria = new Categoria();

    $categoria->nome = trim($_POST['nome'] ?? '');

    if ($categoria->nome === '') {

        echo "<script>
                alert('Informe o nome da categoria.');
              </script>";

    } else {

        $res = $categoria->cadastrar();

        if ($res) {

            echo "<script>
                    alert('Categoria cadastrada com sucesso!');
                    window.location.href = 'listagem-categoria.php';
                  </script>";
            exit;

        } else {

            echo "<script>
                    alert('Erro ao cadastrar categoria!');
                  </script>";
        }
    }
}

echo loadHeader("Cadastro Categoria - Alma Pantaneira", $scriptsPagina);
?>

<section class="content-header">

    <div class="main">
        <h1>Categoria de Despesa</h1>
        <p>Gerenciar Cadastro de Categoria</p>
    </div>

    <a href="listagem-categoria.php" class="btn-outline">
        <i class="bi bi-arrow-left"></i>
        Voltar para Lista
    </a>

</section>

<section class="card">

    <div class="form-title">
        <h2>Cadastrar Categoria</h2>
    </div>

   <form
    id="formCadastroCategoria"
    method="POST"
    action="../../actions/cadastro-categoria.php"
>

    <div class="form-field">

        <label for="descricao">Descrição</label>

        <input
            type="text"
            id="descricao"
            name="descricao"
            placeholder="Descrição da Categoria"
            maxlength="100"
            required
        >


    </div>


        <div class="form-actions">

            <button
                type="reset"
                class="btn-secondary"
            >
                Cancelar
            </button>

            <button
                type="submit"
                class="btn-primary"
            >
                Salvar
            </button>

        </div>

    </form>

</section>

<?= loadFooter() ?>