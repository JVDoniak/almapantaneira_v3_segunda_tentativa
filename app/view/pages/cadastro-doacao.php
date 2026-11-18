<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

require_once "../../controller/Projeto.php";
require_once "../../controller/TipoDoacao.php";

$objProjeto = new Projeto();
$listaProjetos = $objProjeto->listarTodos();

$objTipoDoacao = new TipoDoacao();
$listarTipo = $objTipoDoacao->listarTodas();


$scriptsPagina = [
    "../js/cadastro-doacao.js?v=" . filemtime(__DIR__ . "/../JS/cadastro-doacao.js"),
    "../js/upload-comprovante.js",
    "../js/camera-documento.js",
    "../js/modal-tipo-doacao.js"
    ];

echo loadHeader("Cadastro de Doações - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
    <div>
        <h1>Doação</h1>
        <p> Gerenciar e Cadastrar Doações </p>
    </div>
    <a href="listagem-doacao.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">

  <div class="form-title">
      <h2>Cadastrar Doação</h2>
  </div>

  <form id="formCadastro">
    <div class="form-grid-3" style="margin-bottom: 24px;">

            <!-- Fornecedor (autocompletar) -->
        <div class="form-field" style="position: relative;">
            <div class="label-with-button">
                    <label for="nome_doador" class="campo-obrigatorio">Doador</label>
                    <a href="./cadastro-doador.php"> 
                        <button type="button" class="btn-add">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                    </a>  
            </div>
            <input type="text" id="nome_doador" placeholder="Nome do doador" autocomplete="off" required>
            <input type="hidden" id="id_doador" name="id_doador" value="">
            <div id="sugestoes_doadores"></div>
        </div>


        <div class="form-field">

            <div class="label-with-button">
                    <label for="categoria">Tipo de Doação</label>

                    <button
                        type="button"
                        id="btnAdicionarTipo"
                        class="btn-add"
                        title="Adicionar Tipo Categoria"
                        aria-label="Adicionar Tipo Categoria">
                        <i class="bi bi-plus-lg"></i>
                    </button>
                </div>


             <select id="tipo" name="tipo" required>
                <option value="" disabled selected hidden>Selecione o tipo</option>
                <?php foreach ($listarTipo as $tipo): ?>
                    <option value="<?php echo $tipo['id_tipo_doacao']; ?>">
                        <?php echo $tipo['descricao']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

         <div class="form-field">
            <label for="projeto">Projeto (opcional)</label>
            <select id="projeto" name="id_projeto">
                <option value="" selected>Sem projeto</option>
                <?php foreach ($listaProjetos as $projeto): ?>
                    <option value="<?php echo $projeto['id_projeto']; ?>">
                        <?php echo $projeto['nome']; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-field">
            <label for="valor" class="campo-obrigatorio">Valor da Doação</label>
            <small>Para doações a partir de R$ 1.000,00, o endereço do doador deve estar completo.</small>
            <input type="number" step="0.01" id="valor" name="valor" placeholder="R$ 2.500,00">
        </div>

        <div class="form-field">
            <label for="forma_pagamento" class="campo-obrigatorio">Forma de recebimento</label>
            <select id="forma_pagamento" name="forma_pagamento" required>
                <option value=""disabled selected hidden>Selecione a forma de recebimento</option>
                <option>Transferência Bancária</option>
                <option>PIX</option>
                <option>Dinheiro</option>
                <option>Cartão</option>
            </select>
        </div>

        <div class="form-field">
            <label for="data_doacao" class="campo-obrigatorio">Data da Doação</label>
            <input type="date" id="data_doacao" name="data_doacao" required>
        </div>

        <div class="form-field full">
            <label for="descricao">Descrição da Doação</label>
            <input type="text" id="descricao" name="descricao" placeholder="Exemplo: Contribuição para tratamento veterinário">
        </div>

    </div>
    <div class="upload-box" id="uploadBox">
        <input type="file" id="doc" name="doc" accept="image/*, application/pdf, .zip" style="display:none;">
            <div id="uploadInicial">
                    <div class="upload-icon">
                        <i class="bi bi-upload"></i>
                    </div>
                    <p>Anexar Nota Fiscal ou Comprovante</p>
                    <span>PDF, Imagem ou ZIP (máx. 5MB cada)</span>
                </div>
                <div class="preview-arquivo" id="previewArquivo" style="display: none;">
                    <div id="previewConteudo" class="preview-conteudo"></div>
                    <div class="arquivo-info">
                        <strong id="nomeArquivo"></strong>
                        <span id="tamanhoArquivo"></span>
                    </div>
                    <button type="button" id="removerArquivo" class="btn-remover-arquivo">
                        <i class="bi bi-trash"></i>
                        Remover
                    </button>
                </div>
            </div>
    <!-- NF-02: captura de comprovante com a câmera do celular -->
    <div class="form-field" style="margin-top: 12px;">
        <input type="file" id="fotoDocumento" accept="image/*" capture="environment" hidden>
        <button type="button" id="btnTirarFoto" class="btn-secondary"> Tirar foto do comprovante</button>
        <p id="mensagemFoto" role="status" aria-live="polite"></p>
    </div>
    <div class="form-actions">
      <button type="reset" class="btn-secondary">Limpar formulário</button>
      <button type="submit" id="salvar" name="salvar" class="btn-primary">Salvar</button>
    </div>
  </form>
</section>


<div id="modalTipoDoacao" class="modal oculta">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Novo tipo de doação</h2>
            <button type="button" id="btnFecharModalTipo" class="modal-close">
                &times;
            </button>
        </div>

        <form id="formCadastroTipoDoacao">
            <div class="form-field">
                <label for="descricaoCategoria">Descrição</label>

                <div class="input-with-button">
                    <input type="text" id="descricaoCategoria" name="descricao" placeholder="Descrição" maxlength="100" required>

                    <button type="submit" class="btn-primary" title="Cadastrar Tipo Doação">
                        <i class="bi bi-check-lg"></i>
                    </button>
                </div>
            </div>

            <div class="form-actions">
                <button
                    type="button"
                    id="btnCancelarTipo"
                    class="btn-secondary"
                >
                    Cancelar
                </button>
            </div>
        </form>
    </div>
</div>

  <?= loadFooter() ?>
