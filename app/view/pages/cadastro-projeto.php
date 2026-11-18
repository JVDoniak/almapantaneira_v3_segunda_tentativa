<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/cadastro-projeto.js"
    ];
    
echo loadHeader("Cadastro de Projetos - Alma Pantaneira", $scriptsPagina); ?>

                <section class="content-header">
                    <div>
                        <h1>Projetos</h1>
                        <p> Gerenciar e Cadastrar Projetos </p>
                    </div>
                    <a href="listagem-projeto.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
                </section>

                <section class="card">
                    <form id="formCadastroProjeto" action="#" method="POST" enctype="multipart/form-data" required>
                        <div class="form-title">
                            <h2>Cadastrar Projeto</h2>
                        </div>
                        
                        <div class="form-grid-3" style="margin-bottom: 24px;">
                            <div class="form-field">
                                <label for="nome">Nome do Projeto</label>
                                <input type="text" id="nome_projeto" name="nome" placeholder="Nome do projeto" required>
                            </div>
                            
                            <div class="form-field">
                                <label for="responsavel">Responsável</label>
                                <input type="text" id="responsavel" name="responsavel" placeholder="Responsável pelo projeto" required>
                            </div>
                            
                            <div class="form-field">
                                <label for="local_projeto">Local</label>
                                <input type="text" id="local_projeto" name="local_projeto" placeholder="São Batista" required>
                            </div>

                            <div class="form-field">
                                <div style="display:flex; justify-content: space-between; align-items:center;">
                                    <label for="tipo_projeto">Tipo do Projeto</label>
                                </div>
                                <select id="tipo_projeto" name="tipo_projeto" required>
                                    <option value="" disabled selected hidden>Selecione uma opção</option>
                                    <option>Saúde</option>
                                    <option>Assistência Social</option>
                                    <option>Educação</option>
                                    <option>Pesquisa Médica</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="valor_projeto">Valor</label>
                                <input type="number" step="0.01" id="valor_projeto" name="valor_projeto" placeholder="R$ 2.500,00" required>
                            </div>
                            
                            <div class="form-field">
                                <label for="data_inicio">Data de início</label>
                                <input type="date" id="data_inicio" name="data_inicio" required>
                            </div>
                            
                            <div class="form-field">
                                <label for="data_termino">Data de término</label>
                                <input type="date" id="data_termino" name="data_termino" required>
                            </div>

                            <div class="form-field">
                                <label for="andamento">Andamento do Projeto</label>
                                <select id="andamento" name="andamento" required>
                                    <option value="" disabled selected hidden>Selecione uma opção</option>
                                    <option>Em planejamento</option>
                                    <option>Em andamento</option>
                                    <option>Concluído</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-flex">
                            <div class="form-field full">
                                <label for="descricao">Descrição</label>
                                <textarea id="descricao" name="descricao" placeholder="Descreva as metas e detalhes do projeto..."></textarea>
                            </div>
                        </div>
                        <div id="loader"></div>
                        
                        <div class="form-actions">
                            <button type="reset" class="btn-secondary">Limpar formulário</button>
                            <button type="submit" class="btn-primary">Salvar</button>   
                        </div>          
                    </form>
                </section>
<?= loadFooter() ?>