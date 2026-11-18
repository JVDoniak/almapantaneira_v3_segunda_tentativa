<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/consulta-cnpj.js?v=" . filemtime(__DIR__ . "/../JS/consulta-cnpj.js"),
    "../js/cadastro-doador.js?v=" . filemtime(__DIR__ . "/../JS/cadastro-doador.js"),
    "../js/mascara-validacao.js",
    "../js/mascara-endereco.js"
];

echo loadHeader("Cadastro de Doadores - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
  <div>
    <h1> Doadores </h1>
    <p> Gerenciar e Cadastrar Doadores </p>
  </div>
  <a href="listagem-doador.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
  <div id="step-tipo-doador">
    <div class="form-title">
      <h2>Selecione o Tipo de Doador</h2>
      <p style="color: var(--cinza-texto); margin-top: 6px;">Escolha a categoria do doador para prosseguir</p>
    </div>

    <div class="tipo-doador-container" style="display: flex; gap: 16px; margin-top: 20px;">
      <div class="card-tipo-doador" onclick="selecionarTipoDoador('Pessoa Física')" style="cursor: pointer; border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1;">
        <div class="icon-box"><i class="bi bi-person" style="font-size: 2rem;"></i></div>
        <h3>Pessoa Física</h3>
        <p>Cadastro de doadores individuais (CPF)</p>
      </div>

      <div class="card-tipo-doador" onclick="selecionarTipoDoador('Pessoa Jurídica')" style="cursor: pointer; border: 1px solid #ccc; padding: 20px; border-radius: 8px; flex: 1;">
        <div class="icon-box"><i class="bi bi-building" style="font-size: 2rem;"></i></div>
        <h3>Pessoa Jurídica</h3>
        <p>Cadastro de empresas e instituições (CNPJ)</p>
      </div>
    </div>
  </div>

  <!-- Passo 2: Formulário -->
  <div id="step-formulario" style="display: none;">
    <div class="selected-type-badge" style="margin-bottom: 20px;">
      <span><i id="badge-icon" class="bi bi-person-check-fill"></i>Tipo Selecionado: <strong id="badge-texto">Pessoa Física</strong></span>
      <button type="button" class="btn-change-type" onclick="reiniciarSelecao()"><i class="bi bi-arrow-left"></i> Alterar tipo de Doador</button>
    </div>

    <form id="formCadastro">
      <input type="hidden" id="tipo" name="tipo" value="Pessoa Física">

      <div class="form-section">
        <div class="form-grid-3">
          
          <!-- Documento (CPF ou CNPJ) -->
          <div class="form-field">
            <label for="cpf_cnpj" id="label-doc">CPF <span style="color: red;">*</span></label>
            <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="000.000.000-00">
          </div>

          <!-- Campos Específicos PF -->
          <div class="form-field campo-pf">
            <label for="nome">Nome Completo <span style="color: red;">*</span></label>
            <input type="text" id="nome" name="nome" placeholder="Nome completo">
          </div>

          <!-- Campos Específicos PJ -->
          <div class="form-field campo-pj" style="display: none;">
            <label for="razao_social">Razão Social <span style="color: red;">*</span></label>
            <input type="text" id="razao_social" name="razao_social" placeholder="Razão Social">
          </div>

          <div class="form-field campo-pj" style="display: none;">
            <label for="nome_fantasia">Nome Fantasia <span style="color: red;">*</span></label>
            <input type="text" id="nome_fantasia" name="nome_fantasia" placeholder="Nome Fantasia">
          </div>

          <div class="form-field campo-pj" style="display: none;">
            <label for="contato_empresa">Pessoa de Contato <span style="color: red;">*</span></label>
            <input type="text" id="contato_empresa" name="contato_empresa" placeholder="Nome da pessoa de contato">
          </div>

          <!-- Campos Comuns -->
          <div class="form-field">
            <label for="telefone">Telefone <span style="color: red;">*</span></label>
            <input type="text" id="telefone" name="telefone" placeholder="(99) 99999-9999">
          </div>

          <div class="form-field">
            <label for="email">E-mail <span style="color: red;">*</span></label>
            <input type="email" id="email" name="email" placeholder="usuario@usuario.com">
          </div>

        </div>
      </div>

      <!-- Endereço -->
      <div class="form-section">
        <h2>Endereço (opcional)</h2>
      <p>Necessário para doações individuais a partir de R$ 1.000,00. Se informar um endereço, preencha os dados completos.</p>
        <div class="form-flex">
          <div class="form-field small">
            <label for="cep">CEP</label>
            <input type="text" id="cep" name="cep" placeholder="99999-999">
          </div>
          <div class="form-field">
            <label for="logradouro">Logradouro</label>
            <input type="text" id="logradouro" name="logradouro" placeholder="Endereço">
          </div>
          <div class="form-field small">
            <label for="numero">Número</label>
            <input type="text" id="numero" name="numero" maxlength="20" placeholder="Nº">
          </div>
          <div class="form-field">
            <label for="complemento">Complemento</label>
            <input type="text" id="complemento" name="complemento" placeholder="Complemento">
          </div>
        </div>

        <div class="form-grid-3" style="margin-top: 24px;">
          <div class="form-field">
            <label for="bairro">Bairro</label>
            <input type="text" id="bairro" name="bairro" placeholder="Bairro">
          </div>
          <div class="form-field">
            <label for="cidade">Cidade</label>
            <input type="text" id="cidade" name="cidade" placeholder="Cidade">
          </div>

          <div class="form-field">
            <label for="estado">Estado</label>
            <input type="text" id="estado" name="estado" placeholder="Mato Grosso do Sul">
          </div>

          <!-- <div class="form-field">
            <label for="estado">Estado</label>
            <select id="estado" name="estado">
            </select>
          </div> -->
        </div>
      </div>

      <div class="form-actions">
        <button type="reset" class="btn-trash"><i class="bi bi-trash"></i></button>
        <button type="button" class="btn-secondary" onclick="reiniciarSelecao()">Cancelar</button>
        <button type="submit" class="btn-primary" id="btnSalvar">Salvar</button>
      </div>
    </form>
  </div>
</section>

<?= loadFooter() ?>