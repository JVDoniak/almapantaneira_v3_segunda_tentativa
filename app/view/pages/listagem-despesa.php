<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/listagem-despesa.js?v=" . filemtime(__DIR__ . "/../JS/listagem-despesa.js")
    ];

echo loadHeader("Lista de Despesas - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Lista de Despesas</h1>
            <p>Gerenciar a Lista de Despesas</p>
          </div>

          <a href="cadastro-despesa.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Nova Despesa</a>
        </section>

        <section class="filter-card">
          <form id="formFiltroDespesa" action="" method="GET">
            <div class="filter-grid">
              <div class="form-field-list">
                <label for="id_tipo_despesa">Tipo</label>
                <select id="id_tipo_despesa" name="id_tipo_despesa">
                  <option value="">Todos os tipos</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="nome">Nome da despesa</label>
                <input type="text" id="nome" name="nome" placeholder="Digite o nome da despesa">
              </div>

              <div class="form-field-list">
                <label for="forma_pagamento">Forma de pagamento</label>
                <select id="forma_pagamento" name="forma_pagamento">
                  <option value="">Todas as formas</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="id_categoria_despesa">Categoria</label>
                <select id="id_categoria_despesa" name="id_categoria_despesa">
                  <option value="">Todas as categorias</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="id_projeto">Projeto</label>
                <select id="id_projeto" name="id_projeto">
                  <option value="">Todos os projetos</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="data_inicio">Data de vencimento — início</label>
                <input type="date" id="data_inicio" name="data_inicio">
              </div>

              <div class="form-field-list">
                <label for="data_fim">Data de vencimento — fim</label>
                <input type="date" id="data_fim" name="data_fim">
              </div>

              <div class="filter-actions">
                <button type="submit" class="btn-primary">
                  <i class="bi bi-funnel-fill"></i> Filtrar
                </button>
                <button type="button" id="limparFiltros" class="btn-secondary">
                  <i class="bi bi-x-circle"></i> Limpar
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
                  <th>Nome</th>
                  <th>Tipo</th>
                  <th>Categoria</th>
                  <th>Projeto</th>
                  <th>Forma de Pagamento</th>
                  <th>Vencimento</th>
                  <th>Valor</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
              </tbody>
            </table>
          </div>
        </section>

<?= loadFooter() ?>
