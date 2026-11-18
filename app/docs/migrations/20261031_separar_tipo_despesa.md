# Separacao de tipo e categoria de despesa

Nota: este documento registra o planejamento inicial. A implementacao foi
realizada posteriormente e esta descrita em ../ER-02-relatorio-SENAC.md.
O script continua sem execucao; aplicar antes de utilizar o codigo novo.

Status: proposta preparada, sem execucao de SQL e sem alteracao de PHP/JavaScript.
Tipo = Fixa/Variavel e uma classificacao PROVISORIA e depende da validacao da cliente.
Categoria representa a finalidade da despesa e permanece cadastrada dinamicamente.

## Evidencias e limite da analise

A estrutura analisada e a versionada em app/model/backup.sql; a estrutura real
do banco nao foi consultada. O codigo conecta ao banco almapantaneira em localhost.
Confirmar instancia e schema antes de qualquer aplicacao futura.

O SQL tem categoria_despesa(id_categoria_despesa, descricao) e uma FK
despesa.id_categoria_despesa -> categoria_despesa.id_categoria_despesa.
As categorias iniciais sao Fixa e Variavel. Nao ha tipo_despesa no SQL/codigo.
Fornecedor e projeto permanecem independentes e opcionais no fluxo atual.

O cadastro usa categorias dinamicas, modal de nova categoria e botoes para
cadastrar fornecedor/projeto. A edicao ainda fixa categorias nos radios 1 e 2.
As mensagens de validacao tambem confundem categoria com Fixa/Variavel.
O modal salva uma categoria, mas nao atualiza a lista do formulario apos sucesso.

## Modelo e preservacao

tipo_despesa 1:N despesa, por uma FK nova e inicialmente anulavel.
categoria_despesa 1:N despesa permanece sem alteracao. Tipo e categoria sao
independentes: nao criar FK de categoria para tipo sem novo requisito.

O script separado cria somente a tabela de tipos, suas duas opcoes provisorias e
a coluna/indice/FK em despesa. Nao modifica registros ou vinculos anteriores.
As despesas existentes ficam com tipo NULL (nao classificado), sem valor padrao
que possa inventar uma classificacao. Categorias Fixa/Variavel permanecem intactas.
Uma eventual reclassificacao sera outra etapa, validada com a cliente, com mapa
por id_despesa e auditoria; nao deduzir tipo apenas por IDs de categoria.

## Adaptacao futura dos arquivos

- app/controller/Despesa.php: propriedade de tipo, gravacao e consultas com LEFT
  JOIN, opcoes e filtros separados; preservar despesas com tipo NULL.
- app/controller/TipoDespesa.php (novo): listar tipos do banco sem IDs fixos.
  Cadastro livre de tipos nao esta previsto pela definicao provisoria.
- app/actions/cadastro-despesa.php e app/actions/editar-despesa.php: receber e
  validar tipo separadamente, verificar sua existencia; definir obrigatoriedade
  com a cliente e preservar NULL legado e campos ausentes durante a transicao.
- app/view/pages/cadastro-despesa.php e app/view/pages/edicao-despesa.php: dois
  campos independentes carregados do banco; substituir radios de categoria por
  select dinamico; permitir representar categoria legada e tipo nao classificado.
  Preservar modal de categoria, upload, autocomplete e botoes de fornecedor/projeto.
- app/view/JS/cadastro-despesa.js e app/view/JS/editar-despesa.js: preencher e
  validar ambos os campos; corrigir mensagens; manter opcionais da ER-01.
- app/actions/editar-despesa.php: GET deve fornecer opcoes de tipos e categorias
  para preencher a edicao, alem das opcoes de fornecedor e projeto existentes.
- app/view/JS/modal-categoria-despesa.js e app/actions/cadastro-categoria-despesa.php:
  retornar ID/descricao da categoria criada e inclui-la/seleciona-la sem recarregar
  a pagina ou perder campos preenchidos; permitir reaproveitamento na edicao.
- app/controller/CategoriaDespesa.php: manter cadastro/listagem; revisar retorno
  do ID novo se necessario para integrar o modal.
- app/actions/listagem-despesa.php, app/view/pages/listagem-despesa.php e
  app/view/JS/listagem-despesa.js: exibir/filtrar tipo e categoria separadamente.
- app/model/Database.php (select_despesa_categoria), app/view/pages/conciliacao-despesas.php
  e app/view/JS/conciliacao-despesa.js: revisar consulta e filtros afetados, evitando
  perder despesas legadas por INNER JOIN ou interpretar categoria como tipo.
- app/model/backup.sql: alinhar futuramente o esquema para instalacoes novas;
  nao usar o dump como migracao, pois ele contem DROP TABLE e dados iniciais.

## Verificacao e incompatibilidades

Antes: backup completo verificavel, exportacao dos vinculos de categoria por
id_despesa, contagem de registros e homologacao em copia do banco. Consultas
somente de leitura para confirmar estrutura real:

```sql
SELECT DATABASE(), @@hostname, @@port, VERSION();
SHOW CREATE TABLE almapantaneira.despesa;
SHOW CREATE TABLE almapantaneira.categoria_despesa;
SHOW TABLES FROM almapantaneira LIKE 'tipo_despesa';
SHOW TRIGGERS FROM almapantaneira LIKE 'despesa';
SELECT COUNT(*) FROM almapantaneira.despesa;
SELECT id_despesa, id_categoria_despesa FROM almapantaneira.despesa ORDER BY id_despesa;
```

O script pressupoe InnoDB e objetos novos ainda inexistentes; verificar conflitos
de tabela, coluna, indice e constraint. Nao usa IF NOT EXISTS para mascarar
estruturas divergentes. Configurar o cliente para parar no primeiro erro.
Usar conexao UTF-8 para preservar Variavel com acento no SQL. DDL pode bloquear
a tabela e causar commits implicitos; nao ha atomicidade garantida do conjunto.
Se houver falha parcial, inspecionar os objetos antes de retomar, sem reexecutar
o arquivo inteiro. Nao desabilitar verificacao de chaves estrangeiras.

Depois: usar as consultas comentadas no SQL, comparar contagens e todos os
vinculos de categoria com a exportacao anterior. Verificar que nao ha tipos
orfaos e que despesas antigas possuem tipo NULL. Apos adaptar a aplicacao, testar
cadastro, edicao de registros legados/dinamicos, modal, filtros, conciliacao,
upload e ausencia de fornecedor/projeto em ambiente de homologacao.

## Reversao

Preferir retorno ao codigo anterior mantendo tabela e coluna adicionais: o codigo
atual explicita as colunas gravadas e tolera o novo campo anulavel.
Para remocao fisica, usar apenas os comandos comentados de reversao apos backup
e exportacao dos tipos e do mapeamento por despesa. Essa remocao descarta o novo
atributo; nao e reversao sem perda se houver classificacoes novas sem exportacao.
Nunca restaurar o dump antigo por cima do banco ativo. ROLLBACK de transacao nao
substitui esse procedimento. Reversao parcial exige conferir quais DDL concluiram.
