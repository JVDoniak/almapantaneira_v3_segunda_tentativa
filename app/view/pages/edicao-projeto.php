<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/editar-projeto.js"
    ];
    
echo loadHeader("Edição de Projeto - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
            <div>
                <h1> Editar Projeto </h1>
                <p> Gerenciar e Editar Projeto </p>
            </div>
        </section>

        <section class="card">
          <form id="form_editar_projeto" action="#" method="POST" enctype="multipart/form-data">
            <div class="form-title">
              <h2>Editar Projeto</h2>
            </div>
            
            <!-- Primeira seção de inputs organizada na grid padrão de 3 colunas -->
            <div class="form-grid-3" style="margin-bottom: 24px;">
                <input type="hidden" id="id_projeto" name="id_projeto" >


              <div class="form-field">
                <label for="nome">Nome do Projeto</label>
                <input type="text" id="nome" name="nome" placeholder="Nome do projeto">
              </div>
              
              <div class="form-field">
                <label for="responsavel">Responsável</label>
                <input type="text" id="responsavel" name="responsavel" placeholder="Responsável pelo projeto">
              </div>
              
              <div class="form-field">
                <label for="local">Local</label>
                <input type="text" id="local_projeto" name="local_projeto" placeholder="São Batista">
              </div>

              <div class="form-field">
                <label for="tipo_projeto">Tipo do Projeto</label>
                <select id="tipo_projeto" name="tipo_projeto">
                  <option value="" disabled selected hidden>Selecione uma opção</option>
                  <option>Saúde</option>
                  <option>Assistência Social</option>
                  <option>Educação</option>
                  <option>Pesquisa Médica</option>
                </select>
               </div>
              
              <div class="form-field">
                <label for="data_inicio">Data de início</label>
                <input type="date" id="data_inicio" name="data_inicio">
              </div>
              
              <div class="form-field">
                <label for="data_termino">Data de término</label>
                <input type="date" id="data_termino" name="data_termino">
              </div>

              <div class="form-field">
                <label for="valor_projeto">Valor doado para o projeto</label>
                <input type="text" id="valor_projeto" name="valor_projeto" disabled style="background-color: var(--cinza-claro); cursor: not-allowed;">
              </div>

              <div class="form-field">
                <label for="andamento">Andamento do Projeto</label>
                <select id="andamento" name="andamento">
                  <option>Em planejamento</option>
                  <option>Em andamento</option>
                  <option>Concluído</option>
                </select>
              </div>
            </div>

            <!-- Segunda seção contendo a descrição expandida em 100% da largura -->
            <div class="form-flex">
              <div class="form-field full">
                <label for="descricao">Descrição</label>
                <textarea id="descricao" name="descricao" placeholder="Descreva o projeto"></textarea>
              </div>
            </div>

            <div class="form-actions">
              <button type="reset" class="btn-secondary">Limpar formulário</button>
              <button type="submit" class="btn-primary">Salvar</button>
            </div>
          </form>
        </section>
        
<?= loadFooter() ?>
