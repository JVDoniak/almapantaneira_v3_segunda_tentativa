<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/consulta-cnpj.js?v=" . filemtime(__DIR__ . "/../JS/consulta-cnpj.js"),
    "../js/editar-fornecedor.js",
    "../js/mascara-endereco.js",
    "../js/mascara-validacao.js"
    ];

echo loadHeader("Edição de Fornecedor - Alma Pantaneira", $scriptsPagina); ?>

        <section class="content-header">
          <div>
            <h1> Editar Fornecedor </h1>
            <p> Edição de Fornecedor </p>
          </div>
          <a href="listagem-fornecedor.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
        </section>

        <section class="card">
          <form id="formEdicaoFornecedor" action="#" method="POST" enctype="multipart/form-data">
            <input type="hidden" id="id_fornecedor" name="id_fornecedor">
            <div class="form-title">
              <h2>Editar Fornecedor</h2>
            </div>
            
            <!-- Dados principais do Fornecedor em Grid de 3 colunas -->
            <div class="form-section">
              <div class="form-grid-3">
                <div class="form-field">
                  <label for="nome">Nome Fantasia</label>
                  <input type="text" id="nome" name="nome" placeholder="Informe o nome fantasia">
                </div>
                <div class="form-field">
                    <label for="razao_social">Razão Social</label>
                    <input type="text" id="razao_social" name="razao_social" placeholder="Informe a razão social">
                </div>
                <div class="form-field">
                  <label for="cnpj">CNPJ</label>
                  <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="99.999.999/9999-99">
                </div>
                <div class="form-field"> 
                  <label for="telefone">Telefone</label>
                  <input type="tel" id="telefone" name="telefone" placeholder="(67) 99999-9999">
                </div>
                <div class="form-field">
                  <label for="email">E-mail</label>
                  <input type="email" id="email" name="email" placeholder="exemplo@email.com">
                </div>
                <div class="form-field">
                  <label for="contato">Pessoa de Contato</label>
                  <input type="text" id="contato" name="contato" placeholder="Nome da pessoa de contato">
                </div>
              </div>
            </div>

            <!-- Seção do Endereço padronizada -->
            <div class="form-section">
              <h2>Endereço</h2>
              
              <!-- Linha 1: CEP (menor), Rua (expande), Número (menor) -->
              <div class="form-flex" style="margin-bottom: 24px;">
                <input type="hidden" id="id_endereco" name="id_endereco">
                
                <div class="form-field small">
                  <label for="cep">CEP</label>
                  <input type="text" id="cep" name="cep" placeholder="00000-000">
                </div>
                <div class="form-field" style="flex: 2 1 400px;">
                  <label for="rua">Logradouro</label>
                  <input type="text" id="logradouro" name="logradouro" placeholder="Av. Mato Grosso">
                </div>
                <div class="form-field small">
                  <label for="numero">Número</label>
                  <input type="text" id="numero" name="numero" maxlength="20" placeholder="Nº">
                </div>
                <div class="form-field small">
                  <label for="numero">Complemento</label>
                  <input type="text" id="complemento" name="complemento" placeholder="Quadra, Lote">
                </div>
              </div>

              <!-- Linha 2: Bairro, Cidade e Estado em colunas equilibradas -->
              <div class="form-grid-2">
                <div class="form-field">
                  <label for="bairro">Bairro</label>
                  <input type="text" id="bairro" name="bairro" placeholder="Santa Fé">
                </div>
                <div class="form-field">
                  <label for="cidade">Cidade</label>
                  <input type="text" id="cidade" name="cidade" placeholder="Campo Grande">
                </div>
                <div class="form-field">
                  <label for="estado">Estado</label>
                  <input type="text" id="estado" name="estado" placeholder="Mato Grosso do Sul">
                </div>
              </div>
            </div>

            <div class="form-actions">
              <a href="./listagem-fornecedor.php" class="btn-secondary">Cancelar</a>
              <button type="submit" id="salvar" name="salvar" class="btn-primary">Salvar</button>
            </div>
          </form>
        </section>

<?= loadFooter() ?>
