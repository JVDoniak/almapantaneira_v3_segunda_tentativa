<?php
require_once "../../public/includes/autenticacao.php";
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title> Alma Pantaneira</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../css/estilo-padrao/global.css">
  <link rel="stylesheet" href="../css/estilo-padrao/layout.css">
  <link rel="stylesheet" href="../css/estilo-padrao/forms.css">
  <link rel="stylesheet" href="../css/estilo-padrao/buttons.css">
  <link rel="stylesheet" href="../css/visualizacao-unica-doador.css">
</head>

<body>
  <div class="app-layout">
    <?php include '../components/sidebar.php'; ?>
    <div class="app-main">
      <?php include '../components/menu-superior.php'; ?>
      <main class="main-content">

       <section class="content-header">
          <div>
            <h1>Visualização de Doador</h1>
              <p>Gerenciar e Editar Doador</p>
          </div>
            <a href="listagem-doador.php" class="btn-outline">
              <i class="bi bi-arrow-left"></i> Voltar para Lista
            </a>
                </section>

        <section class="card-doador">
          <div class="left">
            <h3>Informações do Doador</h3>
            <div class="teste4">
              <div class="teste5">
                <h4><i class="bi bi-person"></i> Nome</h4>
                <p>Maria Silva Santos</p>
              </div>
              <div class="teste5">
                <h4><i class="bi bi-person-vcard"></i> CPF</h4>
                <p>123.456.789.00</p>
              </div>
              <div class="teste5">
                <h4><i class="bi bi-telephone-outbound"></i> Telefone</h4>
                <p>(67) 99999-9999</p>
              </div>
              <div class="teste5">
                <h4><i class="bi bi-geo-alt"></i> Endereço</h4>
                <p>Rua das Flores, 123 - Centro, Campo Grande - MS</p>
              </div>

            <div>
              <h4>Status</h4>
              <div class="teste6">
                <span class="tag">Pessoa Física</span>
                <span class="tag ativo">Ativo</span>
                <span class="tag recorrente">Doador Recorrente</span>
              </div>
            </div>
          </div>
        </div>

          <div class="right">
            <h3>Resumo das Doações</h3>
            <div class="div3">
              <div class="box">
                <p><i class="bi bi-currency-dollar"></i> Total Doado</p>
                <strong>R$ 5.100,00</strong>
              </div>

              <div class="box">
                <p><i class="bi bi-alt"></i> Número de Doações</p>
                <strong>12</strong>
              </div>

              <div class="box">
                <p><i class="bi bi-calendar-date"></i> Última Doação</p>
                <strong>R$ 500,00</strong><br>
                <small>27/12/2025</small>
              </div>
             </div>
          </div>
        </section>
        <section class="card">
          <table>
            <thead>
              <tr>
                <th>Data</th>
                <th>Tipo da Doação</th>
                <th>Valor</th>
                <th>Status</th>
                <th>Ação</th>
              </tr>
            </thead>
            <tr>
              <td>28/02/2026</td>
              <td>Dinheiro</td>
              <td>R$ 300.00</td>
             
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>

              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Dinheiro</td>
              <td>R$ 300.00</td>
             
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>

              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Pix</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Pix</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Pix</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Dinheiro</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Dinheiro</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Transferência</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>
            <tr>
              <td>28/02/2026</td>
              <td>Transferência</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Transferência</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Transferência</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>

            <tr>
              <td>28/02/2026</td>
              <td>Pix</td>
              <td>R$ 300.00</td>
              <td>
                <div class="teste">
                  <p class="conf">Confirmada</p>
                </div>
              </td>


              <td class="acoes">
                <i class="bi bi-eye"></i>
                <i class="bi bi-pencil-square"></i>

              </td>
            </tr>
        </section>



      </main>
    </div>
  </div>
</body>

</html>