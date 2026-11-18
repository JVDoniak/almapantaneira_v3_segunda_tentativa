<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ;
 
 require_once "../../controller/Doador.php";
 require_once "../../controller/Projeto.php";

$objDoador = new Doador();
$listaDoadores = $objDoador->listarTodos();

$objProjeto = new Projeto();
$listaProjetos = $objProjeto->listarTodos();

$scriptsPagina = [
    "../js/editar-doacao.js?v=" . filemtime(__DIR__ . "/../JS/editar-doacao.js"),
    "../js/upload-comprovante.js"
    ];
    
echo loadHeader("Edição de Doação - Alma Pantaneira", $scriptsPagina); ?>

                <section class="content-header">
                    <div>
                        <h1>Doação</h1>
                        <p>Gerenciar e Editar Doação</p>
                    </div>
                    <a href="listagem-doacao.php" class="btn-outline">
                        <i class="bi bi-arrow-left"></i> Voltar para Lista
                    </a>
                </section>

                <section class="card">
                    <div class="form-title">
                        <h2>Editar Doação</h2>
                    </div>

                    <form id="form_edicao_doacao" action="#" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="id_doacao" name="id_doacao">

                        <div class="form-grid-3" style="margin-bottom: 24px;">

                            <div class="form-field">
                                <label for="nome_doador" class="campo-obrigatorio">Doador</label>
                                <input type="text" id="nome_doador" placeholder="Nome do doador" disabled style="background-color: var(--cinza-claro); cursor: not-allowed;">
                                <input type="hidden" id="id_doador" name="id_doador" value="">
                                <div id="sugestoes_doadores" class="autocomplete-lista"></div>
                            </div>


                            <div class="form-field">
                                <label for="tipo">Tipo de Doação</label>
                                <select id="tipo" name="tipo">
                                    <option value="" disabled selected hidden>Selecione o tipo</option>
                                    <option>Financeira</option>
                                    <option>Alimentícia</option>
                                    <option>Vestes</option>
                                    <option>Insumos</option>
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
                                <label for="descricao">Descrição da Doação</label>
                                <input type="text" id="descricao" name="descricao"
                                       placeholder="Contribuição para tratamento veterinário">
                            </div>

                            <div class="form-field">
                                <label for="forma">Forma de recebimento</label>
                                <select id="forma" name="forma">
                                    <option>Transferência Bancária</option>
                                    <option>PIX</option>
                                    <option>Dinheiro</option>
                                    <option>Cartão</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="data_doacao">Data da Doação</label>
                                <input type="date" id="data_doacao" name="data_doacao">
                            </div>

                        </div>

                        <div class="form-grid-2" style="margin-bottom: 24px;">
                            <div class="form-field">
                                <label for="valor">Valor da Doação</label>
            <small>Para doações a partir de R$ 1.000,00, o endereço do doador deve estar completo.</small>
                                <input type="text" id="valor" name="valor" disabled style="background-color: var(--cinza-claro); cursor: not-allowed;">
                            </div>

                            <div class="form-field">
                                <label for="status">Status da Doação</label>
                                <select id="status" name="status">
                                    <option selected>Selecione o status da doação</option>
                                    <option>Não confirmada</option>
                                    <option>Confirmada</option>
                                </select>
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

                        <div class="form-actions">
                            <button type="reset" class="btn-secondary">Limpar formulário</button>
                            <button type="submit" class="btn-primary">Salvar</button>
                        </div>

                    </form>
                </section> 

<?= loadFooter() ?>
