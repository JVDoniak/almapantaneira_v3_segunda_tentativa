<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/consulta-cnpj.js?v=" . filemtime(__DIR__ . "/../JS/consulta-cnpj.js"),
    "../js/editar-doador.js",
    "../js/mascara-endereco.js",
    "../js/mascara-validacao.js"
];
    
echo loadHeader("Edição de Doador - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
  <div>
    <h1> Editar Doador </h1>
    <p> Gerenciar e Editar Doador </p>
  </div>
  <a href="listagem-doador.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
  <div class="form-title">
    <h2>Editar Doador</h2>
  </div>
    
  <form id="formEdicaoDoador">
    <input type="hidden" id="id_doador" name="id_doador">
    <input type="hidden" id="id_endereco" name="id_endereco">

    <div class="form-section">
      <div class="form-grid-3">
        
        <!-- Tipo de Doador -->
        <div class="form-field">
          <label for="tipo">Tipo de doador</label>
          <select id="tipo" name="tipo" onchange="aplicarVisibilidadeTipo(this.value)">
            <option value="" disabled hidden>Selecione a opção</option>
            <option value="Pessoa Física">Pessoa Física</option>
            <option value="Pessoa Jurídica">Pessoa Jurídica</option>
          </select>
        </div>

        <!-- Documento (CPF ou CNPJ) -->
        <div class="form-field">
          <label for="cpf_cnpj" id="label-doc">CPF/CNPJ</label>
          <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="999.999.999-99">
        </div>

        <!-- CAMPO EXCLUSIVO PF -->
        <div class="form-field campo-pf">
          <label for="nome">Nome Completo</label>
          <input type="text" id="nome" name="nome" placeholder="Nome completo">
        </div>

        <!-- CAMPOS EXCLUSIVOS PJ -->
        <div class="form-field campo-pj" style="display: none;">
          <label for="razao_social">Razão Social</label>
          <input type="text" id="razao_social" name="razao_social" placeholder="Razão Social">
        </div>

        <div class="form-field campo-pj" style="display: none;">
          <label for="nome_fantasia">Nome Fantasia</label>
          <input type="text" id="nome_fantasia" name="nome_fantasia" placeholder="Nome Fantasia">
        </div>

        <div class="form-field campo-pj" style="display: none;">
          <label for="contato_empresa">Pessoa de Contato</label>
          <input type="text" id="contato_empresa" name="contato_empresa" placeholder="Nome da pessoa de contato">
        </div>

        <!-- CAMPOS COMUNS -->
        <div class="form-field">
          <label for="telefone">Telefone</label>
          <input type="text" id="telefone" name="telefone" placeholder="(99) 99999-9999">
        </div>

        <div class="form-field">
          <label for="email">E-mail</label>
          <input type="email" id="email" name="email" placeholder="usuario@usuario.com">
        </div>

      </div>
    </div>

    <!-- Seção do Endereço -->
    <div class="form-section">
      <h2>Endereço (opcional)</h2>
      <p>Necessário para doações individuais a partir de R$ 1.000,00. Se informar um endereço, preencha os dados completos.</p>
      
      <div class="form-flex" style="margin-bottom: 24px;">
        <div class="form-field small">
          <label for="cep">CEP</label>
          <input type="text" id="cep" name="cep" placeholder="99999-999">
        </div>
        <div class="form-field" style="flex: 2 1 300px;">
          <label for="logradouro">Logradouro</label>
          <input type="text" id="logradouro" name="logradouro" placeholder="Logradouro">
        </div>
        <div class="form-field small">
          <label for="numero">Número</label>
          <input type="text" id="numero" name="numero" maxlength="20" placeholder="Nº">
        </div>
        <div class="form-field" style="flex: 2 1 250px;">
          <label for="complemento">Complemento</label>
          <input type="text" id="complemento" name="complemento" placeholder="Complemento">
        </div>
      </div>

      <div class="form-grid-3">
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
          <select id="estado" name="estado">
            <option value="" disabled hidden>Selecione o Estado</option>
          </select>
        </div>
      </div>
    </div>

    <div class="form-actions">
      <a href="listagem-doador.php" class="btn-secondary" style="text-decoration: none; display: inline-flex; align-items: center; justify-content: center;">Cancelar</a>
      <button type="submit" class="btn-primary">Salvar</button>
    </div>
  </form>
</section>

<?= loadFooter() ?>