<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/conciliacao-despesa.js"
    ];

echo loadHeader("Conciliação de Despesas - Alma Pantaneira", $scriptsPagina); ?>

<link rel="stylesheet" href="../css/conciliacao-contas.css">

        <section class="content-header">
          <div>
            <h1>Conciliação de Despesas</h1>
            <p>Confirme o pagamento das despesas cadastradas</p>
          </div>
          <a href="listagem-despesa.php" class="btn-outline">
            <i class="bi bi-list-ul"></i> Lista de Despesas
          </a>
        </section>

        <!-- Resumo (mesmo padrão de cards do dashboard) -->
        <section>
          <div class="grade-cards">
            <div class="card-item card-pendente">
              <div class="card-dados">
                <p>A Pagar</p>
                <span id="resumoPendentesQtd">0</span>
                <small id="resumoPendentesValor" class="card-variacao">R$ 0,00</small>
              </div>
              <div class="card-icone icone-amarelo">
                <i class="bi bi-hourglass-split"></i>
              </div>
            </div>

            <div class="card-item card-despesas">
              <div class="card-dados">
                <p>Vencidas</p>
                <span id="resumoVencidasQtd">0</span>
                <small id="resumoVencidasValor" class="card-variacao">R$ 0,00</small>
              </div>
              <div class="card-icone icone-vermelho">
                <i class="bi bi-exclamation-triangle"></i>
              </div>
            </div>

            <div class="card-item card-doacoes">
              <div class="card-dados">
                <p>Vencem em 7 dias</p>
                <span id="resumoProximasQtd">0</span>
                <small id="resumoProximasValor" class="card-variacao">R$ 0,00</small>
              </div>
              <div class="card-icone icone-azul">
                <i class="bi bi-calendar-event"></i>
              </div>
            </div>

            <div class="card-item card-recebido">
              <div class="card-dados">
                <p>Pagas</p>
                <span id="resumoPagasQtd">0</span>
                <small id="resumoPagasValor" class="card-variacao">R$ 0,00</small>
              </div>
              <div class="card-icone icone-verde">
                <i class="bi bi-check2-circle"></i>
              </div>
            </div>
          </div>
        </section>

        <section class="filter-card">
          <form id="formFiltroConciliacaoDespesa" action="" method="GET">
            <div class="filter-grid">

              <div class="form-field-list">
                <label for="buscar">Buscar</label>
                <input type="text" id="buscar" name="buscar" placeholder="Despesa ou fornecedor">
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
                <label for="data_inicio">Vencimento — início</label>
                <input type="date" id="data_inicio" name="data_inicio">
              </div>

              <div class="form-field-list">
                <label for="data_fim">Vencimento — fim</label>
                <input type="date" id="data_fim" name="data_fim">
              </div>

              <div class="form-field-list">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="">Todos</option>
                  <option value="a_pagar" selected>A pagar (pendentes e vencidas)</option>
                  <option value="vencida">Somente vencidas</option>
                  <option value="paga">Pagas</option>
                </select>
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
                  <th>Vencimento</th>
                  <th>Despesa</th>
                  <th>Tipo</th>
                  <th>Categoria</th>
                  <th>Projeto</th>
                  <th>Forma de Pagamento</th>
                  <th>Valor</th>
                  <th>Comprovante</th>
                  <th>Status</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
              </tbody>
            </table>
          </div>
        </section>


<!-- Modal: detalhes e confirmação do pagamento (mesmo padrão da conciliação de doações) -->
<div id="modalConciliacao" class="modal oculta">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="tituloModal">Confirmar Pagamento</h2>
            <button type="button" id="btnFecharModalConciliacao" class="modal-close" aria-label="Fechar">
                &times;
            </button>
        </div>

        <div class="conciliacao-detalhes">
            <div class="detalhe">
                <span>Despesa</span>
                <strong id="detNome"></strong>
            </div>
            <div class="detalhe">
                <span>Fornecedor</span>
                <strong id="detFornecedor"></strong>
            </div>
            <div class="detalhe">
                <span>Tipo</span>
                <strong id="detTipo"></strong>
            </div>
            <div class="detalhe">
                <span>Categoria</span>
                <strong id="detCategoria"></strong>
            </div>
            <div class="detalhe">
                <span>Projeto</span>
                <strong id="detProjeto"></strong>
            </div>
            <div class="detalhe">
                <span>Vencimento</span>
                <strong id="detVencimento"></strong>
            </div>
            <div class="detalhe">
                <span>Forma de Pagamento</span>
                <strong id="detForma"></strong>
            </div>
            <div class="detalhe full">
                <span>Descrição</span>
                <strong id="detDescricao"></strong>
            </div>
            <div class="detalhe full">
                <span>Nota Fiscal / Documento</span>
                <strong id="detComprovante"></strong>
            </div>
            <div class="detalhe full oculta" id="blocoComprovantePagamento">
                <span>Comprovante de Pagamento</span>
                <strong id="detComprovantePagamento"></strong>
            </div>
        </div>

        <div class="conciliacao-valor">
            <span>Valor da Despesa</span>
            <strong id="detValor"></strong>
        </div>

        <!-- Despesa vencida -->
        <div id="avisoVencida" class="aviso-conciliacao aviso-erro oculta">
            <i class="bi bi-exclamation-octagon-fill"></i>
            <p id="textoVencida"></p>
        </div>

        <!-- Pendente: dados do pagamento -->
        <form id="formPagamento" class="pagamento-campos" novalidate>
            <div class="form-field">
                <label for="data_pagamento" class="campo-obrigatorio">Data do Pagamento</label>
                <input type="date" id="data_pagamento" name="data_pagamento" required>
            </div>

            <div class="form-field">
                <label for="valor_pago" class="campo-obrigatorio">Valor Pago (R$)</label>
                <input type="number" id="valor_pago" name="valor_pago" step="0.01" min="0.01" required>
            </div>

            <!-- Comprovante do pagamento (o arquivo do cadastro costuma ser a NF ou o boleto) -->
            <div class="form-field full" id="blocoAnexo">
                <label for="comprovante_pagamento" id="labelAnexo">Comprovante de Pagamento</label>
                <input type="file" id="comprovante_pagamento" name="comprovante_pagamento" accept="image/*,.pdf">
            </div>
        </form>

        <!-- Valor pago diferente (juros, multa ou desconto) -->
        <div id="avisoDiferenca" class="aviso-conciliacao aviso-alerta oculta">
            <i class="bi bi-info-circle-fill"></i>
            <p id="textoDiferenca"></p>
        </div>

        <!-- Sem comprovante -->
        <div id="avisoSemComprovante" class="aviso-conciliacao aviso-alerta oculta">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <p>Esta despesa não possui comprovante. Anexe acima ou confira no extrato antes de confirmar.</p>
        </div>

        <!-- Erro de validação -->
        <div id="avisoErroForm" class="aviso-conciliacao aviso-erro oculta" role="alert">
            <i class="bi bi-x-circle-fill"></i>
            <p id="textoErroForm"></p>
        </div>

        <!-- Já paga: informações da conciliação -->
        <div id="infoPaga" class="aviso-conciliacao aviso-sucesso oculta">
            <i class="bi bi-check-circle-fill"></i>
            <p id="textoPaga"></p>
        </div>

        <div class="form-actions">
            <button type="button" id="btnCancelarConciliacao" class="btn-secondary">Cancelar</button>
            <button type="button" id="btnConfirmarPagamento" class="btn-primary">
                <i class="bi bi-check-lg"></i> Confirmar Pagamento
            </button>
        </div>
    </div>
</div>

<!-- Mensagem rápida de sucesso -->
<div id="toastConciliacao" class="toast-conciliacao" role="status" aria-live="polite"></div>

<?= loadFooter() ?>