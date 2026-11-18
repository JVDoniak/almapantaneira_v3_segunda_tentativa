<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/listagem-fornecedor.js"
    ];
    
echo loadHeader("Lista de Fornecedores - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Lista de Fornecedores</h1>
            <p>Consulte e gerencie os fornecedores cadastrados.</p>
          </div>

          <a href="cadastro-fornecedor.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Fornecedor</a>
        </section>

        <section class="filter-card">
          <form id="listar-fornecedores" action="" method="GET">
            <div class="filter-grid">
              <div class="form-field-list">
                <label for="nome_fornecedor">Buscar Fornecedores</label>
                <input type="text" id="nome_fornecedor" name="nome_fornecedor" placeholder="Nome do fornecedor">
              </div>

              <div class="form-field-list">
                <label for="cpf_cnpj">CNPJ</label>
                <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="CNPJ Fornecedor">
              </div>

              <div class="form-field-list">
                <label for="status">Situação Cadastral</label>
                <select id="status" name="status">
                  <option value="selecione" disabled selected hidden>Selecione a situação cadastral</option>
                  <option value="ativo">Ativo</option>
                  <option value="inativo">Inativo</option>
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
                  <th>Nome Fantasia</th>
                  <th>Razão Social</th>
                  <th>CNPJ</th>
                  <th>Contato</th>
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
