# Correção pontual — Número do endereço

## Problema e análise

Em 02/11/2026, a tabela real `almapantaneira.endereco`, no MariaDB 10.4.32, possuía `numero TINYINT(4) NOT NULL`, incompatível com números superiores a 127 e com “S/N”. Existia **1 registro**. A coluna não participa de índices ou chaves estrangeiras. Os vínculos de doadores e fornecedores utilizam `id_endereco` e foram preservados. Não foram encontrados triggers na tabela nem views do banco referenciando endereço.

A busca por `numero` em PHP e JavaScript identificou: persistência em `Endereco.php`; validação em `RegraEnderecoDoador.php`; actions de cadastro/edição de fornecedores e doadores; carregamento em `editar-doador.js` e `editar-fornecedor.js`; mapeamento em `consulta-cnpj.js`; e os quatro formulários de endereço. Actions e carregamento já preservavam texto, sem conversão numérica. As ocorrências em cálculos monetários e telefone não correspondem ao número do endereço e não foram alteradas.

## Solução e arquivos modificados

- `docs/migrations/20261102-endereco-numero-varchar.sql`: `ALTER TABLE endereco MODIFY COLUMN numero VARCHAR(20) NOT NULL`.
- `app/controller/Endereco.php`: validação compartilhada de texto não vazio, UTF-8 válido e até 20 caracteres antes de cadastrar/editar; preserva o valor, sem conversão numérica.
- `app/controller/RegraEnderecoDoador.php`: substitui a restrição numérica de 127 pela validação textual compartilhada.
- `app/view/JS/consulta-cnpj.js`: aceita números textuais de até 20 caracteres retornados pela API, mantendo a proteção de edição manual.
- `app/view/pages/cadastro-doador.php`, `edicao-doador.php`, `cadastro-fornecedor.php` e `edicao-fornecedor.php`: `maxlength="20"`, preservando `id`, `name`, obrigatoriedade e funcionamento.
- `tests/requisito6-endereco.php`, `tests/requisito7-cnpj.cjs` e `tests/doacao-integracao-isolada.cjs`: cenários atualizados para `25`, `1500`, `S/N` e rejeição acima de 20 caracteres.
- `tests/endereco-numero-mariadb.php`: backup, restauração isolada, teste da DDL e aplicação controlada com `--apply`. É uma ferramenta pontual, que recusa aplicar novamente após a mudança da estrutura.

## Backup, execução e preservação

**Migração executada com sucesso**, após autorização expressa do usuário. Backup integral da estrutura e dos dados, fora da pasta pública:

`C:\Users\jvdon\AppData\Local\AlmaPantaneira\backups\endereco-20261102-191930-d83b3367\endereco.sql`

SHA-256: `0494da162d97ec220b008cfc29eea35f16bc3cbf19afc5557a5bc7753ea62161`.

O arquivo foi relido, seu checksum conferido e seu conteúdo restaurado em tabela `TEMPORARY` da própria conexão MariaDB. Estrutura, índices e dados foram reproduzidos; todos os campos e identificadores foram comparados antes da migração de teste. Os testes de gravação ocorreram somente nessa tabela temporária e no SQLite isolado.

Antes da DDL real, a ferramenta obteve `LOCK TABLES endereco WRITE` e confirmou que estrutura e todos os dados continuavam iguais ao backup. Após a DDL, comparou novamente todos os campos, identificadores e quantidade de registros, além do tipo e nulabilidade da coluna. Resultado: **1 registro antes e depois, todos os dados preservados; coluna `VARCHAR(20) NOT NULL`**. Não houve INSERT, UPDATE ou DELETE em registros reais nem mudanças em outras tabelas.

A criação do diretório privado foi inicialmente bloqueada pelo sandbox; a operação parou antes da DDL. Com a permissão do ambiente, o backup foi criado e toda a verificação repetida antes da execução efetiva. A DDL possui commit implícito no MariaDB; não foi tratada como reversível por rollback de transação.

## Testes e resultados

- MariaDB real, tabela temporária: restauração integral do backup, migração, preservação dos dados anteriores e INSERT/SELECT parametrizado via PDO de `25`, `1500` e `S/N`: aprovados, sem truncamento.
- PHP: aceitação dos três valores, rejeição acima de 20 caracteres e regressão da regra de endereço opcional/limite de R$ 1.000,00: aprovados.
- JavaScript com mocks da API: os três números são mantidos; valores acima do limite não são preenchidos; falhas da API e preservação manual continuam aprovadas.
- Integração HTTP/PDO e navegador: **37 verificações aprovadas**, com cadastro/edição de doador, gravação e leitura exata dos três valores na edição, projeto opcional, despesas e modais preservados. SQLite e Chrome isolados; API simulada.
- Evidências: `C:\Users\jvdon\AppData\Local\Temp\alma-integracao-UXHP21\resultados.json`; `verificacao.json` junto ao backup real.
- Sete scripts anteriores de regressão aprovados: `doacao-projeto-opcional.php`, `doacao-insercao-pdo.php`, `er02-sem-banco.php`, `doacao-doador-obrigatorio.cjs`, `doacao-projeto-opcional.cjs`, `er02-formulario.cjs` e `er03-terminologias.cjs`.
- Sintaxe dos arquivos PHP e JavaScript alterados e verificação de whitespace aprovadas. Não houve instalação nem commit.

## Verificação e reversão segura

Para conferir o resultado, executar somente leitura: `SHOW COLUMNS FROM almapantaneira.endereco LIKE 'numero';`. O resultado esperado é `varchar(20)`, `Null = NO`. A execução já foi concluída; não é necessário reaplicar a migração.

Conservar o backup e o checksum fora do diretório público. Para confirmar a restauração posteriormente, usar banco isolado e importar o arquivo nesse destino; nunca importar sobre a tabela real existente. O arquivo não contém DROP TABLE nem exclusão de registros.

Não reduzir a coluna automaticamente para TINYINT. Depois de novos cadastros, `1500`, “S/N”, zeros à esquerda ou outras representações podem não ser reversíveis sem perda. Uma reversão exige pausa das gravações, novo backup validado e verificação de todos os valores e representações atuais; somente se todos forem inteiros canônicos entre -128 e 127 seria possível considerar a redução. Restaurar o backup antigo sobre dados posteriores descartaria mudanças e não está autorizado. Na presença de valores incompatíveis, manter `VARCHAR(20)`.

## Resultado final

Correção concluída e compatibilidade com MariaDB verificada. A limitação de `numero` registrada nos relatórios dos requisitos 6 e 7 foi resolvida por esta entrega, sem alterar esses documentos históricos. Nenhuma pendência específica desta correção; suporte a CNPJ alfanumérico permanece fora do escopo.
