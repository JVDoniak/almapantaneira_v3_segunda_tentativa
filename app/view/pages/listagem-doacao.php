<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/listagem-doacao.js?v=" . filemtime(__DIR__ . "/../JS/listagem-doacao.js")
    ];
    
echo loadHeader("Lista de Doações - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Lista de Doações</h1>
            <p>Gerenciar a Lista de Doações</p>
          </div>

          <a href="cadastro-doacao.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Nova Doação
          </a>
        </section>

        <section class="filter-card">
          <form action="" method="GET">
            <div class="filter-grid">
              
              <div class="form-field-list">
                <label for="buscar">Buscar</label>
                <input type="text" id="buscar" name="buscar" placeholder="Doador ou descrição">
              </div>

              <div class="form-field-list">
                <label for="periodo">Período</label>
                <select id="periodo" name="periodo">
                  <option value="selecione" disabled selected hidden>Selecione o período</option>
                  <option value="30_dias">Últimos 30 dias</option>
                  <option value="6_meses">Últimos 6 meses</option>
                  <option value="este_ano">Este ano</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="tipo">Tipo de Doação</label>
                <select id="tipo" name="tipo">
                  <option value="selecione" disabled selected hidden>Selecione o tipo</option>
                  <option value="monetaria">Monetária (R$)</option>
                  <option value="insumos">Insumos / Alimentos</option>
                  <option value="medicamentos">Medicamentos</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="projeto">Projeto Destinado</label>
                <select id="projeto" name="projeto">
                  <option value="">Todos</option>
                  <option value="expansao_medica">Expansão Médica</option>
                  <option value="saude_comunidade">Saúde Comunitária</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="">Todos</option>
                  <option value="confirmado">Confirmado</option>
                  <option value="pendente">Pendente</option>
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
                  <th>Doador</th>
                  <th>Tipo</th>
                  <th>Data do Recebimento</th>
                  <th>Projeto Destinado</th>
                  <th>Valor (R$)</th>
                  <!-- <th>Status</th> -->
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
              </tbody>
            </table>
          </div>
        </section>

<?= loadFooter() ?>
