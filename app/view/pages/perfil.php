<?php require_once "../components/header.php"; ?>
<?php require_once "../../utils/loadFooter.php" ; 

// Passa os dois scripts dentro da mesma string
$scriptsPagina = [
    "../js/carregar-perfil.js"
];

echo loadHeader("Perfil - Alma Pantaneira", $scriptsPagina); ?>

                <section class="content-header">
                    <div>
                        <h1>Meu Perfil</h1>
                        <p>Visualize suas informações pessoais</p>
                    </div>
                </section>

                <section class="card edit-doacao-card">

                    <div class="edit-card-header">
                        <h2 class="edit-h2"> Dados Pessoais </h2>

                        <button id="btn_editar_perfil" class="btn-outline">
                            <i class="bi bi-pencil-square"></i> Editar Perfil
                        </button>
                    </div>


                    <div class="avatar-wrapper">
                        <div class="avatar-container">
                            <div class="edit-card-icon">
                               <i
                                    class="bi bi-person-fill"
                                    id="iconePerfil">
                                </i>
                                <img
                                     id="fotoPerfil"
                                    src=""
                                    alt="Foto de Perfil"
                                    style="display:none;">
                            </div>
                            <input type="file" id="uploadFoto" name="uploadFoto" accept="image/*" hidden>
                        </div>
                    </div>


                    <form onsubmit="return false;">
                        <div class="edit-form-grid">
                            <div class="form-field-perfil">
                                <label>Nome Completo</label>
                                <input type="text" name="nome" class="input-view" id="nome" readonly>
                                <input type="hidden" name="id_usuario" id="id_usuario">
                                <input type="hidden" id="informacaoPerfil" name="informacaoPerfil" value="editar">
                            </div>

                            <div class="form-field-perfil">
                                <label>CPF</label>
                                <input type="text" class="input-view" id="cpf" maxlength="14" readonly>
                            </div>

                            <div class="form-field-perfil">
                                <label>Telefone</label>
                                <input type="text" class="input-view" name="telefone" id="telefone" placeholder="(99) 99999-9999" readonly>
                            </div>

                            <div class="form-field-perfil">
                                <label>Tipo de Usuário</label>
                                <input type="text" class="input-view" id="tipo" readonly>
                            </div>

                            <div class="form-field-perfil">
                                <label>Email</label>
                                <input type="email" class="input-view" id="email" readonly>
                            </div>

                            <div class="form-field-perfil">
                                <label>Senha</label>
                                <input type="password" id="senha" name="senha" placeholder="Senha" value="******" readonly>
                            </div>
                        </div>

                        <div class="perfil-nota">
                            <strong>Nota:</strong>
                            Para alterar Email, CPF ou Tipo de Usuário, entre em contato com o administrador do sistema.
                        </div>
                    </form>

                </section>

                <section class="card info-sistema-card">
                    <h3>Informações do Sistema</h3>

                    <div class="info-grid">
                        <div>
                            <span>Último acesso</span>
                            <p>22 de Outubro de 2026 às 09:15</p>
                        </div>

                        <div>
                            <span>Data de Cadastro</span>
                            <p>11 de Outubro de 2026</p>
                        </div>

                        <div>
                            <span>Status da Conta</span>
                            <div id="status_conta">
                                
                            </div>
                        </div>

                        <div>
                            <span>Permissões</span>
                            <p>Acesso completo ao módulo Financeiro</p>
                        </div>
                    </div>
                </section>
<?= loadFooter() ?>
