# Doação com projeto opcional — relatório técnico SENAC

**Atualização mais recente:** a validação HTTP/PDO e no Chrome com gravações
fictícias em SQLite isolado está na seção 9. A causa do erro manual e a correção
estão na seção 8. As seções anteriores preservam o histórico de cada etapa;
limitações de testes então registradas não devem ser confundidas com os testes
adicionais já executados na seção 9.

Projeto Integrador Alma Pantaneira V2 — Técnico em Desenvolvimento de Sistemas.  
Data: 02/11/2026. Implementação complementar: solicitação da cliente de permitir
doações sem projeto. Não corresponde a uma renomeação contábil da ER-03.

## 1. Problema e causa técnica

O cadastro exigia projeto no HTML (`required` e indicação visual de campo
obrigatório) e na action (`empty($idProjeto)`). A action convertia o valor para
inteiro, fazendo uma seleção vazia virar zero em vez de NULL. O controller
declarava `public int $id_projeto`, impedindo representar ausência de vínculo.

A edição não tinha `required`, mas não oferecia uma opção vazia selecionável e
encaminhava string vazia ao campo inteiro. O JavaScript preenchia o select com
`projeto_nome`, embora as opções tivessem IDs como valores. Isso também podia
deixar uma doação vinculada sem a seleção correta ao abrir a edição.

As consultas de listagem e de busca para edição faziam INNER JOIN com projeto;
consequentemente, uma doação sem vínculo não era retornada. A listagem não tinha
texto alternativo para projeto ausente.

## 2. Estrutura real do banco e necessidade de migração

Foi realizada **somente leitura** usando a conexão configurada pela aplicação:

```sql
SELECT DATABASE() AS banco, VERSION() AS versao;
SHOW CREATE TABLE doacao;
```

Resultado local: banco `almapantaneira`, MariaDB `10.4.32-MariaDB`, tabela InnoDB,
com a definição:

```sql
id_projeto int(11) DEFAULT NULL
```

A FK `doacao_ibfk_2` referencia `projeto(id_projeto)` e aceita NULL, pois a coluna
é anulável. O SQL versionado em `app/model/backup.sql` também já declara a coluna
com `DEFAULT NULL`.

**Neste ambiente não é necessário executar script SQL.** Não foi criada uma
migração redundante e não houve alteração de esquema, dados, FK ou scripts antigos.

Em outra instalação, confirmar servidor/schema pela conexão real e executar
`SHOW CREATE TABLE doacao` ou `SHOW FULL COLUMNS FROM doacao` por leitura antes
da implantação. Se o resultado divergir e indicar NOT NULL, preparar uma migração
própria versionada em `docs/migrations/`, preservando tipo/sinal, índices e FK
daquela instalação. Sua execução exigirá autorização, backup e homologação.
Não executar o dump `backup.sql` em banco existente: ele contém comandos de
recriação e dados iniciais e não é uma atualização segura.

## 3. Arquivos modificados e implementação

| Arquivo | Alteração |
| --- | --- |
| `app/view/pages/cadastro-doacao.php` | Remove `required` e a classe de obrigatoriedade apenas de Projeto; apresenta `Projeto (opcional)` e opção selecionável `Sem projeto`, com valor vazio. |
| `app/view/pages/edicao-doacao.php` | Mesmo rótulo/opção vazia; mantém `name="id_projeto"`; inclui versão por `filemtime` no script de edição para evitar JavaScript antigo em cache. |
| `app/actions/cadastro-doacao.php` | Projeto ausente/vazio vira NULL; ID preenchido precisa ser inteiro positivo; elimina projeto da condição de obrigatoriedade, preservando verificações de valor e forma de recebimento; mensagem correspondente deixa de exigir projeto. Include por `__DIR__` para resolução independente do diretório de execução. |
| `app/actions/editar-doacao.php` | Normaliza projeto como no cadastro; permite remover/atribuir vínculo; busca registro anterior e preserva seu valor quando o campo não integra o POST; comunica retorno falso da persistência como erro. Include por `__DIR__`. |
| `app/controller/Doacao.php` | Propriedade `id_projeto` passa a `?int`, com padrão NULL. Insert/update continuam transmitindo o atributo sem conversão para zero. |
| `app/model/Database.php` | LEFT JOIN de projeto na listagem. Busca de edição parte de doação, mantém LEFT JOIN de projeto, inclui `doacao.id_projeto` e usa parâmetro para ID da doação. Falha dessa busca retorna false, evitando interpretar uma mensagem de erro como registro encontrado. |
| `app/view/JS/editar-doacao.js` | Preenche o select com `id_projeto ?? ""`. Redireciona apenas se o JSON indicar sucesso e a resposta HTTP for válida; mantém a tela em erro de persistência. |
| `app/view/JS/listagem-doacao.js` | Mostra `Sem projeto` quando o nome do projeto é nulo ou vazio. |
| `app/view/pages/listagem-doacao.php` | Versiona o script da listagem por `filemtime` para disponibilizar a apresentação atual sem cache antigo. |
| `app/view/JS/conciliacao-doacao.js` | No protótipo, tabela e modal exibem `Sem projeto` para ausência; filtro deixa de gerar uma opção nula/vazia para um nome inexistente. Nenhuma integração de pagamentos/recebimentos criada. |
| `tests/doacao-projeto-opcional.php` | Testes de caixa branca das actions/controller com Database simulado e captura das consultas reais sem execução de SQL. |
| `tests/doacao-projeto-opcional.cjs` | Testes com DOM/fetch simulados da edição, listagem, campos e obrigatoriedades. |
| `docs/ER-doacao-projeto-opcional-relatorio-SENAC.md` | Este relatório, criado sem alterar os relatórios anteriores. |

São 10 arquivos existentes de aplicação alterados, dois novos testes e este
documento. As alterações pré-existentes de ER-01, ER-02 e ER-03 foram mantidas.

### Preservação do valor durante edição

O campo Valor da edição já era `disabled` e, portanto, não é incluído em FormData.
A action antes usava string vazia quando ele não vinha no POST. A busca do valor
original foi necessária para remover/vincular projeto sem apagar o valor da
doação. Não foi habilitada a edição desse campo nem alterada sua regra de negócio.

### NULL até o PDO

A ausência/vazio do parâmetro gera PHP NULL, não zero nem texto `"NULL"`. A
propriedade nullable preserva esse valor. Os métodos insert/update existentes
usam placeholders e `array_values()`, que mantêm NULL, e o encaminham ao PDO
com `execute($params)`. A API genérica de binding não foi alterada.
Projeto preenchido passa por FILTER_VALIDATE_INT com mínimo 1; texto inválido,
zero, negativo ou array é rejeitado antes de gravar. A FK mantém a integridade
referencial para IDs positivos inexistentes; o fluxo agora comunica a falha de
persistência na edição em vez de reportar sucesso indevido.

## 4. Arquivos examinados que não precisaram de alteração

- `app/view/JS/cadastro-doacao.js`: usa FormData do formulário; não impõe condição
  própria para projeto. Ficou integralmente preservado, inclusive correções da ER-03.
- `app/actions/listagem-doacao.php`: já encaminha o resultado do controller em JSON;
  o ajuste necessário estava no JOIN e na renderização.
- `app/actions/relatorio-projeto.php`, `app/controller/Projeto.php`,
  `app/view/pages/relatorio-projeto.php` e `app/view/JS/relatorio-projeto.js`:
  relatório **por projeto** continua agregando somente doações vinculadas. Sua
  consulta INNER JOIN é intencional nesse escopo; doações sem projeto não são
  atribuídas nem somadas a um projeto artificial. A consulta foi testada por leitura.
- `app/view/pages/visualizacao-unica-doador.php`: conteúdo estático, sem consulta
  dinâmica de projeto para adaptar; nenhuma integração nova foi criada.
- Dashboard e conciliação continuam usando seus mocks existentes; não foram
  conectados ao banco nem tiveram critérios financeiros alterados.

O cadastro/edição com projeto existente continua preservando o ID informado.
Na edição, a opção vazia agora permite remover explicitamente o vínculo.
Os `required` dos outros campos permaneceram exatamente iguais aos anteriores;
o backend continua validando os campos que já validava. Não foi ampliada a
validação de doador/tipo ou feita revisão de regras não relacionadas.

## 5. Testes de caixa branca e resultados

| Teste | Evidência / resultado |
| --- | --- |
| `C:/xampp/php/php.exe tests/doacao-projeto-opcional.php` | Aprovado: 20 cenários de actions e duas consultas capturadas, sem banco. |
| Cadastro com projeto | Recebe ID 7, encaminha inteiro 7 ao controller/persistência simulada e retorna sucesso. |
| Cadastro sem projeto | Com campo vazio e com campo ausente: sucesso, ID NULL e valor mantido. |
| Edição com projeto | Preserva projeto 7; também vincula uma doação originalmente sem projeto. |
| Edição sem projeto | Campo vazio ou ausente gera NULL, inclusive ao retirar vínculo anterior. Valor omitido pelo formulário permanece `75.00` na fixture. |
| Projeto inválido | Cadastro e edição rejeitam texto, zero, negativo e array sem chamar insert/update simulado. |
| Demais validações do cadastro | Ausência de valor ou forma de recebimento continua rejeitada, sem gravação. |
| Falha na persistência | Cadastro retorna success false; edição retorna status 400. GET de edição de fixture sem projeto retorna registro com ID NULL. |
| Consultas capturadas | Métodos reais de Database chamados em subclasse sem construtor PDO: LEFT JOIN de projeto confirmado, busca por ID parametrizada, ID de projeto incluído e NULL preservado no retorno simulado. SQL não executado nesse teste. |
| `node tests/doacao-projeto-opcional.cjs` | Aprovado: edição por ID com projeto e NULL, retorno de erro sem redirecionar, duas linhas de listagem com/sem projeto, opção vazia selecionável e obrigatoriedades restantes preservadas. DOM/fetch simulados. |
| Sintaxe PHP | `php -l` nos sete PHP da aplicação alterados e no novo teste: nenhum erro. |
| Sintaxe JavaScript | `node --check` nos três JS alterados e no novo teste: nenhum erro. |
| Regressão das ER anteriores | `php tests/er02-sem-banco.php` e `node tests/er02-formulario.cjs`: aprovados, sem conexão real. |
| Auditoria de escopo | Comparação com snapshot do início desta tarefa: cadastro-doacao.js e documentos anteriores intactos. `required` de campos diferentes de projeto idênticos nos dois formulários. |
| Whitespace | `git diff --check`: aprovado, sem erros. |

### Leituras reais complementares, sem modificar dados

- Estrutura real confirmada por SELECT de metadados e SHOW CREATE TABLE.
- Listagem nova executada com o controller real: **1 doação retornada, vinculada
  a projeto**. Não há registro real sem projeto nessa base para validar esse caso.
- Busca real de edição: retornou `id_projeto` no registro consultado, sem erro SQL.
- Consulta do relatório por projeto: executada sem erro, somente SELECT.

Não foram feitos INSERT, UPDATE, DELETE, ALTER, migrações ou POSTs reais. Os
métodos de escrita do teste são mocks em subprocessos, sem conectar ao PDO.

## 6. Testes não realizados e pendências

- Não houve teste de gravação real, pois não há autorização para alterar dados.
  Homologar posteriormente cadastro/edição com projeto, sem projeto e remoção
  de vínculo em base de testes autorizada; confirmar NULL efetivo no banco.
- A listagem real não possui doação sem projeto; a renderização e a preservação
  dessas linhas foram verificadas com fixtures e captura do LEFT JOIN.
- Não houve teste manual no navegador, upload/foto real ou verificação visual
  em celular. Conferir também seleção inicial, mudança de projeto e opção vazia.
- Os scripts de upload, foto, tipo de doação e autocomplete de doadores não
  foram alterados. Seus fluxos reais precisam de regressão visual em homologação.
- O relatório por projeto não inclui doações sem vínculo por definição de seu
  agrupamento. Um resumo global de doações sem projeto é um requisito diferente,
  que deverá ser combinado caso a cliente o deseje.
- As questões contábeis/financeiras pendentes da ER-03 e Fixa/Variável provisório
  da ER-02 continuam pendentes; esta mudança não decide nenhuma delas.

## 7. Implantação e controle

Neste banco: publicar os arquivos, recarregar as páginas e executar a homologação
autorizada. **Nenhum script SQL precisa ser executado.** Antes de implantar em
outro ambiente, repetir a verificação somente de leitura da nulabilidade.

Não foram reclassificadas doações, removidos vínculos existentes automaticamente,
alterados relatórios anteriores ou realizados commits. A remoção de vínculo só
ocorre quando o cadastro/edição é enviado sem projeto, conforme o requisito.

## 8. Investigação da falha de inserção no teste manual

### 8.1. Falha reportada

No teste manual de 02/11/2026, uma doação Financeira, Sem projeto, no valor de
R$ 1.200,00, por Transferência Bancária e com data 02/11/2026, apresentou:
“Erro ao registrar a doação no banco de dados.” O doador parecia selecionado
na interface. Não foi feita nova tentativa de gravação com dados reais.

### 8.2. Evidência do erro real, anterior à correção

Foram consultados o esquema, configurações da sessão, IDs existentes sem nomes,
triggers e o diagnóstico somente de leitura:

```sql
SHOW ENGINE INNODB STATUS;
```

O bloco `LATEST FOREIGN KEY ERROR` registrava a operação às **12:15:04 de
02/11/2026**, mesmo horário do POST `/app/actions/cadastro-doacao.php` no log
de acesso do Apache. O trecho SQL registrado pelo motor foi:

```sql
INSERT INTO doacao
  (id_doador,tipo,id_projeto,descricao,valor,forma_pagamento,data_doacao)
VALUES
  ('0','1',NULL,'financeira','1200.00','Transferência Bancária','2026-11-02')
```

Em seguida, o diagnóstico do motor identificou:

```text
Foreign key constraint fails for table `almapantaneira`.`doacao`
CONSTRAINT `doacao_ibfk_1` FOREIGN KEY (`id_doador`)
REFERENCES `doador` (`id_doador`)
```

**Causa confirmada: o SQL tentava inserir `id_doador = 0`, inexistente na tabela
de doadores. Não era uma rejeição de `id_projeto = NULL`.** A consulta de IDs
mostrou apenas o doador 1 nessa base; não existe doador 0.
Os dados pessoais presentes no restante do diagnóstico do InnoDB foram omitidos
deste relatório. Não foi necessário reproduzir a falha gravando outro registro.

A action usava `intval($idDoador)` sem validar o ID ou sua existência. Campo
oculto vazio/ausente ou texto inválido poderia virar zero; a interface não
impedia o envio de nome preenchido sem um ID efetivamente selecionado. O corpo
original do POST não está no log de acesso, portanto não é possível determinar
qual interação específica deixou o ID inválido. A conclusão comprovada é o
valor zero recebido pela operação SQL e sua violação da FK, não uma suposição
sobre o clique realizado no navegador.

### 8.3. Outras hipóteses verificadas

| Item | Evidência |
| --- | --- |
| Projeto NULL | `SHOW CREATE TABLE doacao` mantém `id_projeto int(11) DEFAULT NULL`; o INSERT registrado contém NULL, não zero/string vazia. |
| ID do doador | Coluna `id_doador int(11) DEFAULT NULL` possui FK. Ser anulável não permite um número inexistente como zero. Neste cadastro o doador é obrigatório. |
| Valor | Coluna `decimal(10,2)`; operação registrada contém `1200.00`, dentro do tipo e precisão. |
| Forma de recebimento | `varchar(50) NOT NULL`; o texto registrado cabe nessa coluna. |
| Tipo | `varchar(150) NOT NULL`; select envia ID textual `1`, comportamento preexistente que não causou a FK. Não foi feita redefinição de tipos nesta correção. |
| Data | Coluna DATE; operação contém `2026-11-02`, formato válido. |
| Charset | Sessão e banco informaram utf8mb4. Teste SELECT com parâmetros preservou os acentos de Transferência Bancária. |
| Triggers | `SHOW TRIGGERS LIKE 'doacao'` retornou lista vazia. |
| Exceção oculta | `Database::execute()` capturava PDOException e devolvia false sem registrar a mensagem; a action só recebia o retorno falso. O erro original não estava disponível no log PHP, mas foi recuperado do diagnóstico do InnoDB. |

### 8.4. Correção aplicada e arquivos desta complementação

| Arquivo | Correção |
| --- | --- |
| `app/actions/cadastro-doacao.php` | Valida ID de doador como inteiro positivo e confirma sua existência antes de inserir. Retorna “Selecione um doador válido da lista.” para campo ausente, inválido ou inexistente, sem converter silenciosamente para zero. Projeto continua nullable e opcional. |
| `app/controller/Doacao.php` | Novo método `doadorExiste()` consulta o ID com placeholder. Nenhuma alteração no mapeamento de NULL do projeto. |
| `app/view/JS/cadastro-doacao.js` | Antes de montar/enviar FormData, exige ID positivo e nome preenchido; orienta selecionar doador da lista e mantém o foco no campo. Autocomplete, seleção por mouse/teclado e seleção por nome exato no blur foram preservados. |
| `app/view/pages/cadastro-doacao.php` | Nome do doador recebe required para refletir sua obrigatoriedade já indicada no rótulo; script do cadastro ganha versão por filemtime para evitar cache antigo. Projeto continua sem required e com opção Sem projeto. |
| `app/model/Database.php` | Catch de PDOException em execute registra SQLSTATE, código do driver e mensagem no log do servidor. Não imprime SQL/array de parâmetros no JSON; continua retornando false, preservando o contrato existente. O texto do driver pode conter detalhes da restrição e deve permanecer no log do servidor. |
| `tests/doacao-projeto-opcional.php` | Mocks adaptados à consulta de existência do doador e mais sete cenários para ID ausente, vazio, zero, negativo, texto, array ou inexistente. |
| `tests/doacao-insercao-pdo.php` | Novo teste dos métodos insert/execute reais com conexão simulada, inspecionando SQL/parâmetros e o caminho de PDOException. |
| `tests/doacao-doador-obrigatorio.cjs` | Novo teste do submit/autocomplete real em DOM simulado, cobrindo seleção, perda de ID ao redigitar e vínculo de projeto opcional. |
| `docs/ER-doacao-projeto-opcional-relatorio-SENAC.md` | Acrescenta evidência, correção, testes e limites desta investigação. |

O registro de erros foi acrescentado à camada que antes os ocultava. Não se
alterou o SQL genérico de inserção, nem houve mudança do banco ou de migrações.
Somente a verificação de existência usa SELECT; nenhum novo INSERT/UPDATE/DELETE
real foi executado. Valores, tipos, forma/data e demais regras não relacionadas
não foram redefinidos. Não foi realizado commit.

### 8.5. Testes realizados depois da correção

| Teste | Resultado |
| --- | --- |
| `php tests/doacao-projeto-opcional.php` | Aprovado: **27 cenários de actions**, incluindo cadastro/edição com e sem projeto, valor/formas obrigatórios, erro de persistência e sete cenários de doador inválido, mais duas consultas capturadas. Controllers reais com Database simulado, sem SQL executado. |
| `php tests/doacao-insercao-pdo.php` | Aprovado: insert gera os sete placeholders esperados; execute recebe ID de projeto inteiro ou PHP NULL e ID de doador positivo sem conversões. PDOException simulada retorna false sem saída que contamine o JSON; log temporário contém SQLSTATE 23000 e código 1452. Esses códigos são da simulação; a exceção original foi ocultada pelo código anterior. |
| PDO real, somente SELECT | Executado `SELECT ? IS NULL AS projeto_nulo, ? AS projeto_preenchido, ? AS valor, ? AS forma`, com `[null, 1, '1200.00', 'Transferência Bancária']`. Retornou projeto_nulo=1, projeto_preenchido=1, valor=1200.00 e forma acentuada correta. Confirma envio de NULL pelo mesmo execute, sem gravar dados. |
| `node tests/doacao-doador-obrigatorio.cjs` | Aprovado: sem ID/zero/negativo/texto não ocorre POST; autocomplete envia ID selecionado com e sem projeto; redigitar nome invalida ID e impede envio; blur com nome exato continua selecionando. |
| `node tests/doacao-projeto-opcional.cjs` | Aprovado: ID do projeto na edição, Sem projeto na listagem, tratamento de erro e obrigatoriedades restantes preservados. |
| Testes ER-02 | `php tests/er02-sem-banco.php` e `node tests/er02-formulario.cjs`: aprovados, preservando tipo/categoria e opcionais das despesas. |
| Sintaxe | `php -l` nos quatro PHP de aplicação alterados e dois testes PHP; `node --check` no JS alterado e novo teste: sem erros. |
| Whitespace | `git diff --check`: sem erros. |

Os testes de escrita usaram exclusivamente objetos simulados e dados fictícios.
O teste de log criou um arquivo temporário e restaurou a configuração anterior
do processo de teste; não alterou php.ini nem a configuração do servidor.

### 8.6. Resultado esperado e limites

Com um doador realmente selecionado e existente, cadastro com e sem projeto
continua encaminhando os mesmos dados, com projeto NULL no segundo caso. Se
somente o nome estiver visível sem um ID válido, o formulário orienta selecionar
o doador e o backend também bloqueia a operação, sem tentar gravar zero.

Ainda é necessário repetir o teste manual autorizado no navegador, verificando
o ID selecionado no FormData; não foi feita gravação real nesta investigação.
Outras falhas de SQL continuarão retornando erro genérico à interface, com
diagnóstico disponível no log do servidor. Em remoção concorrente de doador,
a FK permanece como proteção final e o erro fica registrado.

**Não há script SQL ou migração a executar.** A estrutura já aceita NULL no
projeto. A falha era o ID zero do doador e sua validação ausente, comprovados
pelo registro da operação que realmente falhou.

## 9. Validação técnica autônoma com integração e navegador

Data: 02/11/2026. Objetivo: concluir os cenários tecnicamente executáveis sem
instalar ferramentas, alterar registros reais ou executar migrações no banco
da aplicação. Esta seção complementa e não substitui o histórico anterior.

### 9.1. Ambiente identificado

- PHP local com PDO MySQL e PDO SQLite disponíveis.
- Node.js `v24.14.1`, com WebSocket nativo; Chrome instalado localmente.
- Playwright não estava disponível como dependência do projeto, pacote global
  ou cache nos locais verificados. Nenhuma ferramenta foi instalada.
- Utilizado Chrome headless via Chrome DevTools Protocol (CDP), com implementação
  de teste em Node e sem bibliotecas adicionais.
- O wrapper `npm.ps1` estava bloqueado pela política do PowerShell; a consulta
  dos pacotes foi feita com `npm.cmd`, sem alterar políticas ou instalar pacotes.

### 9.2. Isolamento e diferenças em relação ao ambiente real

Criado o teste reproduzível `tests/doacao-integracao-isolada.cjs`. A cada execução:

1. Cria uma pasta temporária própria e copia os fontes da aplicação. Não copia
   uploads nem dados reais. Os originais não são modificados.
2. Somente nessa cópia, troca o conector MySQL por PDO SQLite em arquivo novo,
   verifica que não restou a construção de conexão MySQL e habilita foreign_keys.
3. Provisiona tabelas mínimas e registros **fictícios**: doador 3, projeto 7 e tipo
   Financeira. Esse SQL é preparação de banco de testes novo, não migração nem
   atualização do banco da aplicação.
4. Inicia servidor PHP em loopback e porta efêmera. Usa páginas, JavaScripts,
   actions, controller e métodos insert/execute/listagem da cópia dos fontes reais.
   Não substitui o resultado de gravações por mocks nessa suíte.
5. Usa autenticação de fixture apenas na cópia temporária; login e autorização
   de produção não são objeto desse teste. Sessões PHP e perfil novo do Chrome
   também ficam na pasta temporária, separados das sessões do usuário.
6. Executa requests HTTP, ações no navegador e consultas ao arquivo SQLite para
   confirmar persistência, IDs e NULL. Ao terminar, encerra os processos criados.

**As gravações dessa suíte são reais no SQLite isolado, não simulações de insert.**
Isso não equivale a gravar no MariaDB de produção/homologação: dialeto, tipagem
DECIMAL/DATE e regras específicas do motor não são idênticos. O esquema mínimo
de teste representa o fluxo de doação e as FKs necessárias, não todo o banco.

### 9.3. Execução e resultados finais

Comandos reproduzíveis a partir da raiz da V2:

```text
node tests/doacao-integracao-isolada.cjs
node tests/doacao-integracao-isolada.cjs --browser
```

O primeiro executa somente HTTP/PDO. O segundo acrescenta Chrome headless.
`ALMA_TEST_PHP` e `ALMA_TEST_BROWSER` permitem indicar executáveis já instalados
em outros caminhos. Nenhum dos comandos instala ferramentas.

**Rodada final aprovada: 21 verificações, sendo 11 de HTTP/PDO e 10 de ambiente/
navegador, sem falhas de aplicação nos cenários cobertos.**

| Cenário | Camada e resultado |
| --- | --- |
| Cadastro com projeto vazio | HTTP → action → controller → Database → PDO SQLite. Inseriu e confirmou `id_projeto IS NULL`. |
| Cadastro com projeto 7 | Mesma cadeia, confirmou vínculo 7 e valor 1200. |
| Cadastro sem o parâmetro de projeto | Inseriu e confirmou NULL. |
| Doador zero | Backend rejeitou; contagem do SQLite não aumentou. |
| Doador positivo inexistente | Backend rejeitou; contagem não aumentou. |
| Valor obrigatório ausente/vazio | Backend rejeitou sem inserir. |
| Forma de recebimento obrigatória ausente/vazia | Backend rejeitou sem inserir. |
| Projeto positivo inexistente | FK real do SQLite rejeitou a inserção; JSON comunicou falha e log registrou SQLSTATE. Nenhuma linha adicional. |
| Listagem HTTP | JSON retornou todas as doações fictícias, com nome de projeto ou NULL conforme o vínculo. |
| Edição vinculando projeto | Update real no SQLite confirmou projeto 7 e preservou o valor omitido pelo campo disabled. |
| Edição removendo projeto | Update real no SQLite confirmou NULL e preservou o valor. |
| Chrome disponível | Iniciado headless, sem instalar dependências. |
| Cadastro no navegador sem projeto | Autocomplete real selecionou doador, formulário foi salvo por HTTP e SQLite confirmou NULL, doador 3 e valor 1200. |
| Cadastro no navegador com projeto | Mesma interação e persistência, com vínculo 7. |
| Nome digitado sem ID selecionado | Navegador mostrou alerta de seleção do doador; nenhuma nova linha. |
| ID inexistente submetido no navegador | ID oculto adulterado na automação para verificar defesa do servidor; backend rejeitou e nenhuma nova linha. |
| Tipo obrigatório | Validação nativa do navegador impediu envio com tipo vazio. |
| Forma de recebimento obrigatória | Validação nativa impediu envio com seleção vazia. |
| Data obrigatória | Validação nativa impediu envio com data vazia. |
| Listagem no navegador | Exibiu linhas com “Sem projeto” e “Projeto ficticio de integracao”, com quantidade igual à do SQLite. |
| JavaScript no navegador | Nenhuma exceção Runtime.exceptionThrown; captura de tela gerada para evidência. |

Os campos foram preenchidos com dados fictícios equivalentes ao caso reportado:
Financeira, 1200.00, Transferência Bancária e 2026-11-02. A seleção do doador usou
o evento de mouse no autocomplete real. A validação não chamou diretamente o
handler para simular um cadastro bem-sucedido.

Artefatos da rodada final:

```text
C:\Users\jvdon\AppData\Local\Temp\alma-integracao-WFyKii
  resultados.json
  php-server.log
  chrome.log
  cdp-events.json
  listagem.png
  ficticio.sqlite
```

São artefatos temporários e podem ser removidos pelo sistema. O roteiro versionável
e o registro de resultados neste relatório permitem repetir a validação.
O CDN de ícones foi bloqueado no navegador de teste para não depender de rede
externa; isso não é uma validação da disponibilidade desse serviço.

### 9.4. Testes automatizados anteriores reexecutados

| Comando | Resultado / tipo de banco |
| --- | --- |
| `php tests/doacao-projeto-opcional.php` | Aprovado: 27 cenários e duas consultas capturadas; mocks, sem gravação real. |
| `php tests/doacao-insercao-pdo.php` | Aprovado: montagem de insert, parâmetros NULL e registro de exceção; conexão simulada. |
| `node tests/doacao-doador-obrigatorio.cjs` | Aprovado: DOM/fetch simulados e fluxo de seleção. |
| `node tests/doacao-projeto-opcional.cjs` | Aprovado: DOM/fetch simulados, edição, listagem e obrigatoriedades. |
| `php tests/er02-sem-banco.php` | Aprovado: regressão de despesas com Database simulado. |
| `node tests/er02-formulario.cjs` | Aprovado: regressão de formulário com DOM simulado. |
| `node --check tests/doacao-integracao-isolada.cjs` | Aprovado; as páginas/actions reais também foram executadas pelo PHP na suíte isolada. |
| `git diff --check` | Aprovado. |

### 9.5. Leituras no MariaDB real e distinção de evidências

Foram feitas apenas consultas de leitura na conexão real:

- MariaDB `10.4.32-MariaDB`, schema `almapantaneira`.
- `id_projeto`: IS_NULLABLE=YES, COLUMN_TYPE=int(11).
- SELECT com parâmetros NULL, 1200.00 e Transferência Bancária: retorno correto,
  incluindo acentos. Valida binding/leitura PDO, não um INSERT nesse motor.
- Listagem real consultada sem erro: 1 linha. Contagem real registrada como
  1 doação e último ID 1. Nenhum teste realizou gravação nessa conexão.

Não houve execução de migrações. **Não há script SQL a aplicar para tornar o
projeto anulável nesta base.** O schema real já permite NULL.

### 9.6. Falhas de execução encontradas e resolvidas

As primeiras tentativas do navegador não concluíram todos os cenários; elas não
foram contabilizadas como aprovação:

1. Chrome dentro do sandbox: timeout de Page.enable; logs mostraram acesso
   negado, falhas repetidas de processo gráfico e encerramento do Chrome.
   Após autorização, o teste foi executado fora do sandbox, mantendo site,
   perfil e banco isolados. Não foi instalada ferramenta nem desabilitado o
   sandbox interno do Chrome.
2. Clique durante rolagem suave: timeout aguardando alerta, sem POST realizado.
   A captura do formulário confirmou todos os campos válidos. O automatizador
   passou a usar rolagem instantânea e confirmar elementFromPoint antes do clique.
3. Corrida com carregamento de doadores/navegação: em uma repetição, interação
   ocorreu antes do carregamento assíncrono das opções. O automatizador passou
   a aguardar nova performance.timeOrigin, documento completo e recurso de
   doadores carregado antes de interagir. A rodada final passou todos os cenários.

Essas correções foram feitas **somente no novo roteiro de testes**. Não houve
erro adicional da funcionalidade de projeto opcional que exigisse alteração do
código da aplicação nesta etapa. Os seis roteiros anteriores também passaram.

### 9.7. Arquivos desta etapa e pendências humanas

Arquivos criados/alterados nesta validação:

- `tests/doacao-integracao-isolada.cjs` — novo roteiro HTTP/PDO/Chrome isolado.
- `docs/ER-doacao-projeto-opcional-relatorio-SENAC.md` — atualização deste registro.

Nenhum arquivo de aplicação, relatório anterior de ER-01/ER-02/ER-03 ou dado
existente foi modificado nesta rodada. Não houve commit.

O requisito de projeto opcional passou nos cenários executáveis com mocks e
com gravações SQLite isoladas, incluindo o navegador real. Permanecem limites:

- Homologação de gravação em uma instância **MariaDB isolada**, caso se exija
  equivalência completa com o motor de produção. Não foi criada uma base nova
  no servidor real nem aplicado dump/migração automaticamente.
- Login/autorização reais, envio de recibos, câmera/upload e integrações externas
  não fazem parte deste teste do vínculo opcional; autenticação foi fixture.
- Na captura, a coluna Tipo mostra o identificador textual 1 enviado pelo cadastro,
  comportamento preexistente independente do projeto opcional. A apresentação do
  tipo não foi alterada nesta validação; eventual ajuste deve ter escopo próprio.
- As validações semânticas pendentes da cliente nas ER anteriores continuam
  pendentes. Nenhuma delas foi presumida para aprovar o vínculo opcional.

Não resta bloqueio técnico para os cenários executados nesta etapa. Nova
intervenção humana só é necessária para ampliar o escopo ou autorizar/prover a
homologação específica no motor MariaDB, se desejada.
