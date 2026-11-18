<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/listagem-usuario.js"
    ];
    
echo loadHeader("Listagem Usuários - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1>Lista de Usuários</h1>
            <p>Acompanhe os Usuários cadastrados.</p>
          </div>

          <a href="cadastro-usuario.php" class="btn-primary"><i class="bi bi-plus-lg"></i> Novo Usuário</a>
        </section>

        <section class="filter-card">
          <!-- O id é usado pelo JS para interceptar o envio do formulário -->
          <form id="listar-usuarios" action="" method="GET">
            <div class="filter-grid">

              <div class="form-field-list">
                <label for="buscar_usuario">Buscar Usuário</label>
                <input type="text" id="buscar_usuario" name="buscar_usuario" placeholder="Digite o nome do usuário">
              </div>

              <div class="form-field-list">
                <label for="cargo">Cargo</label>
                <!-- Opções iguais às do cadastro de usuário -->
                <select id="cargo" name="cargo">
                  <option value="">Todos os cargos</option>
                  <option value="Presidente">Presidente</option>
                  <option value="Administrador">Administrador</option>
                  <option value="Financeiro">Financeiro</option>
                  <option value="Usuário">Usuário</option>
                </select>
              </div>

              <div class="form-field-list">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="exemplo@example.invalid">
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
                  <th>Nome</th>
                  <th>Email</th>
                  <th>Cargo</th>
                  <th>Ações</th>
                </tr>
              </thead>
              <tbody id="bodyTable">
                <!-- o conteudo da lista esta sendo feita pelo js -->
              </tbody>
            </table>
          </div>
        </section>
<?= loadFooter() ?>