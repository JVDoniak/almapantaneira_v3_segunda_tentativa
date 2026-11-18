# ER-02 — Separação entre Tipo e Categoria de Despesa

## Correção posterior — categoria na listagem

Problema informado: Tipo aparece, mas Categoria mostra "-". A investigação
consultou o endpoint real por GET, sem alterar o banco: o JSON das três despesas
retornou nome_categoria preenchido. A consulta contém o LEFT JOIN correto e
c.descricao AS nome_categoria. O JavaScript atual gera oito células na ordem
Nome, Tipo, Categoria, Projeto, Forma de Pagamento, Vencimento, Valor e Ações.

Causa mais provável: JavaScript antigo em cache, incompatível com o cabeçalho
atual. Antes da ER-02 eram sete células: a categoria ocupava a posição de Tipo
e o projeto vazio ("-") ocupava a posição de Categoria. O cache do navegador
do usuário não foi inspecionado diretamente; essa causa é compatível com o
sintoma e com a ausência de divergência no código/JSON atuais.

Correção mínima: app/view/pages/listagem-despesa.php passa a carregar
listagem-despesa.js com parâmetro v calculado por filemtime do arquivo. Mudanças
no script passam a gerar nova URL, evitando reutilização de uma versão anterior.
Não houve alteração na consulta, no contrato JSON, nos dados ou em outros fluxos.

Validação: PHP -l na página, node --check no JS e git diff --check passaram.
Um teste temporário em memória usou o JSON real e o JavaScript atual em DOM
simulado: as três linhas geraram oito células, com tipo, categoria e projeto
nas posições corretas. O GET da URL versionada retornou o script atual.
Somente leituras/GET foram executados. Pendente conferir a página no navegador
do usuário após recarregar e confirmar a URL versionada na aba Network.

Projeto: Alma Pantaneira V2. Data: 31/10/2026.

Implementação em arquivos concluída; migração preparada e NÃO executada.
O funcionamento integrado depende da aplicação da migração e da homologação.
Tipo = Fixa ou Variável é uma definição PROVISÓRIA, dependente da validação da cliente.
Categoria = finalidade do gasto, cadastrada dinamicamente.

## Diagnóstico e resultado

O SQL versionado usava categoria_despesa tanto para Fixa/Variável quanto para
finalidades cadastradas pelo modal. O cadastro já tinha select dinâmico, enquanto
a edição usava rádios fixos de categoria com IDs 1 e 2.

Agora despesa possui dois vínculos independentes: id_tipo_despesa para
tipo_despesa e id_categoria_despesa para categoria_despesa. O tipo vem do banco,
sem IDs fixos na interface. Não foi criado cadastro livre de tipos porque as
opções provisórias são Fixa/Variável. O tipo aceita “Não classificado”, inclusive
no cadastro, enquanto a obrigatoriedade não tiver sido validada pela cliente.
Categoria continua obrigatória no cadastro; na edição, NULL legado pode ser
mantido, mas uma categoria já informada não pode ser apagada por campo vazio.

Nenhuma despesa foi reclassificada. Categorias antigas, inclusive Fixa/Variável,
continuam disponíveis com seus IDs e vínculos. Sua organização semântica futura
exige revisão explícita da cliente; não é feita por esta migração.

## Inventário das alterações desta implementação

| Arquivo | Alteração |
| --- | --- |
| docs/migrations/20261031_separar_tipo_despesa.sql | Migração separada: tabela de tipos, opções provisórias, coluna anulável, índice e FK; verificação e reversão comentadas. |
| docs/migrations/20261031_separar_tipo_despesa.md | Planejamento e orientações; referência para o relatório final. |
| app/model/backup.sql | Esquema de instalação nova alinhado à ER-02; seeds de despesas mantêm tipo NULL e categoria original. Não aplicar esse dump em banco existente. |
| app/controller/TipoDespesa.php (novo) | Listagem de tipos e consulta parametrizada de existência. |
| app/controller/Despesa.php | Propriedade e persistência de tipo; listagem com LEFT JOIN; filtro independente de tipo; validação de existência de categoria. |
| app/actions/cadastro-despesa.php | Recepção e validação de tipo; validação de categoria existente; preservação dos opcionais da ER-01. |
| app/actions/editar-despesa.php | Validação separada; manutenção de NULL legado; preservação de tipo/categoria quando ausentes no POST; verificação da existência da despesa. |
| app/view/pages/cadastro-despesa.php | Campo de tipo independente, categorias dinâmicas com escape HTML, recursos existentes mantidos. |
| app/view/pages/edicao-despesa.php | Dois selects dinâmicos, modal de categoria reaproveitado, salvamento bloqueado até carregar dados. |
| app/view/JS/cadastro-despesa.js | Mensagem de categoria corrigida, sem confundir finalidade com Fixa/Variável. |
| app/view/JS/editar-despesa.js | Preenchimento independente, suporte a NULL legado e nova categoria; liberação do salvamento após carregar dados. |
| app/controller/CategoriaDespesa.php | Retorno do ID da categoria criada, obtido na mesma conexão após inserção bem-sucedida. |
| app/actions/cadastro-categoria-despesa.php | Resposta com ID e descrição; validação de descrição com limite de 100 caracteres. |
| app/view/JS/modal-categoria-despesa.js | Inclui e seleciona a categoria recém-criada sem recarregar nem perder o restante do formulário; reset por referência explícita. |
| app/actions/listagem-despesa.php | Opções e filtro de tipo separados dos de categoria. |
| app/view/pages/listagem-despesa.php | Filtro e coluna de tipo. |
| app/view/JS/listagem-despesa.js | Exibição do tipo, “Não classificado” para NULL, opções de filtro e colspan atualizado. |
| app/model/Database.php | Consulta select_despesa_categoria inclui tipo por LEFT JOIN; API genérica de PDO preservada. |
| app/view/pages/conciliacao-despesas.php | Coluna de tipo e campo de tipo no modal existente. |
| app/view/JS/conciliacao-despesa.js | Apresentação separada de tipo e categoria, NULL sem inferência, colspan atualizado. |
| tests/er02-sem-banco.php (novo) | Testes isolados de controllers com Database simulado. |
| tests/er02-formulario.cjs (novo) | Teste de validação do cadastro com DOM simulado, sem rede. |
| docs/ER-02-relatorio-SENAC.md (novo) | Registro de alterações, testes, aplicação e reversão. |

Não foram alterados arquivos de doações nesta etapa. Alterações pré-existentes
de doações e camera-documento.js no diretório de trabalho foram preservadas.
Fornecedor/projeto opcionais, autocomplete, upload, filtros, status e botões de
cadastro de fornecedor/projeto existentes na V2 foram mantidos.

## Impactos e limitações

- A estrutura real do banco não foi consultada; a análise se baseia no SQL
  versionado. Confirmar nomes, engine, colunas anuláveis e restrições reais.
- O código novo requer a migração antes do uso. Sem tipo_despesa/coluna nova,
  consultas e formulários podem falhar. Coordenar implantação em manutenção.
- A edição antiga não representava categorias dinâmicas; foi corrigida para
  carregar todas, sem converter automaticamente categorias antigas em tipos.
- A listagem preserva despesas sem tipo através de LEFT JOIN. Filtro vazio
  apresenta todos; uma opção selecionada apresenta somente aquele tipo.
- A conciliação de despesas continua sendo protótipo com dados fictícios e
  confirmação local no navegador. Foi adaptada apenas a apresentação relacionada
  à ER-02. Não foi criada integração com pagamentos nem inferido tipo dos mocks.
- O SQL original já permite NULL em fornecedor/projeto. Se a tabela real divergir,
  a migração de tipo não corrige essa divergência: verificá-la antes da homologação.
- O modal exige a extensão PHP mbstring para validar o comprimento da descrição.
- O dump de instalação nova continua contendo comandos destrutivos preexistentes;
  ele não deve ser utilizado como atualização do banco existente.

## Validações realizadas sem banco

- PHP: php -l em todos os PHP alterados e no novo controller/teste.
- JavaScript: node --check nos JS alterados e no novo teste.
- git diff --check para detectar problemas de whitespace.
- php tests/er02-sem-banco.php: cadastro com tipo/categoria distintos, NULL em
  fornecedor/projeto, edição mantendo NULL de tipo e categoria dinâmica, parâmetros
  separados nos filtros, LEFT JOIN e rejeição de tipo/categoria inexistentes.
  O teste usa uma implementação simulada de Database e não abre conexão PDO.
- node tests/er02-formulario.cjs: aceita categoria dinâmica com opcionais vazios;
  rejeita fornecedor/projeto digitados sem vínculo e cadastro sem categoria.

Não foram executados SQL, testes de integração com banco, requisições reais de
cadastro ou testes manuais no navegador. A sintaxe SQL foi revisada por leitura,
sem execução ou validação pelo servidor MySQL/MariaDB.

## Como aplicar futuramente

1. Validar Fixa/Variável e a obrigatoriedade com a cliente. Fazer backup verificável
   do banco e preservar uma cópia dos arquivos da aplicação anteriores à ER-02.
2. Homologar em cópia do banco. Usar as consultas de leitura do documento de
   migração para confirmar servidor/schema, engine InnoDB, estrutura, triggers e
   ausência da tabela/coluna/índice/constraint novos. Confirmar NULL dos opcionais.
3. Registrar contagem de despesas e exportar id_despesa/id_categoria_despesa.
   Paralisar escritas da aplicação durante aplicação e conferência.
4. No cliente SQL, selecionar explicitamente o banco correto e usar conexão UTF-8.
   Executar SOMENTE docs/migrations/20261031_separar_tipo_despesa.sql, uma vez,
   parando no primeiro erro. Nunca usar app/model/backup.sql para atualização.
5. Conferir as consultas comentadas de verificação: estrutura, tipos, ausência de
   órfãos e contagens. Imediatamente após a migração, todas as despesas anteriores
   devem ter tipo NULL e exatamente os mesmos vínculos de categoria.
6. Disponibilizar o código novo, executar os testes funcionais abaixo e liberar
   escritas após conferir o resultado. Uma execução parcial exige inspeção dos
   objetos existentes antes de retomar; não reexecutar o arquivo ignorando erros.

DDL MySQL/MariaDB pode causar commit implícito e bloqueios. Não contar com
transação/ROLLBACK para desfazer o conjunto do script.

## Testes pendentes em homologação

1. Cadastrar uma despesa de cada tipo com categoria dinâmica; verificar os dois
   vínculos separadamente. Testar também “Não classificado”.
2. Editar despesas antigas das categorias 1/2 e de outras categorias, com tipo
   NULL e categoria NULL legada; salvar sem alteração e comparar vínculos.
3. Atribuir tipo explicitamente e editar de novo; confirmar persistência. Conferir
   que a ausência do campo no POST não apaga a classificação anterior.
4. Cadastrar categoria pelo modal nas duas telas; verificar seleção imediata e
   manutenção dos campos já preenchidos. Testar descrição vazia, longa e acentuada.
5. Testar fornecedor/projeto vazios, selecionados e texto sem seleção; anexar
   comprovante e confirmar que o upload/autocomplete continuam funcionando.
6. Filtrar listagem por tipo, categoria e ambos; limpar filtros, conferir despesas
   sem tipo, totais de linhas, filtros existentes e alteração de status.
7. Conferir tipo/categoria na tabela e modal da conciliação protótipo, mantendo
   seu fluxo local atual. Não interpretar os mocks como dados reais do banco.
8. Interromper o GET da edição e confirmar que Salvar continua bloqueado.
9. Enviar IDs de tipo/categoria inválidos ou inexistentes; confirmar rejeição no
   servidor. Conferir acentos das opções e ausência de erros no console.

## Reversão segura

Preferir reversão lógica: voltar aos arquivos anteriores à ER-02 e manter a nova
tabela/coluna. O código anterior explicita os campos e pode ignorar o atributo
novo. Não fazer restauração indiscriminada de arquivos com outras alterações.

Para reversão física, parar escritas, exportar tipo_despesa e o mapeamento
id_despesa/id_tipo_despesa, fazer backup completo e conferir dependências.
Os comandos estão COMENTADOS no final da migração: remover FK, índice, coluna e
tabela, nessa ordem, somente após revisão. A remoção descarta os tipos atribuídos
depois da migração; sem exportação, essa informação se perde. Despesas e categorias
originais não precisam ser removidas. Conferir contagens e vínculos após reverter.
