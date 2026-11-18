<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/relatorio-projeto.js"
    ];

echo loadHeader("Doações por Projeto - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Doações por Projeto</h1>
            <p>Acompanhe o total arrecadado por projetos.</p>
          </div>

          <a href="cadastro-projeto.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Projeto</a>
        </section>

        <section class="filter-card">
          <form action="" method="GET">
            <div class="filter-grid">
              <div class="form-field-list">
                <label for="nome_projeto">Buscar Projeto</label>
                <input type="text" id="nome_projeto" name="nome_projeto" placeholder="Nome do projeto">
              </div>

              <div class="form-field-list">
                <label for="data_inicio">Data de início</label>
                <input type="date" id="data_inicio" name="data_inicio">
              </div>

              <div class="form-field-list">
                <label for="tipo_projeto">Tipo do Projeto</label>
                <select id="tipo_projeto" name="tipo_projeto">
                  <option value="selecione" disabled selected hidden>Selecione o tipo</option>
                  <option value="saude">Saúde</option>
                  <option value="assistencia">Assistência Social</option>
                  <option value="educacao">Educação</option>
                  <option value="pesquisa">Pesquisa Médica</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="status">Situação Cadastral</label>
                <select id="status" name="status">
                  <option value="selecione" disabled selected hidden>Selecione a situação cadastral</option>
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
                  <th>Nome do Projeto</th>
                  <th>Tipo do Projeto</th>
                  <th>Data de Início</th>
                  <th>Andamento do Projeto</th>
                  <th>Responsável</th>
                  <th>Local do Projeto</th>
                  <th>Total Arrecadado</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
              </tbody>
            </table>
          </div>
        </section>

<?= loadFooter() ?>