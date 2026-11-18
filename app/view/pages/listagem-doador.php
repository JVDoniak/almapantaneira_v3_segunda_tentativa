<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/listagem-doador.js"
    ];
    
echo loadHeader("Lista de Doadores - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Lista de Doadores</h1>
            <p>Gerenciar a Lista de Doadores</p>
          </div>

          <a href="cadastro-doador.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Doador</a>
        </section>

        <section class="filter-card">
          <form action="" method="GET"> 
            <div class="filter-grid">
              <div class="form-field-list">
                <label for="nome">Buscar Doador</label>
                <input type="text" id="nome" name="nome" placeholder="Nome Doador">
              </div>

              <div class="form-field-list">
                <label for="cpf_cnpj">Buscar CPF/CNPJ</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="CPF ou CNPJ" maxlength="14" oninput="mascaraCPF(this)">
              </div>

              <div class="form-field-list">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo">
                  <option value="selecione" disabled selected hidden>Selecione o tipo</option>
                  <option value="Pessoa Física">Pessoa Física</option>
                  <option value="Pessoa Jurídica">Pessoa Jurídica</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="status_doador">Situação Cadastral</label>
                <select id="status_doador" name="status_doador">
                  <option value="selecione" selected hidden>Selecione a situação cadastral</option>
                  <option value="1">Ativo</option>
                  <option value="0">Inativo</option>
                </select>
              </div>

              <div class="filter-actions">
                <button type="submit" class="btn-primary">
                  <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
              </div>
            </div>
          </form>
        </section>

        <section class="table-card">
          <div class="table-responsive">
            <table class="data-table">
              <thead>
                <tr>
                  <th>Tipo</th>
                  <th>Nome</th>
                  <th>CPF/CNPJ</th>
                  <th>Telefone</th>
                  <th>E-mail</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
              </tbody>
            </table>
          </div>
        </section>

<?= loadFooter() ?>
