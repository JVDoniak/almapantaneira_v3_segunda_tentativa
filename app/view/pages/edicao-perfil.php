<?php require_once "../components/header.php"; ?>
<?php require_once "../../actions/upload-imagem.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

// Passa os dois scripts dentro da mesma string
$scriptsPagina = [
    "../js/carregar-edicao-perfil.js",
    "../js/editar-perfil2.js",
    "../js/mascara-validacao.js"
];

echo loadHeader("Perfil - Alma Pantaneira", $scriptsPagina); ?>

                <section class="content-header">
                    <div>
                        <h1>Editar Perfil</h1>
                        <p>Gerencie suas informações pessoais</p>
                    </div>
                </section>

                <section class="card edit-doacao-card">
                    <form id="formEditarPerfil" method="POST" enctype="multipart/form-data">



                        <div class="edit-card-header">
                            <h2 class="edit-h2"> Dados Pessoais </h2>
                        </div>

                        <div class="avatar-wrapper">
                            <div class="avatar-container">
                                
                                <div class="edit-card-icon">
                                    <i class="bi bi-person-fill" id="iconePerfil"></i>
                                    <img id="fotoPerfil" src="" alt="Foto de Perfil" style="display:none;">
                                </div>

                                <div class="camera-icon" onclick="document.getElementById('uploadFoto').click()">
                                    <i class="bi bi-camera-fill"></i>
                                </div>

                                <input type="file" id="uploadFoto" name="uploadFoto" accept="image/*" hidden>
                            </div>
                        </div>

                        <div class="edit-form-grid">
                            <div class="form-field">
                                <label>Nome Completo</label>
                                <input id="nome" name="nome" type="text" class="input-edit" placeholder="Nome Completo">
                                <input type="hidden" name="id_usuario" id="id_usuario">
                                <input type="hidden" id="informacaoPerfil" name="informacaoPerfil" value="editar">
                            </div>

                            <div class="form-field">
                                <label>CPF</label>
                                <input type="text" class="input-view" id="cpf" placeholder="000.000.000-00" maxlength="14" readonly>
                            </div>

                            <div class="form-field">
                                <label>Telefone</label>
                                <input type="text" class="input-edit" id="telefone" name="telefone" placeholder="(99) 99999-9999">
                            </div>

                            <div class="form-field">
                                <label>Tipo de Usuário</label>
                                <input id="tipo" type="text" class="input-view" placeholder="Cargo" readonly>
                            </div>

                            <div class="form-field">
                                <label>Email</label>
                                <input id="email" type="email" class="input-view" placeholder="exemplo@example.invalid" readonly>
                            </div>

                            <div class="form-field"> 
                                <label for="senha">Senha</label>
                                <div class="input-senha">
                                    <input type="password" id="senha" name="senha" value="***********" disabled>
                                    <button type="button" class="toggle-senha" aria-label="Mostrar senha" style="display:none";>
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="form-field">
                                <label for="confirmarSenha">Confirmar nova senha</label>
                                <div class="input-senha">
                                    <input type="password" id="confirmarSenha" name="confirmarSenha" value="***********" disabled>
                                    <button type="button" class="toggle-senha" aria-label="Mostrar senha" style="display:none";>
                                        <i class="bi bi-eye"></i>
                                    </button> 
                                </div>

                                <div class="new-password">
                                 <label for="senha">Nova senha</label>

                                    <button type="button" id="editarSenha" class="btn-edit-password">
                                        <i class="bi bi-pencil-fill"></i>
                                    </button> 
                                </div> 
                        </div>
                    </div>

                        <div class="perfil-nota">
                            <strong>Nota:</strong>
                            Para alterar Email, CPF ou Tipo de Usuário, entre em contato com o administrador do sistema.
                        </div>

                        <div class="edit-form-actions">
                            <button type="reset" class="btn-secondary">Cancelar</button>
                            <button type="submit" name="salvar" class="btn-primary">Salvar</button>
                        </div>
                    </form>
                </section>
                
 <?= loadFooter() ?>
