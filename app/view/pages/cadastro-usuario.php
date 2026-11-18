<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/cadastro-usuario.js",
    "../js/mascara-validacao.js"
    ];
    
echo loadHeader("Cadastro Usuário - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
    <div>
        <h1>Cadastro de Usuário</h1>
        <p> Gerenciar e Cadastrar Usuário </p>
    </div>
    <a href="listagem-usuario.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
    <div class="form-title">
        <h2>Cadastrar Usuário</h2>
    </div>
  
    <form id="formCadastroUsuario">
      <div class="form-grid-3">
          
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
              <label for="tipo">Cargo</label>
              <select id="tipo" name="tipo">
                 <option selected value="">Selecione o Cargo</option>
                <option value="Presidente">Presidente</option>
                <option value="Administrador">Administrador</option>
                <option value="Financeiro">Financeiro</option>
                <option value="Tesoureiro">Tesoureiro</option>
              </select>
          </div>

            <div class="form-field">
              <label for="status_usuario">Status</label>
              <select id="status_usuario" name="status_usuario">
                <option selected value="">Selecione o status</option>
                <option  value="1">Ativo</option>
                <option  value="0">Inativo</option>
              </select>
          </div>
          
      </div>

      <div class="form-actions">
        <button type="reset" class="btn-secondary">Cancelar</button>
        <button name="REQUEST_METHOD"  type="submit" class="btn-primary">Salvar</button>
      </div>
    </form>
</section>

<?= loadFooter() ?>