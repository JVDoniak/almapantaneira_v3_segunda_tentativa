<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/conciliacao-doacao.js"
    ];

echo loadHeader("Conciliação de Doações - Alma Pantaneira", $scriptsPagina); ?>

<link rel="stylesheet" href="../css/conciliacao-contas.css">

        <section class="content-header">
          <div>
            <h1>Conciliação de Doações</h1>
            <p>Valide o recebimento das doações cadastradas</p>
          </div>
          <a href="listagem-doacao.php" class="btn-outline">
            <i class="bi bi-list-ul"></i> Lista de Doações
          </a>
        </section>

        <!-- Resumo (mesmo padrão de cards do dashboard) -->
        <section>
          <div class="grade-cards">
            <div class="card-item card-pendente">
              <div class="card-dados">
                <p>Aguardando Confirmação</p>
                <span id="resumoPendentesQtd">0</span>
              </div>
              <div class="card-icone icone-amarelo">
                <i class="bi bi-hourglass-split"></i>
              </div>
            </div>

            <div class="card-item card-doacoes">
              <div class="card-dados">
                <p>Valor Pendente</p>
                <span id="resumoPendentesValor">R$ 0,00</span>
              </div>
              <div class="card-icone icone-azul">
                <i class="bi bi-cash-stack"></i>
              </div>
            </div>

            <div class="card-item card-recebido">
              <div class="card-dados">
                <p>Confirmadas</p>
                <span id="resumoConfirmadasQtd">0</span>
              </div>
              <div class="card-icone icone-verde">
                <i class="bi bi-check2-circle"></i>
              </div>
            </div>

            <div class="card-item card-saldo">
              <div class="card-dados">
                <p>Total Confirmado</p>
                <span id="resumoConfirmadasValor">R$ 0,00</span>
              </div>
              <div class="card-icone icone-cinza">
                <i class="bi bi-wallet2"></i>
              </div>
            </div>
          </div>
        </section>

        <section class="filter-card">
          <form id="formFiltro" action="" method="GET">
            <div class="filter-grid">

              <div class="form-field-list">
                <label for="buscar">Buscar Doador</label>
                <input type="text" id="buscar" name="buscar" placeholder="Nome do doador">
              </div>

              <div class="form-field-list">
                <label for="data_inicio">Data de Início</label>
                <input type="date" id="data_inicio" name="data_inicio">
              </div>

              <div class="form-field-list">
                <label for="data_fim">Data de Fim</label>
                <input type="date" id="data_fim" name="data_fim">
              </div>

              <div class="form-field-list">
                <label for="projeto">Projeto</label>
                <select id="projeto" name="projeto">
                  <option value="">Todos</option>
                  <!-- Preenchido pelo JS (no back-end virá do banco, igual cadastro-doacao.php) -->
                </select>
              </div>

              <div class="form-field-list">
                <label for="status">Status</label>
                <select id="status" name="status">
                  <option value="">Todos</option>
                  <option value="pendente" selected>Pendente</option>
                  <option value="confirmada">Confirmada</option>
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
                  <th>Data</th>
                  <th>Doador</th>
                  <th>Projeto</th>
                  <th>Forma de recebimento</th>
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


<!-- Modal: detalhes e confirmação da doação (mesmo padrão do modal de Tipo de Doação) -->
<div id="modalConciliacao" class="modal oculta">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Confirmar Recebimento</h2>
            <button type="button" id="btnFecharModalConciliacao" class="modal-close" aria-label="Fechar">
                &times;
            </button>
        </div>

        <div class="conciliacao-detalhes">
            <div class="detalhe">
                <span>Doador</span>
                <strong id="detDoador"></strong>
            </div>
            <div class="detalhe">
                <span>E-mail</span>
                <strong id="detEmail"></strong>
            </div>
            <div class="detalhe">
                <span>Tipo de Doação</span>
                <strong id="detTipo"></strong>
            </div>
            <div class="detalhe">
                <span>Projeto</span>
                <strong id="detProjeto"></strong>
            </div>
            <div class="detalhe">
                <span>Data da Doação</span>
                <strong id="detData"></strong>
            </div>
            <div class="detalhe">
                <span>Forma de recebimento</span>
                <strong id="detForma"></strong>
            </div>
            <div class="detalhe full">
                <span>Descrição</span>
                <strong id="detDescricao"></strong>
            </div>
            <div class="detalhe full">
                <span>Comprovante</span>
                <strong id="detComprovante"></strong>
            </div>
        </div>

        <div class="conciliacao-valor">
            <span>Valor da Doação</span>
            <strong id="detValor"></strong>
        </div>

        <!-- Aviso quando não há comprovante anexado -->
        <div id="avisoSemComprovante" class="aviso-conciliacao aviso-alerta oculta">
            <i class="bi bi-exclamation-triangle-fill"></i>
            <p>Esta doação não possui comprovante anexado. Confira no extrato antes de confirmar.</p>
        </div>

        <!-- Pendente: opção de envio do recibo -->
        <div id="blocoEnvioRecibo">
            <label class="check-recibo">
                <input type="checkbox" id="enviarRecibo" checked>
                <span>Enviar recibo e agradecimento para o e-mail do doador</span>
            </label>
        </div>

        <!-- Já confirmada: informações da confirmação -->
        <div id="infoConfirmada" class="aviso-conciliacao aviso-sucesso oculta">
            <i class="bi bi-check-circle-fill"></i>
            <p id="textoConfirmada"></p>
        </div>

        <div class="form-actions">
            <button type="button" id="btnCancelarConciliacao" class="btn-secondary">Cancelar</button>
            <button type="button" id="btnReenviarRecibo" class="btn-outline oculta">
                <i class="bi bi-envelope"></i> Reenviar Recibo
            </button>
            <button type="button" id="btnConfirmarRecebimento" class="btn-primary">
                <i class="bi bi-check-lg"></i> Confirmar Recebimento
            </button>
        </div>
    </div>
</div>

<!-- Mensagem rápida de sucesso -->
<div id="toastConciliacao" class="toast-conciliacao" role="status" aria-live="polite"></div>

<?= loadFooter() ?>