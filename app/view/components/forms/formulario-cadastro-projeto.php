<form id="formCadastroProjeto" action="#" method="POST" enctype="multipart/form-data" required>
                        <div class="form-title">
                            <h2>Cadastrar Projeto</h2>
                        </div>

                        <div class="form-grid-3" style="margin-bottom: 24px;">
                            <div class="form-field">
                                <label for="nomeProjeto">Nome do Projeto</label>
                                <input type="text" id="nomeProjeto" name="nome" placeholder="Nome do projeto" required>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_responsavel">Responsável</label>
                                <input type="text" id="modal_projeto_responsavel" name="responsavel" placeholder="Responsável pelo projeto" required>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_local_projeto">Local</label>
                                <input type="text" id="modal_projeto_local_projeto" name="local_projeto" placeholder="São Batista" required>
                            </div>

                            <div class="form-field">
                                <div style="display:flex; justify-content: space-between; align-items:center;">
                                    <label for="modal_projeto_tipo_projeto">Tipo do Projeto</label>
                                </div>
                                <select id="modal_projeto_tipo_projeto" name="tipo_projeto" required>
                                    <option value="" disabled selected hidden>Selecione uma opção</option>
                                    <option>Saúde</option>
                                    <option>Assistência Social</option>
                                    <option>Educação</option>
                                    <option>Pesquisa Médica</option>
                                </select>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_valor_projeto">Valor</label>
                                <input type="number" step="0.01" id="modal_projeto_valor_projeto" name="valor_projeto" placeholder="R$ 2.500,00" required>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_data_inicio">Data de início</label>
                                <input type="date" id="modal_projeto_data_inicio" name="data_inicio" required>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_data_termino">Data de término</label>
                                <input type="date" id="modal_projeto_data_termino" name="data_termino" required>
                            </div>

                            <div class="form-field">
                                <label for="modal_projeto_andamento">Andamento do Projeto</label>
                                <select id="modal_projeto_andamento" name="andamento" required>
                                    <option value="" disabled selected hidden>Selecione uma opção</option>
                                    <option>Em planejamento</option>
                                    <option>Em andamento</option>
                                    <option>Concluído</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-flex">
                            <div class="form-field full">
                                <label for="modal_projeto_descricao">Descrição</label>
                                <textarea id="modal_projeto_descricao" name="descricao" placeholder="Descreva as metas e detalhes do projeto..."></textarea>
                            </div>
                        </div>
                        <div id="modal_projeto_loader"></div>

                        <div class="form-actions">
                            <button type="reset" class="btn-secondary">Limpar formulário</button>
                            <button type="submit" class="btn-primary">Salvar</button>
                        </div>
                    </form>
