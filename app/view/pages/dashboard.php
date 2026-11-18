<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js",
    "../js/tela-dashboard.js"
    ];

echo loadHeader("Dashboard - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Dashboard</h1>
            <p>Visão geral das receitas, despesas e doações</p>
          </div>
          <span class="periodo-exibido" id="periodoExibido"></span>
        </section>

        <!-- Filtros: valem para todos os cards, gráficos e a tabela abaixo -->
        <section class="filter-card">
          <form id="formFiltroDashboard" action="" method="GET">
            <div class="filter-grid">

              <div class="form-field-list">
                <label for="periodo">Período</label>
                <select id="periodo" name="periodo">
                  <option value="mes">Este mês</option>
                  <option value="3_meses">Últimos 3 meses</option>
                  <option value="6_meses" selected>Últimos 6 meses</option>
                  <option value="ano">Últimos 12 meses</option>
                  <option value="personalizado">Personalizado</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="data_inicio">Data de Início</label>
                <input type="date" id="data_inicio" name="data_inicio" disabled>
              </div>

              <div class="form-field-list">
                <label for="data_fim">Data de Fim</label>
                <input type="date" id="data_fim" name="data_fim" disabled>
              </div>

              <div class="form-field-list">
                <label for="projeto">Projeto</label>
                <select id="projeto" name="projeto">
                  <option value="">Todos os projetos</option>
                  <!-- No back-end virá do banco, igual cadastro-doacao.php -->
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

        <!-- Cards de resumo do período filtrado -->
        <section>
          <div class="grade-cards">
            <div class="card-item card-recebido">
              <div class="card-dados">
                <p>Receitas Confirmadas</p>
                <span id="cardReceitas">R$ 0,00</span>
                <small id="varReceitas" class="card-variacao"></small>
              </div>
              <div class="card-icone icone-verde">
                <i class="bi bi-graph-up-arrow"></i>
              </div>
            </div>

            <div class="card-item card-despesas">
              <div class="card-dados">
                <p>Despesas</p>
                <span id="cardDespesas">R$ 0,00</span>
                <small id="varDespesas" class="card-variacao"></small>
              </div>
              <div class="card-icone icone-vermelho">
                <i class="bi bi-graph-down-arrow"></i>
              </div>
            </div>

            <div class="card-item card-doacoes">
              <div class="card-dados">
                <p>Saldo do Período</p>
                <span id="cardSaldo">R$ 0,00</span>
                <small class="card-variacao">Receitas menos despesas</small>
              </div>
              <div class="card-icone icone-azul">
                <i class="bi bi-wallet2"></i>
              </div>
            </div>

            <div class="card-item card-pendente">
              <div class="card-dados">
                <p>Doações a Conciliar</p>
                <span id="cardPendentes">0</span>
                <a href="conciliacao-contas.php" class="card-link" id="cardPendentesValor">R$ 0,00 · Conciliar</a>
              </div>
              <div class="card-icone icone-amarelo">
                <i class="bi bi-hourglass-split"></i>
              </div>
            </div>
          </div>
        </section>

        <!-- Receitas x Despesas ao longo do tempo -->
        <section class="painel-grafico">
          <div class="painel-grafico-header">
            <h2>Receitas x Despesas por Mês</h2>
          </div>
          <div class="grafico-area grafico-area-alta">
            <canvas id="graficoMensal" aria-label="Gráfico de receitas e despesas por mês" role="img"></canvas>
          </div>
        </section>

        <!-- Composição: despesas por categoria e receitas por projeto -->
        <section class="container-graficos">
          <div class="painel-grafico">
            <div class="painel-grafico-header">
              <h2>Despesas por Categoria</h2>
              <span id="totalCategorias" class="painel-total"></span>
            </div>
            <div class="grafico-area" id="areaCategorias">
              <canvas id="graficoCategorias" aria-label="Gráfico de despesas por categoria" role="img"></canvas>
            </div>
          </div>

          <div class="painel-grafico">
            <div class="painel-grafico-header">
              <h2>Receitas por Projeto</h2>
              <span id="totalProjetos" class="painel-total"></span>
            </div>
            <div class="grafico-area" id="areaProjetos">
              <canvas id="graficoProjetos" aria-label="Gráfico de receitas por projeto" role="img"></canvas>
            </div>
          </div>
        </section>
<?= loadFooter() ?>
