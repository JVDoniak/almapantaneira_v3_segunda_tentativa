<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/editar-usuario.js",
    ];

echo loadHeader("Edição Usuário - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
    <div>
        <h1>Edição de Usuário</h1>
        <p>Editar os usuários do sistema</p>
    </div>
    <a href="listagem-usuario.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
    <div class="form-title">
        <h2>Editar Usuário</h2>
    </div>

    <form id="formEdicaoUsuario" action="#" method="post">
      <div class="form-grid-3">
          <input type="hidden" name="id_usuario" id="id_usuario">

          <div class="form-field">
            <label for="nome">Nome completo</label>
            <input type="text" id="nome" name="nome" placeholder="Nome completo">
          </div>

          <div class="form-field">
            <label for="cpf">CPF</label>
            <input type="text" id="cpf" name="cpf" placeholder="999.999.999-99">
          </div>

          <div class="form-field">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone" placeholder="(67) 99999-9999">
          </div>

          <div class="form-field">
            <label for="email">E-mail</label>
            <input type="email" id="email" name="email" placeholder="Digite o e-mail">
          </div>

          <div class="form-field">
            <label for="cargo">Cargo</label>
            <select id="cargo" name="cargo">
              <option>Presidente</option>
              <option>Administrador</option>
              <option>Financeiro</option>
              <option>Usuário</option>
            </select>
          </div>

      </div>

      <div class="form-actions">
        <button type="reset" class="btn-secondary">Cancelar</button>
        <button type="submit" id="salvar" name="salvar" class="btn-primary">Salvar</button>
      </div>
    </form>
</section>

<?= loadFooter() ?>