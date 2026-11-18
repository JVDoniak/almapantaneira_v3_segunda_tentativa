<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

$scriptsPagina = [
    "../js/consulta-cnpj.js?v=" . filemtime(__DIR__ . "/../JS/consulta-cnpj.js"),
    "../js/cadastro-fornecedor.js",
    "../js/mascara-endereco.js",
    "../js/mascara-cadastro-fornecedor.js"
    ];

echo loadHeader("Cadastro de Fornecedores - Alma Pantaneira", $scriptsPagina); ?>

<section class="content-header">
    <div>
        <h1> Fornecedores </h1>
        <p> Gerenciar e Cadastrar Fornecedores </p>
    </div>
    <a href="listagem-fornecedor.php" class="btn-outline"><i class="bi bi-arrow-left"></i> Voltar para Lista</a>
</section>

<section class="card">
    <div class="form-title">
        <h2>Cadastrar Fornecedor</h2>
    </div>

    <form id="formCadastroFornecedor">
        <div class="form-section">
            <div class="form-grid-3">
                <div class="form-field">
                    <label for="nome">Nome Fantasia</label>
                    <input type="text" id="nome" name="nome" placeholder="Informe o nome fantasia" required>
                </div>
                <div class="form-field">
                    <label for="razao_social">Razão Social</label>
                    <input type="text" id="razao_social" name="razao_social" placeholder="Informe a razão social" required>
                </div>
                <div class="form-field">
                    <label for="cpf_cnpj">CNPJ</label>
                    <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="00.000.000/0001-00" required>
                </div>
                <div class="form-field">
                    <label for="telefone">Telefone</label>
                    <input type="text" id="telefone" name="telefone" placeholder="(99) 99999-9999" required>
                </div>
                <div class="form-field">
                    <label for="email">E-mail</label>
                    <input type="email" id="email" name="email" placeholder="usuario@usuario.com" required>
                </div>
                <div class="form-field">
                    <label for="contato">Pessoa de Contato</label>
                    <input type="contato" id="contato" name="contato" placeholder="Nome da pessoa de contato" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-centered-clean">Endereço</h2>

            <div class="form-flex" style="margin-bottom: 24px;">
                <div class="form-field small">
                    <label for="cep">CEP</label>
                    <input type="text" id="cep" name="cep" placeholder="79999-999" required>
                </div>
                <div class="form-field" style="flex: 2 1 300px;">
                    <label for="rua">Logradouro</label>
                    <input type="text" id="logradouro" name="logradouro" placeholder="Av. Mato Grosso" required>
                </div>
                <div class="form-field small">
                    <label for="numero">Número</label>
                    <input type="text" id="numero" name="numero" maxlength="20" placeholder="Nº" required>
                </div>
                <div class="form-field" style="flex: 2 1 250px;">
                    <label for="complemento">Complemento</label>
                    <input type="text" id="complemento" name="complemento" placeholder="Quadra, Lote">
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-field">
                    <label for="bairro">Bairro</label>
                    <input type="text" id="bairro" name="bairro" placeholder="Santa Fé" required>
                </div>
                <div class="form-field">
                    <label for="cidade">Cidade</label>
                    <input type="text" id="cidade" name="cidade" placeholder="Campo Grande" required>
                </div>
                <div class="form-field">
                    <label for="estado">Estado</label>
                    <input type="text" id="estado" name="estado" placeholder="Mato Grosso do Sul" required>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="reset" class="btn-secondary">Limpar formulário</button>
            <button name="REQUEST_METHOD" type="submit" class="btn-primary">Salvar</button>
        </div>
    </form>
</section>

<?= loadFooter() ?>
