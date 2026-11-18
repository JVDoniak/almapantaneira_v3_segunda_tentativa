<form id="formCadastroFornecedor">
        <div class="form-section">
            <div class="form-grid-3">
                <div class="form-field">
                    <label for="modal_fornecedor_nome">Nome Fantasia</label>
                    <input type="text" id="modal_fornecedor_nome" name="nome" placeholder="Informe o nome fantasia" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_razao_social">Razão Social</label>
                    <input type="text" id="modal_fornecedor_razao_social" name="razao_social" placeholder="Informe a razão social" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_cpf_cnpj">CNPJ</label>
                    <input type="text" id="modal_fornecedor_cpf_cnpj" name="cpf_cnpj" placeholder="00.000.000/0001-00" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_telefone">Telefone</label>
                    <input type="text" id="modal_fornecedor_telefone" name="telefone" placeholder="(99) 99999-9999" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_email">E-mail</label>
                    <input type="email" id="modal_fornecedor_email" name="email" placeholder="usuario@usuario.com" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_contato">Pessoa de Contato</label>
                    <input type="contato" id="modal_fornecedor_contato" name="contato" placeholder="Nome da pessoa de contato" required>
                </div>
            </div>
        </div>

        <div class="form-section">
            <h2 class="section-centered-clean">Endereço</h2>

            <div class="form-flex" style="margin-bottom: 24px;">
                <div class="form-field small">
                    <label for="modal_fornecedor_cep">CEP</label>
                    <input type="text" id="modal_fornecedor_cep" name="cep" placeholder="79999-999" required>
                </div>
                <div class="form-field" style="flex: 2 1 300px;">
                    <label for="modal_fornecedor_rua">Logradouro</label>
                    <input type="text" id="modal_fornecedor_logradouro" name="logradouro" placeholder="Av. Mato Grosso" required>
                </div>
                <div class="form-field small">
                    <label for="modal_fornecedor_numero">Número</label>
                    <input type="text" id="modal_fornecedor_numero" name="numero" maxlength="20" placeholder="Nº" required>
                </div>
                <div class="form-field" style="flex: 2 1 250px;">
                    <label for="modal_fornecedor_complemento">Complemento</label>
                    <input type="text" id="modal_fornecedor_complemento" name="complemento" placeholder="Quadra, Lote">
                </div>
            </div>

            <div class="form-grid-3">
                <div class="form-field">
                    <label for="modal_fornecedor_bairro">Bairro</label>
                    <input type="text" id="modal_fornecedor_bairro" name="bairro" placeholder="Santa Fé" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_cidade">Cidade</label>
                    <input type="text" id="modal_fornecedor_cidade" name="cidade" placeholder="Campo Grande" required>
                </div>
                <div class="form-field">
                    <label for="modal_fornecedor_estado">Estado</label>
                    <input type="text" id="modal_fornecedor_estado" name="estado" placeholder="Mato Grosso do Sul" required>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <button type="reset" class="btn-secondary">Limpar formulário</button>
            <button name="REQUEST_METHOD" type="submit" class="btn-primary">Salvar</button>
        </div>
    </form>
