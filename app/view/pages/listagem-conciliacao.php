<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    // Colocar JS da Pagina aqui exemplo: "../js/cadastro-usuario.js"
    ];
    
echo loadHeader("Listagem Conciliação - Alma Pantaneira"); ?>

        <section class="content-header">
          <div>
            <h1>Conciliação</h1>
            <p>Gerenciar a conciliação de notas de entrada e saída</p>
          </div>
          <div>
            <a href="cadastro-conciliacao.php" class="btn-primary">
              <i class="bi bi-plus-lg"></i> Nova Conciliação
            </a>
          </div>
        </section>

        <section class="filter-card">
          <form action="" method="GET">
            <div class="filter-grid">
              <div class="form-field-list">
                <label for="buscar">Buscar</label>
                <input type="text" id="buscar" name="buscar" placeholder="Lançamento">
              </div>

              <div class="form-field-list">
                <label for="data_inicio">Data de Início</label>
                <input type="date" name="data_inicio" id="data_inicio">
              </div>

              <div class="form-field-list">
                <label for="data_fim">Data de Fim</label>
                <input type="date" name="data_fim" id="data_fim">
              </div>

              <div class="form-field-list">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value=""disabled selected hidden>Selecione uma opção</option>
                  <option value="C">Conciliado</option>
                  <option value="N">Não Conciliado</option>
                </select>
              </div>

              <div class="filter-actions">
                <button type="submit" class="btn-primary">
                  <i class="bi bi-search"></i> Filtrar
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
                  <th><input type="checkbox" class="custom-checkbox"></th>
                  <th>Lançamento</th>
                  <th>Nº Doc.</th>
                  <th>Data</th>
                  <th>Valor</th>
                  <th>Conciliado</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td><input type="checkbox" class="custom-checkbox"></td>
                  <td>Compra de Mercado</td>
                  <td>7548815</td>
                  <td>01/05/2026</td>
                  <td>R$ 700,45</td>
                  <td>Não</td>
                  <td>
                    <div class="table-actions-cell">
                      <i class="bi bi-eye" title="Visualizar"></i>
                      <a href="edicao-conciliacao"><i class="bi bi-pencil-square" title="Editar"></i></a>
                      <label class="switch-component">
                        <input type="checkbox">
                        <span class="switch-slider"></span>
                      </label>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </section>

<?= loadFooter() ?>
