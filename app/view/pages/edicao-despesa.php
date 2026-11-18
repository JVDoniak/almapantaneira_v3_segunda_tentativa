<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;

$scriptsPagina = [
    "../js/editar-despesa.js",
    "../js/modal-categoria-despesa.js"
];

require_once "../../controller/TipoDespesa.php";
$listarTipos = (new TipoDespesa())->listarTodas();
require_once "../../controller/CategoriaDespesa.php";
$listarCategoria = (new CategoriaDespesa())->listarTodas();

echo loadHeader("Edição de Despesa - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
  <div>
    <h1> Editar Despesa </h1>
    <p> Gerenciar e Editar Despesa </p>
  </div>
  <a href="listagem-despesa.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
  <div class="form-title">
    <h2>Editar Despesa</h2>
  </div>

  <form id="formEdicaoDespesa">
    <input type="hidden" id="id_despesa" name="id_despesa">

    <div class="form-section">
      <div class="form-grid-3">

        <div class="form-field">
          <label for="nome">Nome da Despesa</label>
          <input type="text" id="nome" name="nome" placeholder="Nome da despesa">
        </div>

        <!-- Fornecedor (autocompletar) -->
        <div class="form-field" style="position: relative;">
          <label for="fornecedor">Fornecedor (opcional)</label>
          <input type="text" id="fornecedor" autocomplete="off" placeholder="Digite o nome do fornecedor">
          <input type="hidden" id="id_fornecedor" name="id_fornecedor">
          <div id="sugestoes_fornecedores"></div>
        </div>

        <!-- Projeto (autocompletar) -->
        <div class="form-field" style="position: relative;">
          <label for="projeto">Projeto (opcional)</label>
          <input type="text" id="projeto" autocomplete="off" placeholder="Digite o nome do projeto">
          <input type="hidden" id="id_projeto" name="id_projeto">
          <div id="sugestoes_projetos"></div>
        </div>

        <div class="form-field">
          <label for="valor">Valor</label>
          <input type="number" id="valor" name="valor" step="0.01" min="0" placeholder="0,00">
        </div>

        <div class="form-field">
          <label for="data_vencimento">Data de Vencimento</label>
          <input type="date" id="data_vencimento" name="data_vencimento">
        </div>

        <div class="form-field">
          <label for="forma_pagamento">Forma de Pagamento</label>
          <select id="forma_pagamento" name="forma_pagamento">
            <option value="" disabled hidden>Selecione a forma de pagamento</option>
            <option value="Pix">PIX</option>
            <option value="Cartao">Cartão</option>
            <option value="Boleto">Boleto</option>
          </select>
        </div>

        <div class="form-field">
          <label for="id_tipo_despesa">Tipo de Despesa</label>
          <select id="id_tipo_despesa" name="id_tipo_despesa">
            <option value="">Não classificado</option>
            <?php foreach ($listarTipos as $tipo): ?>
              <option value="<?= (int) $tipo['id_tipo_despesa'] ?>"><?= htmlspecialchars($tipo['descricao'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
          </select>
        </div>

         <div class="form-field">

            <div class="label-with-button">
                    <label for="id_categoria_despesa">Categoria da Despesa</label>

                    <button
                        type="button"
                        id="btnAdicionarCategoria"
                        class="btn-add"
                        title="Adicionar Categoria"
                        aria-label="Adicionar Categoria">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>


             <select id="id_categoria_despesa" name="id_categoria_despesa">
                <option value="">Não classificada</option>
                <?php foreach ($listarCategoria as $categoria): ?>
                    <option value="<?= (int) $categoria['id_categoria_despesa'] ?>">
                        <?= htmlspecialchars($categoria['descricao'], ENT_QUOTES, 'UTF-8') ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>



      </div>
    </div>

    <div class="form-section">
      <div class="form-flex">
        <div class="form-field full">
          <label for="descricao">Descrição</label>
          <textarea id="descricao" name="descricao" rows="5" placeholder="Descrição da despesa"></textarea>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="listagem-despesa.php" class="btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Cancelar</a>
      <button type="submit" id="salvarDespesa" class="btn-primary" disabled>Salvar</button>
    </div>
  </form>
</section>

<div id="modalCategoriaDespesa" class="modal oculta">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Nova categoria de despesa</h2>
            <button type="button" id="btnFecharModalCategoria" class="modal-close">
                &times;
            </button>
        </div>

        <form id="formCadastroCategoria">
            <div class="form-field">
                <label for="descricaoCategoria">Descrição</label>

                <div class="input-with-button">
                    <input type="text" id="descricaoCategoria" name="descricao" placeholder="Descrição" maxlength="100" required>

                    <button type="submit" class="btn-primary" title="Cadastrar Categoria">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <button
                    type="button"
                    id="btnCancelarCategoria"
                    class="btn-secondary"
                >
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>


<?= loadFooter() ?>
