# ER-03 — Relatório técnico SENAC

**ER-03 — Implementação técnica concluída. Validação de terminologias específicas pela cliente pendente.**

As seções 1–6 registram a primeira etapa; a seção 7 preserva a segunda etapa.
A conclusão de encerramento técnico está na seção 8.

Projeto Integrador Alma Pantaneira V2 — Técnico em Desenvolvimento de Sistemas.  
Data: 02/11/2026. Escopo: correções editoriais de baixo risco autorizadas.

## 1. Problema identificado e análise do código

O diagnóstico identificou mensagens copiadas entre módulos, instruções genéricas,
grafia inconsistente, títulos de modais pouco específicos e botões cujo rótulo
não correspondia à ação. O diagnóstico foi lido integralmente antes de qualquer
alteração e permanece intacto: [ER-03-diagnostico.md](ER-03-diagnostico.md).

Foram realizadas **35 correções textuais em 18 arquivos da aplicação**, mais a
criação deste relatório. Uma correção corresponde a um local de texto alterado,
inclusive ocorrências iguais em lugares diferentes; não é contagem de linhas do
Git. Foram atendidos integral ou parcialmente 13 grupos do diagnóstico:
01, 02, 03, 04, 05, 07, 08, 09, 11, 13, 14, 19 e 20.

A análise de impacto verificou labels, placeholders, opções, eventos de reset,
scripts dos formulários e mensagens retornadas pelas actions. Alterações de
domínio financeiro e de comportamento ficaram fora do lote. As mudanças anteriores
de ER-01/ER-02 e outras alterações já presentes no diretório foram preservadas.

## 2. Arquivos modificados nesta etapa

- `app/view/JS/cadastro-doacao.js`
- `app/view/pages/listagem-fornecedor.php`
- `app/view/pages/cadastro-doacao.php`
- `app/view/pages/conciliacao-doacao.php`
- `app/view/pages/edicao-doacao.php`
- `app/view/pages/listagem-doacao.php`
- `app/view/pages/cadastro-despesa.php`
- `app/view/pages/edicao-despesa.php`
- `app/view/pages/cadastro-fornecedor.php`
- `app/view/pages/edicao-fornecedor.php`
- `app/view/pages/cadastro-doador.php`
- `app/view/pages/edicao-doador.php`
- `app/view/pages/listagem-doador.php`
- `app/view/pages/cadastro-projeto.php`
- `app/actions/cadastro-projeto.php`
- `app/actions/cadastro-fornecedor.php`
- `app/actions/cadastro-doador.php`
- `app/actions/cadastro-despesa.php`

Também foi criado `docs/ER-03-relatorio-SENAC.md`. São 19 arquivos nesta entrega:
18 arquivos de aplicação (17 PHP, sendo 13 páginas e 4 actions, e 1 JavaScript)
e este documento. O status geral do Git também contém mudanças pré-existentes
que não pertencem à ER-03.

## 3. Texto anterior, corrigido e justificativa

As linhas são dos arquivos atuais. Quando o trecho inclui uma tag, apenas o texto
visível ou placeholder foi modificado; os atributos reproduzidos permaneceram
iguais. Cada linha abaixo registra uma das 35 correções.

| Nº / grupo | Arquivo e linha | Texto/trecho anterior | Texto/trecho corrigido | Justificativa |
| --- | --- | --- | --- | --- |
| 1 / 01 | `app/view/JS/cadastro-doacao.js:77` | `Erro ao carregar fornecedores e projetos. Veja o console (F12).` | `Não foi possível carregar os doadores. Tente novamente.` | A requisição carrega doadores; preservados logs e tratamento de erro. |
| 2 / 01 | `app/view/JS/cadastro-doacao.js:77` | `Não foi possível carregar fornecedores e projetos.` | `Não foi possível carregar os doadores. Tente novamente.` | Corrige entidade de mensagem copiada de outro módulo. |
| 3 / 02 | `app/view/pages/listagem-fornecedor.php:13` | `Acompanhe os Fornecedores cadastrados, seus responsáveis e valores arrecadados.` | `Consulte e gerencie os fornecedores cadastrados.` | A listagem não apresenta arrecadação por fornecedor. |
| 4 / 03 | `app/view/pages/cadastro-doacao.php:100` | `>Forma de Recibo</label>` | `>Forma de recebimento</label>` | As opções representam meios de recebimento, não documentos. |
| 5 / 03 | `app/view/pages/conciliacao-doacao.php:120` | `>Forma de Recibo</th>` | `>Forma de recebimento</th>` | Padroniza o cabeçalho com o campo de recebimento. |
| 6 / 03 | `app/view/pages/conciliacao-doacao.php:166` | `>Forma de Recibo</span>` | `>Forma de recebimento</span>` | Padroniza o detalhe do mesmo campo, preservando o modal. |
| 7 / 03 | `app/view/pages/edicao-doacao.php:78` | `>Forma de Recebimento</label>` | `>Forma de recebimento</label>` | Uniformiza caixa entre cadastro, edição e consulta. |
| 8 / 04 | `app/view/pages/cadastro-doacao.php:102` | `<option value=""disabled selected hidden>Selecione o tipo</option>` | `<option value=""disabled selected hidden>Selecione a forma de recebimento</option>` | Placeholder possui value explícito vazio; orienta o campo correto sem mudar payload. |
| 9 / 05 | `app/view/pages/listagem-doacao.php:31` | `<select id="periodo" name="periodo">                   <option value="selecione" disabled selected hidden>Selecione o tipo</option>` | `<select id="periodo" name="periodo">                   <option value="selecione" disabled selected hidden>Selecione o período</option>` | Placeholder do filtro de período com value explícito preservado. |
| 10 / 07 | `app/view/pages/cadastro-despesa.php:140` | `>Selecione a opção</option>` | `>Selecione a forma de pagamento</option>` | Placeholder de pagamento com value explícito vazio. |
| 11 / 07 | `app/view/pages/edicao-despesa.php:69` | `>Selecione a opção</option>` | `>Selecione a forma de pagamento</option>` | Aplica a mesma instrução ao campo equivalente da edição, com value explícito vazio. |
| 12 / 08 | `app/view/pages/cadastro-fornecedor.php:30` | `id="nome" name="nome" placeholder="Nome ou Instituição"` | `id="nome" name="nome" placeholder="Informe o nome fantasia"` | Distingue orientação do nome fantasia da razão social. |
| 13 / 08 | `app/view/pages/cadastro-fornecedor.php:34` | `id="razao_social" name="razao_social" placeholder="Nome ou Instituição"` | `id="razao_social" name="razao_social" placeholder="Informe a razão social"` | Instrui o preenchimento do campo de razão social. |
| 14 / 08 | `app/view/pages/edicao-fornecedor.php:32` | `id="nome" name="nome" placeholder="Nome ou Instituição"` | `id="nome" name="nome" placeholder="Informe o nome fantasia"` | Distingue orientação do nome fantasia da razão social. |
| 15 / 08 | `app/view/pages/edicao-fornecedor.php:36` | `id="razao_social" name="razao_social" placeholder="Nome ou Instituição"` | `id="razao_social" name="razao_social" placeholder="Informe a razão social"` | Instrui o preenchimento do campo de razão social. |
| 16 / 09 | `app/view/pages/cadastro-doador.php:106` | `<label for="logradouro">Rua <span` | `<label for="logradouro">Logradouro <span` | Uniformiza com fornecedores sem restringir o campo a rua. |
| 17 / 09 | `app/view/pages/edicao-doador.php:94` | `<label for="logradouro">Rua</label>` | `<label for="logradouro">Logradouro</label>` | Mesmo conceito e nome técnico nos dois formulários. |
| 18 / 09 | `app/view/pages/edicao-doador.php:95` | `name="logradouro" placeholder="Rua"` | `name="logradouro" placeholder="Logradouro"` | Alinha placeholder ao rótulo, sem mudar campo ou máscara. |
| 19 / 11 | `app/view/pages/cadastro-fornecedor.php:45` | `for="email">Email` | `for="email">E-mail` | Padroniza grafia; name/id email e asterisco permanecem iguais. |
| 20 / 11 | `app/view/pages/cadastro-doador.php:90` | `for="email">Email` | `for="email">E-mail` | Padroniza grafia; name/id email e asterisco permanecem iguais. |
| 21 / 11 | `app/view/pages/edicao-doador.php:77` | `for="email">Email` | `for="email">E-mail` | Padroniza grafia; name/id email e asterisco permanecem iguais. |
| 22 / 11 | `app/view/pages/listagem-fornecedor.php:60` | `<th>Email</th>` | `<th>E-mail</th>` | Padroniza cabeçalho de e-mail. |
| 23 / 11 | `app/view/pages/listagem-doador.php:68` | `<th>Email</th>` | `<th>E-mail</th>` | Padroniza cabeçalho de e-mail. |
| 24 / 13 | `app/view/pages/cadastro-fornecedor.php:94` | `<button type="reset" class="btn-secondary">Cancelar</button>` | `<button type="reset" class="btn-secondary">Limpar formulário</button>` | O botão de cadastro executa reset; preservados tipo e comportamento. |
| 25 / 13 | `app/view/pages/cadastro-doacao.php:149` | `<button type="reset" class="btn-secondary">Cancelar</button>` | `<button type="reset" class="btn-secondary">Limpar formulário</button>` | O botão de cadastro executa reset; preservados tipo e comportamento. |
| 26 / 13 | `app/view/pages/cadastro-projeto.php:88` | `<button type="reset" class="btn-secondary">Cancelar</button>` | `<button type="reset" class="btn-secondary">Limpar formulário</button>` | O botão de cadastro executa reset; preservados tipo e comportamento. |
| 27 / 14 | `app/view/pages/cadastro-despesa.php:195` | `<h2>Categoria da Despesa</h2>` | `<h2>Nova categoria de despesa</h2>` | Explicita a criação no modal existente sem confundir tipo e categoria. |
| 28 / 14 | `app/view/pages/edicao-despesa.php:136` | `<h2>Categoria da Despesa</h2>` | `<h2>Nova categoria de despesa</h2>` | Explicita a criação no modal existente sem confundir tipo e categoria. |
| 29 / 14 | `app/view/pages/cadastro-doacao.php:159` | `<h2>Tipo de Doação</h2>` | `<h2>Novo tipo de doação</h2>` | Explicita a ação de criar no modal existente. |
| 30 / 19 | `app/actions/cadastro-projeto.php:35` | `'Cadastrado com succeso'` | `'Projeto cadastrado com sucesso.'` | Corrige erro ortográfico e identifica o registro salvo. |
| 31 / 19 | `app/actions/cadastro-projeto.php:35` | `'Não foi cadastrado'` | `'Não foi possível cadastrar o projeto.'` | Identifica a operação que falhou sem mudar seu critério. |
| 32 / 20 | `app/actions/cadastro-fornecedor.php:55` | `'Cadastrado com Sucesso'` | `'Fornecedor cadastrado com sucesso.'` | Identifica a entidade na confirmação. |
| 33 / 20 | `app/actions/cadastro-fornecedor.php:55` | `'Não Cadastrado'` | `'Não foi possível cadastrar o fornecedor.'` | Torna a falha específica e mantém o retorno JSON. |
| 34 / 20 | `app/actions/cadastro-doador.php:56` | `'Cadastrado com Sucesso!'` | `'Doador cadastrado com sucesso.'` | Identifica a entidade e uniformiza pontuação. |
| 35 / 20 | `app/actions/cadastro-despesa.php:126` | `'Cadastrado com Sucesso!'` | `'Despesa cadastrada com sucesso.'` | Identifica a entidade e ajusta concordância. |

## 4. Análise dos impactos e preservação funcional

- A busca de doadores deixa de citar fornecedores/projetos; logs, endpoints e
  tratamento de erro continuam os mesmos.
- “Forma de recebimento” muda somente labels, cabeçalho e detalhe. As opções e
  seus valores enviados permanecem intactos.
- Os quatro placeholders de select alterados foram conferidos previamente:
  recebimento da doação usa `value=""`; período usa `value="selecione"`;
  pagamento no cadastro e na edição da despesa usa `value=""`. Não houve mudança
  nos valores ou nos estados `disabled`, `selected` e `hidden`.
- “Limpar formulário” foi aplicado somente aos três botões de cadastro já do tipo
  `reset`. Não foi adicionado handler, navegação nem mudança de tipo de botão.
  Links de cancelamento e botões de fechar/cancelar modais permanecem iguais.
- Labels de logradouro/e-mail e placeholders mantêm IDs, names, máscaras,
  asteriscos e obrigatoriedade. Labels de doador podem ser incorporados pelo
  validador a um alerta; o mecanismo de composição da mensagem não mudou.
- Títulos dos modais explicam criação; cadastro e seleção dinâmica de categoria
  e tipo permanecem iguais, preservando a ER-02.
- Nas actions, somente literais de mensagem foram alterados. Chaves existentes
  como `successo`, `sucess`, `success` e `message` foram preservadas, assim como
  casts, códigos, condições e respostas de sucesso/falha.
- Fornecedor/projeto opcionais da ER-01, separação tipo/categoria, NULL legado,
  classificação provisória Fixa/Variável e filtros/cache da ER-02 foram mantidos.
- Não houve mudança de banco, SQL, variáveis, funções, classes, rotas ou formatos
  de dados. Nenhum endpoint real foi chamado durante os testes desta etapa.

## 5. Testes executados e resultados

| Teste | Procedimento | Resultado |
| --- | --- | --- |
| Caixa branca — escopo | Snapshot anterior à etapa e reaplicação das substituições aprovadas, comparados byte a byte com os arquivos finais. | Aprovado: exatamente 35 substituições em 18 arquivos. Os outros 133 arquivos do snapshot permaneceram iguais antes de criar o relatório, incluindo diagnóstico, controllers, consultas e testes anteriores. |
| Caixa branca — HTML | Comparação das sequências de tags antes/depois, permitindo somente os placeholders de input aprovados. | Aprovado: estrutura e demais atributos preservados, incluindo `id`, `name`, `value`, `type`, classes, links, handlers e atributos de validação. |
| Caixa branca — selects | Comparação de atributos e valores efetivos de opções; quando não há `value`, considerar seu texto como valor. | Aprovado: 79 opções estáticas preservam atributos e valores. Expressões PHP dinâmicas também não receberam substituições. |
| Sintaxe PHP | `C:/xampp/php/php.exe -l` em cada um dos 17 PHP alterados. | Aprovado: nenhum erro de sintaxe. |
| Sintaxe JavaScript | `node --check app/view/JS/cadastro-doacao.js`. | Aprovado. |
| Regressão ER-02 e opcionais da ER-01 | `C:/xampp/php/php.exe tests/er02-sem-banco.php`. | Aprovado: 8 verificações de tipo/categoria independentes, NULL, filtros, LEFT JOIN e existência; Database simulado, sem PDO real. |
| Regressão de formulário | `node tests/er02-formulario.cjs`. | Aprovado: aceita categoria dinâmica e opcionais vazios; rejeita texto sem vínculo e categoria vazia. |
| Caixa branca — JS dinâmico | JavaScript anterior/atual em VM, com DOM e fetch simulados: JSON inválido, resposta sem doadores, lista válida, falha de rede e submit. | Aprovado: 5 cenários; mesmos endpoints, métodos e fluxos. Apenas os dois alertas aprovados mudaram. |
| Caixa branca — actions | Quatro actions reais avaliadas em cópias em memória com controllers/retornos simulados, sem seus requires reais. Um cenário de sucesso e um de falha por entidade. | Aprovado: 8 cenários; mensagens e chaves/indicadores JSON conferidos; sem conexão com banco. |
| Whitespace | `git diff --check` limitado aos arquivos desta etapa. | Aprovado. |

Verificadores adicionais executados temporariamente em memória e na pasta
temporária do ambiente; nenhuma dependência adicionada ou teste existente
alterado. O snapshot corresponde à situação inicial desta etapa, porque o diff
contra HEAD inclui implementações anteriores ainda não commitadas.

Limites: não foram feitos testes no navegador real, inspeção visual em dispositivos,
reset nativo do browser, envio real de formulários ou testes de integração com PDO.
Falhas de rede foram simuladas; os requires das actions foram substituídos por
mocks somente nas cópias do teste, nunca nos arquivos da aplicação.

Próxima verificação manual em homologação: conferir alinhamento dos textos em
desktop/móvel, reset dos três cadastros, navegação de Cancelar, abertura/fechamento
dos modais e labels nas mensagens de validação de doador. Não se declara melhoria
de usabilidade medida nesta etapa.

## 6. Pendências

### Dependem da cliente — mantidas sem alterações

- Grupos 24–29: situação cadastral versus recebimento, receitas/saldo do painel,
  conciliação versus confirmação, valor/meta/orçamento do projeto, base do total
  arrecadado e significado da data da doação/recebimento.
- Grupo 31: natureza e nomenclatura de nota fiscal, recibo e comprovante por etapa.
- Grupo 36: comunicação dos envios simulados de recibo no protótipo.
- Grupo 37: eventual suporte a CPF para fornecedor; o rótulo CNPJ foi preservado.
- Grupo 38: integração/finalidade da tela estática de detalhes de doador.
- A classificação Fixa/Variável segue provisória e depende de validação.

### Dependências técnicas e itens para lotes futuros

- Opções sem `value` explícito não foram alteradas: meios de recebimento da
  doação, tipos de doação na edição, tipo/andamento de projeto e status textuais.
  Alterar sua grafia pode mudar o payload ou impedir a seleção do valor salvo.
- Do grupo 13, os resets das telas de edição foram adiados para avaliar o que
  restauram depois do carregamento dinâmico. Não alterar seu comportamento para
  corresponder ao texto. Cancelar dos links/modais permanece intacto.
- Grupos 06, 10, 12, 15–18, 21–23, 30 e 32–35 não foram implementados neste lote.
  Incluem padronizações adicionais, textos dinâmicos e ramos a revisar. Nem todos
  dependem da cliente; precisam de seleção para a próxima etapa. O grupo 07 foi
  aplicado também à instrução equivalente da edição, com valor preservado.
- Problemas técnicos fora de copy continuam fora do escopo: confirmação
  duplicada, ícones sem ação, HTML de estados vazios, contratos JSON inconsistentes
  e integração dos protótipos. Não foram transformados em regras novas.

O diagnóstico original não foi substituído ou apagado. Nenhum SQL foi executado,
nenhum dado foi excluído e nenhum commit foi realizado. Esta primeira etapa está
encerrada; aguardar novas instruções para ampliar a implementação.

## 7. Segunda etapa — continuidade autorizada

Data: 02/11/2026. Esta seção complementa o histórico da primeira etapa, sem
substituir suas tabelas, contagens ou resultados. O diagnóstico e este relatório
foram lidos integralmente antes da implementação.

### 7.1. Escopo, padrão visual e contagens

- **Segunda etapa: 98 correções textuais em 34 arquivos de aplicação**, sendo
  22 PHP e 12 JavaScripts.
- **Acumulado da ER-03: 133 correções** (35 + 98), em **40 arquivos distintos de
  aplicação**. Uma correção é uma ocorrência de texto alterada em uma etapa;
  não equivale a linhas de Git nem necessariamente a um rótulo único no estado final.
- Nesta etapa também foi ampliado um roteiro de integração existente, criado
  um novo teste e atualizado este relatório: **37 arquivos na entrega da etapa**.
  No acumulado, são 43 arquivos distintos contando aplicação, os dois testes e
  este relatório. O diagnóstico original não foi alterado.

Foram aplicados literalmente os plurais solicitados:
**Gerenciar e Cadastrar Despesas** e **Gerenciar e Cadastrar Doações**.
O mesmo padrão foi aplicado aos cadastros relacionados de doadores, fornecedores
e projetos. Iniciais maiúsculas dos títulos foram preservadas conforme a escolha
visual expressa nesta autorização. A sugestão inicial do diagnóstico de reduzir
todos os rótulos para caixa de frase (grupo 18) não foi aplicada contra essa escolha.
Textos de botões de reset nas edições seguem “Limpar formulário”, como nos três
cadastros corrigidos na primeira etapa.

### 7.2. Arquivos modificados nesta etapa

- `app/view/pages/cadastro-despesa.php`
- `app/view/pages/edicao-despesa.php`
- `app/view/pages/listagem-despesa.php`
- `app/view/pages/cadastro-doacao.php`
- `app/view/pages/edicao-doacao.php`
- `app/view/pages/listagem-doacao.php`
- `app/view/pages/cadastro-fornecedor.php`
- `app/view/pages/edicao-fornecedor.php`
- `app/view/pages/listagem-fornecedor.php`
- `app/view/pages/cadastro-doador.php`
- `app/view/pages/edicao-doador.php`
- `app/view/pages/listagem-doador.php`
- `app/view/pages/cadastro-projeto.php`
- `app/view/pages/edicao-projeto.php`
- `app/view/pages/listagem-projeto.php`
- `app/view/JS/cadastro-doador.js`
- `app/view/JS/editar-doador.js`
- `app/view/pages/relatorio-projeto.php`
- `app/view/pages/listagem-conciliacao.php`
- `app/view/components/sidebar.php`
- `app/actions/editar-fornecedor.php`
- `app/actions/editar-doacao.php`
- `app/actions/editar-projeto.php`
- `app/actions/editar-doador.php`
- `app/view/JS/cadastro-despesa.js`
- `app/view/JS/editar-despesa.js`
- `app/view/JS/listagem-despesa.js`
- `app/view/JS/listagem-fornecedor.js`
- `app/view/JS/listagem-doador.js`
- `app/view/JS/listagem-projeto.js`
- `app/view/JS/listagem-doacao.js`
- `app/view/JS/cadastro-fornecedor.js`
- `app/view/JS/mascara-cadastro-fornecedor.js`
- `app/view/JS/relatorio-projeto.js`

Arquivos de testes e documentação:

- `tests/doacao-integracao-isolada.cjs` — acrescenta o modo opcional `--er03`,
  com verificações de textos, payload, reset, modal e cadastro de despesas.
- `tests/er03-terminologias.cjs` — novo teste de switches/payloads, PF/PJ e erro JSON.
- `docs/ER-03-relatorio-SENAC.md` — esta atualização.

As alterações de código da aplicação se limitam a textos visíveis, placeholders,
títulos e literais utilizados nas mensagens. Não foram alterados identificadores,
rotas, contratos JSON, consultas, validações, condições ou regras de negócio.

### 7.3. Texto anterior, atualizado e justificativa

As localizações abaixo correspondem ao snapshot do início da segunda etapa;
como não foram inseridas linhas nos fontes, continuam identificando as ocorrências.
Em trechos que reproduzem tags, os atributos não mudaram, exceto o conteúdo
textual dos placeholders de contato explicitamente registrados.

| Nº / grupo | Arquivo e linha | Texto anterior | Texto atualizado | Justificativa técnica |
| --- | --- | --- | --- | --- |
| 1 / 15 | `app/view/pages/cadastro-despesa.php:25` | `Gerenciar e Cadastrar Despesa </p>` | `Gerenciar e Cadastrar Despesas </p>` | Plural dos registros; iniciais maiúsculas conforme orientação. |
| 2 / 15 | `app/view/pages/cadastro-despesa.php:18` | `Cadastro Despesa - Alma Pantaneira` | `Cadastro de Despesas - Alma Pantaneira` | Preposição correta e título de cadastro consistente. |
| 3 / 15 | `app/view/pages/edicao-despesa.php:14` | `Edição Despesa - Alma Pantaneira` | `Edição de Despesa - Alma Pantaneira` | Preposição correta na aba de edição. |
| 4 / 16 | `app/view/pages/listagem-despesa.php:8` | `Listagem Despesas - Alma Pantaneira` | `Lista de Despesas - Alma Pantaneira` | Alinha título da aba ao cabeçalho. |
| 5 / 15 | `app/view/pages/cadastro-doacao.php:26` | `Gerenciar e Cadastrar Doação </p>` | `Gerenciar e Cadastrar Doações </p>` | Plural dos registros; iniciais maiúsculas conforme orientação. |
| 6 / 15 | `app/view/pages/cadastro-doacao.php:21` | `Cadastro De Doações - Alma Pantaneira` | `Cadastro de Doações - Alma Pantaneira` | Preposição correta e título de cadastro consistente. |
| 7 / 15 | `app/view/pages/edicao-doacao.php:18` | `Edição Doação - Alma Pantaneira` | `Edição de Doação - Alma Pantaneira` | Preposição correta na aba de edição. |
| 8 / 16 | `app/view/pages/listagem-doacao.php:8` | `Listagem Doações - Alma Pantaneira` | `Lista de Doações - Alma Pantaneira` | Alinha título da aba ao cabeçalho. |
| 9 / 15 | `app/view/pages/cadastro-fornecedor.php:15` | `Gerenciar e Cadastrar Fornecedor </p>` | `Gerenciar e Cadastrar Fornecedores </p>` | Plural dos registros; iniciais maiúsculas conforme orientação. |
| 10 / 15 | `app/view/pages/cadastro-fornecedor.php:10` | `Cadastro Fornecedor - Alma Pantaneira` | `Cadastro de Fornecedores - Alma Pantaneira` | Preposição correta e título de cadastro consistente. |
| 11 / 15 | `app/view/pages/edicao-fornecedor.php:10` | `Edição Fornecedor - Alma Pantaneira` | `Edição de Fornecedor - Alma Pantaneira` | Preposição correta na aba de edição. |
| 12 / 16 | `app/view/pages/listagem-fornecedor.php:8` | `Listagem Fornecedores - Alma Pantaneira` | `Lista de Fornecedores - Alma Pantaneira` | Alinha título da aba ao cabeçalho. |
| 13 / 15 | `app/view/pages/cadastro-doador.php:15` | `Gerenciar e Cadastrar Doador </p>` | `Gerenciar e Cadastrar Doadores </p>` | Plural dos registros; iniciais maiúsculas conforme orientação. |
| 14 / 15 | `app/view/pages/cadastro-doador.php:10` | `Cadastro Doador - Alma Pantaneira` | `Cadastro de Doadores - Alma Pantaneira` | Preposição correta e título de cadastro consistente. |
| 15 / 15 | `app/view/pages/edicao-doador.php:10` | `Edição Doador - Alma Pantaneira` | `Edição de Doador - Alma Pantaneira` | Preposição correta na aba de edição. |
| 16 / 16 | `app/view/pages/listagem-doador.php:8` | `Listagem Doadores - Alma Pantaneira` | `Lista de Doadores - Alma Pantaneira` | Alinha título da aba ao cabeçalho. |
| 17 / 15 | `app/view/pages/cadastro-projeto.php:13` | `Gerenciar e Cadastrar Projeto </p>` | `Gerenciar e Cadastrar Projetos </p>` | Plural dos registros; iniciais maiúsculas conforme orientação. |
| 18 / 15 | `app/view/pages/cadastro-projeto.php:8` | `Cadastro Projeto - Alma Pantaneira` | `Cadastro de Projetos - Alma Pantaneira` | Preposição correta e título de cadastro consistente. |
| 19 / 15 | `app/view/pages/edicao-projeto.php:8` | `Edição Projeto - Alma Pantaneira` | `Edição de Projeto - Alma Pantaneira` | Preposição correta na aba de edição. |
| 20 / 16 | `app/view/pages/listagem-projeto.php:8` | `Listagem Projetos - Alma Pantaneira` | `Lista de Projetos - Alma Pantaneira` | Alinha título da aba ao cabeçalho. |
| 21 / 16 | `app/view/pages/listagem-projeto.php:12` | `<h1>Listagem de Projetos</h1>` | `<h1>Lista de Projetos</h1>` | Uniformiza as listagens. |
| 22 / 15 | `app/view/pages/edicao-despesa.php:19` | `Gerenciar cadastro de Despesa` | `Gerenciar e Editar Despesa` | Subtítulo corresponde à edição e ao padrão visual. |
| 23 / 15 | `app/view/pages/edicao-doador.php:15` | `Gerenciar cadastro de Doador` | `Gerenciar e Editar Doador` | Subtítulo corresponde à edição e ao padrão visual. |
| 24 / 15 | `app/view/pages/edicao-doacao.php:23` | `Gerenciar Edição de doação` | `Gerenciar e Editar Doação` | Subtítulo corresponde à edição e ao padrão visual. |
| 25 / 15 | `app/view/pages/edicao-projeto.php:13` | `Editar e gerenciar projeto` | `Gerenciar e Editar Projeto` | Subtítulo corresponde à edição e ao padrão visual. |
| 26 / 10 | `app/view/pages/cadastro-doador.php:79` | `Contato na Empresa <span` | `Pessoa de Contato <span` | Identifica pessoa de contato, distinta de telefone. |
| 27 / 10 | `app/view/pages/cadastro-doador.php:80` | `placeholder="Ex: Maria do Financeiro"` | `placeholder="Nome da pessoa de contato"` | Orienta o conteúdo sem presumir cargo ou empresa. |
| 28 / 10 | `app/view/pages/edicao-doador.php:66` | `Pessoa de Contato na Empresa</label>` | `Pessoa de Contato</label>` | Identifica pessoa de contato, distinta de telefone. |
| 29 / 10 | `app/view/pages/edicao-doador.php:67` | `placeholder="Ex: Maria do Financeiro"` | `placeholder="Nome da pessoa de contato"` | Orienta o conteúdo sem presumir cargo ou empresa. |
| 30 / 10 | `app/view/pages/cadastro-fornecedor.php:49` | `for="contato">Contato</label>` | `for="contato">Pessoa de Contato</label>` | Mesmo conceito usado em doadores. |
| 31 / 10 | `app/view/pages/cadastro-fornecedor.php:50` | `placeholder="Contato na Empresa"` | `placeholder="Nome da pessoa de contato"` | Orienta o conteúdo do campo, sem renomeá-lo. |
| 32 / 10 | `app/view/pages/edicao-fornecedor.php:51` | `for="contato">Contato</label>` | `for="contato">Pessoa de Contato</label>` | Mesmo conceito usado em doadores. |
| 33 / 10 | `app/view/pages/edicao-fornecedor.php:52` | `placeholder="Nome de contato"` | `placeholder="Nome da pessoa de contato"` | Orienta o conteúdo do campo, sem renomeá-lo. |
| 34 / 12 | `app/view/JS/cadastro-doador.js:100` | `CPF do Doador <span` | `CPF <span` | Uniformiza documento PF; máscara e asterisco preservados. |
| 35 / 12 | `app/view/JS/cadastro-doador.js:108` | `CNPJ da Empresa <span` | `CNPJ <span` | Uniformiza documento PJ, sem restringir a empresas. |
| 36 / 12 | `app/view/JS/editar-doador.js:144` | `CPF do Doador <span` | `CPF <span` | Uniformiza documento PF; máscara e asterisco preservados. |
| 37 / 12 | `app/view/JS/editar-doador.js:139` | `CNPJ da Empresa <span` | `CNPJ <span` | Uniformiza documento PJ, sem restringir a empresas. |
| 38 / 13 | `app/view/pages/edicao-doacao.php:135` | `<button type="reset" class="btn-secondary">Cancelar</button>` | `<button type="reset" class="btn-secondary">Limpar formulário</button>` | Reset limpa os valores iniciais do HTML, não cancela/navega. |
| 39 / 13 | `app/view/pages/edicao-projeto.php:88` | `<button type="reset" class="btn-secondary">Cancelar</button>` | `<button type="reset" class="btn-secondary">Limpar formulário</button>` | Reset limpa os valores iniciais do HTML, não cancela/navega. |
| 40 / 22 | `app/view/pages/listagem-fornecedor.php:33` | `for="status">Status</label>` | `for="status">Situação Cadastral</label>` | Distingue ativação do cadastro de andamento/recebimento. |
| 41 / 06 | `app/view/pages/listagem-fornecedor.php:35` | `<option value="selecione" disabled selected hidden>Selecione o tipo</option>` | `<option value="selecione" disabled selected hidden>Selecione a situação cadastral</option>` | Instrução específica do filtro; value e atributos preservados. |
| 42 / 22 | `app/view/pages/listagem-doador.php:42` | `for="status_doador">Status</label>` | `for="status_doador">Situação Cadastral</label>` | Distingue ativação do cadastro de andamento/recebimento. |
| 43 / 06 | `app/view/pages/listagem-doador.php:44` | `<option value="selecione" selected hidden>Selecione o tipo</option>` | `<option value="selecione" selected hidden>Selecione a situação cadastral</option>` | Instrução específica do filtro; value e atributos preservados. |
| 44 / 22 | `app/view/pages/listagem-projeto.php:44` | `for="status">Status do Projeto</label>` | `for="status">Situação Cadastral</label>` | Distingue ativação do cadastro de andamento/recebimento. |
| 45 / 06 | `app/view/pages/listagem-projeto.php:46` | `<option value="selecione" disabled selected hidden>Selecione o tipo</option>` | `<option value="selecione" disabled selected hidden>Selecione a situação cadastral</option>` | Instrução específica do filtro; value e atributos preservados. |
| 46 / 22 | `app/view/pages/relatorio-projeto.php:44` | `for="status">Status do Projeto</label>` | `for="status">Situação Cadastral</label>` | Distingue ativação do cadastro de andamento/recebimento. |
| 47 / 06 | `app/view/pages/relatorio-projeto.php:45` | `<select id="status" name="status">                   <option value="selecione" disabled selected hidden>Selecione o tipo</option>` | `<select id="status" name="status">                   <option value="selecione" disabled selected hidden>Selecione a situação cadastral</option>` | Instrução específica do filtro; value e atributos preservados. |
| 48 / 06 | `app/view/pages/listagem-conciliacao.php:43` | `<option value=""disabled selected hidden>Selecione o tipo</option>` | `<option value=""disabled selected hidden>Selecione uma opção</option>` | Corrige instrução sem redefinir conciliação. |
| 49 / 23 | `app/view/pages/cadastro-projeto.php:69` | `for="andamento">Status</label>` | `for="andamento">Andamento do Projeto</label>` | Distingue o campo andamento do cadastro ativo. |
| 50 / 23 | `app/view/pages/edicao-projeto.php:70` | `for="andamento">Status</label>` | `for="andamento">Andamento do Projeto</label>` | Distingue o campo andamento do cadastro ativo. |
| 51 / 23 | `app/view/pages/relatorio-projeto.php:69` | `<th>Status</th>` | `<th>Andamento do Projeto</th>` | A célula exibe projeto.andamento. |
| 52 / 28 | `app/view/pages/relatorio-projeto.php:8` | `Doações por Projetos` | `Doações por Projeto` | Título de agrupamento singular; métrica/cálculo preservados. |
| 53 / 28 | `app/view/pages/relatorio-projeto.php:12` | `Doações por Projetos` | `Doações por Projeto` | Título de agrupamento singular; métrica/cálculo preservados. |
| 54 / 30 | `app/view/pages/listagem-doacao.php:85` | `<th>Valor / Qtd</th>` | `<th>Valor (R$)</th>` | A célula usa apenas formatação monetária, sem unidade/quantidade. |
| 55 / 17 | `app/view/components/sidebar.php:36` | `>Conciliação Doação</a>` | `>Conciliação de Doações</a>` | Corrige preposição/plural, sem trocar termo ou destino. |
| 56 / 17 | `app/view/components/sidebar.php:49` | `>Conciliação Despesas</a>` | `>Conciliação de Despesas</a>` | Corrige preposição/plural, sem trocar termo ou destino. |
| 57 / 21 | `app/actions/editar-fornecedor.php:23` | `Fornecedor ou Endereço não Encontrado!` | `Fornecedor ou endereço não encontrado.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 58 / 21 | `app/actions/editar-fornecedor.php:28` | `Parametros Invalidos!` | `Parâmetros inválidos.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 59 / 21 | `app/actions/editar-fornecedor.php:83` | `Fornecedor Atualizado com Sucesso!` | `Fornecedor atualizado com sucesso.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 60 / 21 | `app/actions/editar-fornecedor.php:89` | `Erro ao tentar editar o fornecedor!` | `Não foi possível atualizar o fornecedor.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 61 / 21 | `app/actions/editar-doacao.php:15` | `Doação inexistente!!` | `Doação não encontrada.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 62 / 21 | `app/actions/editar-projeto.php:18` | `Projeto inexistente!!` | `Projeto não encontrado.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 63 / 21 | `app/actions/editar-projeto.php:65` | `Projeto editado com sucesso!` | `Projeto atualizado com sucesso.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 64 / 21 | `app/actions/editar-doacao.php:73` | `Doação editada com sucesso!` | `Doação atualizada com sucesso.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 65 / 21 | `app/actions/editar-doador.php:69` | `Doador e Endereço atualizados com sucesso!` | `Dados do doador e do endereço atualizados com sucesso.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 66 / 21 | `app/actions/editar-doador.php:120` | `Doador e Endereço atualizados com sucesso!` | `Dados do doador e do endereço atualizados com sucesso.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 67 / 21 | `app/actions/editar-doador.php:71` | `Erro ao atualizar os dados do doador ou endereço.` | `Não foi possível atualizar os dados do doador ou do endereço.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 68 / 21 | `app/actions/editar-doador.php:122` | `Erro ao atualizar os dados do doador ou endereço.` | `Não foi possível atualizar os dados do doador ou do endereço.` | Corrige redação/ortografia/pontuação, mantendo ramo e contrato JSON. |
| 69 / 32 | `app/view/JS/cadastro-despesa.js:12` | `Erro ao carregar fornecedores e projetos. Veja o console (F12).` | `Não foi possível carregar fornecedores e projetos. Tente novamente.` | Remove dependência de console do usuário; logs preservados. |
| 70 / 32 | `app/view/JS/cadastro-despesa.js:71` | `Ocorreu um erro no servidor ao salvar. Verifique o console.` | `Não foi possível salvar a despesa. Tente novamente.` | Mensagem da tarefa, mesmos logs e tratamento de erro. |
| 71 / 32 | `app/view/JS/editar-despesa.js:148` | `Ocorreu um erro no servidor ao salvar. Verifique o console.` | `Não foi possível salvar a despesa. Tente novamente.` | Mensagem da tarefa, mesmos logs e tratamento de erro. |
| 72 / 33 | `app/view/JS/listagem-despesa.js:112` | `Erro ao atualizar no banco de dados: ` | `Não foi possível atualizar a despesa: ` | Prefixo orientado à tarefa; detalhe do backend preservado. |
| 73 / 33 | `app/view/JS/listagem-fornecedor.js:100` | `Erro ao atualizar no banco de dados: ` | `Não foi possível atualizar o fornecedor: ` | Prefixo orientado à tarefa; detalhe do backend preservado. |
| 74 / 33 | `app/view/JS/listagem-doador.js:92` | `Erro ao atualizar no banco de dados: ` | `Não foi possível atualizar o doador: ` | Prefixo orientado à tarefa; detalhe do backend preservado. |
| 75 / 33 | `app/view/JS/listagem-projeto.js:120` | `Erro ao atualizar no banco de dados: ` | `Não foi possível atualizar o projeto: ` | Prefixo orientado à tarefa; detalhe do backend preservado. |
| 76 / 34 | `app/view/JS/listagem-doacao.js:11` | `Nenhuma doação Cadastrada!` | `Nenhuma doação encontrada.` | Uniformiza estado vazio, preservando HTML/condições. |
| 77 / 34 | `app/view/JS/listagem-fornecedor.js:44` | `Nenhum Fornecedor Cadastrado!` | `Nenhum fornecedor encontrado.` | Uniformiza estado vazio, preservando HTML/condições. |
| 78 / 34 | `app/view/JS/listagem-projeto.js:59` | `Nenhum Projeto Cadastrado!` | `Nenhum projeto encontrado.` | Uniformiza estado vazio, preservando HTML/condições. |
| 79 / 34 | `app/view/JS/listagem-doacao.js:88` | `Erro ao Carregar Doações.` | `Não foi possível carregar as doações.` | Corrige capitalização e identifica tarefa. |
| 80 / 35 | `app/view/JS/listagem-despesa.js:93` | `esta Despesa?` | `esta despesa?` | Corrige ortografia/capitalização da confirmação. |
| 81 / 35 | `app/view/JS/listagem-doador.js:73` | `este Doador?` | `este doador?` | Corrige ortografia/capitalização da confirmação. |
| 82 / 35 | `app/view/JS/listagem-doacao.js:49` | `está Daoção?` | `esta doação?` | Corrige ortografia/capitalização da confirmação. |
| 83 / 35 | `app/view/JS/listagem-despesa.js:90` | `"ATIVAR"` | `"ativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 84 / 35 | `app/view/JS/listagem-despesa.js:90` | `"DESATIVAR"` | `"desativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 85 / 35 | `app/view/JS/listagem-doador.js:71` | `"ATIVAR"` | `"ativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 86 / 35 | `app/view/JS/listagem-doador.js:71` | `"DESATIVAR"` | `"desativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 87 / 35 | `app/view/JS/listagem-fornecedor.js:79` | `"ATIVAR"` | `"ativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 88 / 35 | `app/view/JS/listagem-fornecedor.js:79` | `"DESATIVAR"` | `"desativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 89 / 35 | `app/view/JS/listagem-projeto.js:101` | `"ATIVAR"` | `"ativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 90 / 35 | `app/view/JS/listagem-projeto.js:101` | `"DESATIVAR"` | `"desativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 91 / 35 | `app/view/JS/listagem-doacao.js:47` | `"ATIVAR"` | `"ativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 92 / 35 | `app/view/JS/listagem-doacao.js:47` | `"DESATIVAR"` | `"desativar"` | Literal exclusivo do confirm; payload novoStatus intacto. |
| 93 / 32 | `app/view/JS/cadastro-fornecedor.js:18` | `Erro na Requisicao` | `Não foi possível cadastrar o fornecedor. Tente novamente.` | Corrige mensagem de falha, mesmo catch/log. |
| 94 / 11 | `app/view/JS/mascara-cadastro-fornecedor.js:81` | `Insira um Email Válido.` | `Informe um e-mail válido.` | Uniformiza grafia sem alterar regex. |
| 95 / 33 | `app/view/JS/listagem-doacao.js:68` | `Erro ao atualizar no banco de dados: ` | `Não foi possível atualizar a doação: ` | Mesmo prefixo de erro dos demais módulos; detalhe e status preservados. |
| 96 / 34 | `app/view/JS/relatorio-projeto.js:11` | `Nenhum projeto cadastrado.` | `Nenhum projeto encontrado.` | Uniformiza estado vazio do relatório, sem alterar estrutura. |
| 97 / 34 | `app/view/JS/relatorio-projeto.js:49` | `Erro ao carregar lista.` | `Não foi possível carregar o relatório de projetos.` | Mensagem identifica a consulta que falhou. |
| 98 / 32 | `app/view/JS/editar-doador.js:185` | `Ocorreu um erro no servidor ao salvar. Verifique o console.` | `Não foi possível salvar os dados do doador. Tente novamente.` | Mesma orientação de tarefa aplicada à edição de doador; logs preservados. |

### 7.4. Análise técnica e preservação de valores

- As cinco telas de cadastro e as cinco listagens agora têm títulos de aba
  coerentes e preposições corretas. As abas de edição identificam um registro,
  sem exigir plural. “Lista de Projetos” foi alinhado às outras listagens.
- “Pessoa de Contato” é distinto de telefone e está consistente entre cadastro
  e edição de doadores/fornecedores. As mensagens que derivam seus rótulos
  continuam usando o mesmo mecanismo de validação e asteriscos.
- CPF/CNPJ foram padronizados nos dois scripts ativos PF/PJ; máscaras,
  visibilidade, valores e regras das pessoas física/jurídica não mudaram.
- Situação Cadastral identifica filtros já vinculados a ativo/inativo. Andamento
  do Projeto identifica o campo já ligado a planejamento/execução/conclusão.
  Não houve unificação dos campos nem mudança das opções.
- Os placeholders de situação têm `value` explícito, preservado. Nenhuma opção
  sem `value` foi reescrita. Meios de recebimento, tipos e andamentos enviados
  como texto permanecem exatamente iguais, inclusive capitalização/acentuação.
- “Valor (R$)” corresponde à célula já formatada como moeda. Não foi alterado
  tratamento de doação não monetária, quantidade, unidade ou cálculo de relatório.
- “Doações por Projeto” é apenas singular no título; “Total Arrecadado” e o
  critério da soma continuam pendentes de validação e não foram alterados.
- Os literais ativar/desativar são usados exclusivamente em confirmações. Os
  testes compararam os requests anteriores/atuais e confirmaram os mesmos IDs,
  endpoints e status 0/1. Nenhum nome de variável foi modificado. A confirmação
  duplicada de projeto é um problema funcional preexistente e não foi removida.
- Prefixos de erro deixaram de citar o banco; detalhes complementares do backend
  e logs continuam disponíveis. As chaves `msg`, `message`, `status`, `success`
  e demais variantes existentes não foram uniformizadas nem renomeadas.
- Reset de edição permanece reset. O JavaScript preenche `.value`, não estabelece
  todos os dados carregados como defaults do HTML; portanto, o botão não podia
  prometer cancelamento/navegação ou restauração do registro do banco.
- “Projeto (opcional)”, opção Sem projeto, doador obrigatório/existente, envio de
  NULL, LEFT JOIN, preservação do valor na edição e controles de erro das doações
  continuam intactos. O diagnóstico é histórico e contém uma orientação anterior
  para não estender opcionais à doação; a solicitação posterior da cliente já
  autorizou essa funcionalidade, que não foi revertida.

### 7.5. Testes de caixa branca e resultados

| Teste | Resultado |
| --- | --- |
| Auditoria de substituições planejadas | Snapshot anterior da etapa comparado com a aplicação das 98 substituições exatas. Nenhuma mudança inesperada nos fontes. |
| Tags/atributos HTML | Sequências de tags comparadas, permitindo apenas os placeholders de contato previstos: estrutura, IDs, names, values, tipos, rotas, atributos de obrigatoriedade e handlers preservados. |
| Valores de opções | **106 opções estáticas** nas páginas alteradas comparadas pelo valor efetivo: `value` explícito ou texto quando implícito. Nenhuma diferença; trechos PHP dinâmicos também preservados. |
| Sintaxe PHP | `php -l` nos **22 PHP** de aplicação alterados: aprovado. |
| Sintaxe JavaScript | `node --check` nos **12 JS** de aplicação alterados e nos dois roteiros de teste: aprovado. |
| `node tests/er03-terminologias.cjs` | Aprovado: **30 cenários** de ativação/desativação com sucesso, erro e cancelamento, nos cinco módulos. Payloads e checkbox preservados. |
| Comparação com snapshot no teste ER-03 | Executado com `ALMA_ER03_BASELINE` apontando ao snapshot temporário; mesmos requests, IDs, status e estados antes/depois. |
| PF/PJ | **4 cenários** nos scripts de cadastro/edição: CPF/CNPJ, asteriscos e visibilidade mantidos. |
| JSON inválido na edição de doador | Mensagem da tarefa apresentada; mesmo endpoint, sem redirecionamento indevido. |
| `php tests/doacao-projeto-opcional.php` | Aprovado: 27 cenários com mocks e duas consultas capturadas, sem gravação real. |
| `php tests/doacao-insercao-pdo.php` | Aprovado: parâmetros NULL e registro de erro, conexão simulada. |
| `node tests/doacao-doador-obrigatorio.cjs` | Aprovado: seleção de doador e bloqueio de ID inválido em DOM simulado. |
| `node tests/doacao-projeto-opcional.cjs` | Aprovado: edição, listagem e obrigatoriedades preservadas, com mocks. |
| `php tests/er02-sem-banco.php` | Aprovado: tipo/categoria independentes e fornecedor/projeto opcionais das despesas. |
| `node tests/er02-formulario.cjs` | Aprovado: categoria dinâmica e opcionais preservados. |
| `node tests/doacao-integracao-isolada.cjs --browser --er03` | **26 verificações aprovadas**, com HTTP/PDO e Chrome real: inclui os 21 cenários existentes e 5 verificações adicionais da ER-03 descritas abaixo. |
| `git diff --check` | Aprovado. |

#### Navegador e integração com gravação isolada

A suíte usa cópia temporária dos fontes, servidor PHP em loopback, autenticação
de fixture, PDO SQLite com FKs e dados fictícios. Nenhum registro do MariaDB real
foi utilizado para gravação. SQLite não equivale a homologação de escrita em MariaDB.

O Chrome disponível foi usado sem instalar ferramentas, pela execução fora do
sandbox já autorizada para este roteiro. Não foi instalado Playwright nem pacote
adicional. Os 21 cenários anteriores confirmam cadastro e edição com/sem projeto,
rejeição de doador inexistente, obrigatoriedades, erro de FK, NULL e listagem.
As cinco verificações adicionais foram:

1. Títulos de doação e FormData **sem projeto**: IDs, tipo, valor, forma e data
   iguais aos esperados antes do POST; persistência NULL confirmada no SQLite.
2. Mesmo cenário **com projeto**: vínculo 7 preservado, sem mudança de payload.
3. Reset nativo do cadastro continua limpando os campos visíveis, sem gravar.
4. Modal de tipo continua abrindo/fechando com os mesmos identificadores.
5. Cadastro de despesas exibe o plural autorizado, com nomes/valores de tipo e
   categoria preservados, categoria dinâmica e fornecedor/projeto não obrigatórios.

Na primeira execução ampliada, o teste supôs incorretamente que reset limparia
também o ID hidden. A observação no navegador mostrou que atribuir `.value` a um
input hidden atualiza seu default; o reset preserva esse default. A expectativa
foi ajustada para comparar com `defaultValue`, sem alterar o formulário. O nome
visível fica vazio e sua validação continua impedindo um envio sem doador válido.
A rodada seguinte passou os 26 cenários. Não houve regressão funcional introduzida.

Artefatos temporários da rodada aprovada:

```text
C:\Users\jvdon\AppData\Local\Temp\alma-integracao-38geHf
```

Contêm resultado JSON, logs, screenshot e SQLite fictício. Podem ser removidos
pelo sistema; os roteiros no projeto permitem repetir os testes. O snapshot de
comparação é temporário; o teste ER-03 também roda sem ele e verifica os contratos
de status/fluxo com fixtures. Não se declara validação de login real, integrações
externas ou de todos os cadastros em navegador.

### 7.6. Situação dos itens pendentes

| Grupo do diagnóstico | Situação atual |
| --- | --- |
| 01–11 | Correções editoriais atendidas pelas duas etapas, inclusive os filtros e contato. |
| 12 | Scripts ativos de cadastro/edição PF/PJ atendidos. `mascara-doacao.js` legado não é carregado pelo cadastro atual e referencia elementos/variáveis sem integração comprovada; não foi alterado para evitar tratar uma pendência técnica como copy funcional. |
| 13–16 | Resets, títulos de modais, preposições, plurais e listagens atendidos. Reset não foi transformado em cancelamento. |
| 17 | Corrigidas preposições/plural no menu. Listar/Nova já são coerentes entre módulos e foram mantidos; renomear o procedimento de conciliação depende da cliente. |
| 18 | Mantida a apresentação com iniciais maiúsculas conforme escolha visual autorizada; não aplicada a sugestão de caixa baixa do diagnóstico inicial. |
| 19–23 | Mensagens, situação cadastral e andamento atendidos sem mudar seus valores. |
| 24–27 | Dependem da cliente: confirmação financeira/cadastro, receitas/saldo, conciliação e significado do valor do projeto. Mantidos. |
| 28 | Título de agrupamento corrigido; métrica Total Arrecadado e critério de reconhecimento continuam pendentes da cliente. |
| 29 | Data da doação versus data efetiva de recebimento: depende da cliente. |
| 30 | Cabeçalho monetário corrigido. Quantidade/unidade/valoração de doação não monetária continua fora do escopo. |
| 31 | Documentos por etapa (nota fiscal, recibo, comprovante): depende da cliente; mantidos. |
| 32–35 | Textos seguros de erros, estados vazios e confirmações atendidos; código de tratamento, conteúdo complementar e payloads preservados. |
| 36–38 | Envio simulado de recibo, CPF de fornecedor e finalidade da tela estática de detalhes: dependem de validação/integração. Mantidos. |

Fixa/Variável continua sendo a definição provisória da ER-02, sujeita à cliente.
Também permanecem questões funcionais fora da ER-03: ícones sem ação, confirmação
duplicada, estrutura de HTML de estados vazios, inconsistências de tipos exibidos
como ID, contratos JSON e integrações dos protótipos. Nenhum desses problemas
foi introduzido ou corrigido por alteração de regra nesta rodada.

### 7.7. Resultado e controle

**ER-03 — Implementação técnica concluída. Validação de terminologias específicas pela cliente pendente.** As correções textuais seguras identificadas para os
fluxos ativos foram concluídas e testadas. Itens condicionais/técnicos estão
registrados, sem impedir a entrega do lote editorial.

Não houve escrita em dados reais do MariaDB, mudança de esquema, execução de
migrações, instalação de ferramenta ou commit. Apenas o relatório da ER-03 foi
atualizado; os demais relatórios e o diagnóstico permanecem intactos.

## 8. Conclusão técnica e encerramento da ER-03

**ER-03 — Implementação técnica concluída. Validação de terminologias específicas pela cliente pendente.**

O problema identificado foi a falta de consistência entre títulos, rótulos,
instruções, botões e mensagens dos módulos, incluindo textos copiados de outras
telas e termos que confundiam finalidade, situação cadastral e andamento.
As duas etapas implementaram **133 correções textuais em 40 arquivos distintos
da aplicação**, preservando regras de negócio, identificadores, contratos e
valores enviados ao backend, as funcionalidades das ER-01/ER-02 e o cadastro
de doações com projeto opcional.

Os testes de caixa branca registrados verificaram sintaxe, substituições
planejadas, estrutura HTML, preservação dos valores de **106 opções**, payloads,
fluxos de sucesso/erro/cancelamento, CPF/CNPJ e mensagens dinâmicas. Os seis
roteiros de regressão existentes foram aprovados; o roteiro específico da ER-03
aprovou 30 cenários de ativação, quatro PF/PJ e o cenário de JSON inválido.

A integração HTTP/PDO e a automação em Chrome aprovaram **26 verificações**,
com gravações fictícias em SQLite isolado e confirmação de cadastro com/sem
projeto, rejeição de doador inválido, listagem e preservação dos formulários.
Esses resultados não são apresentados como gravação em MariaDB real: os limites
de motor, autenticação de fixture e cobertura estão documentados na seção 7.5.
Não houve regressão funcional identificada nos cenários executados.

Neste encerramento, os resultados foram confirmados por leitura das evidências
já existentes, sem repetir a suíte. O arquivo `resultados.json` no diretório
de artefatos registrado na seção 7.5 contém 26 resultados, `failed: false`,
`isolated: true` e `browserEnabled: true`. Os demais resultados aprovados são
os registrados nas tabelas de testes das duas etapas; não constituem novas
execuções nesta finalização documental.

Permanecem exclusivamente registradas para decisão da cliente as terminologias
de receita/saldo/arrecadação, confirmação financeira versus situação cadastral,
conciliação, valor do projeto, datas de doação/recebimento, documentos por etapa,
comunicação de recibos simulados, CPF de fornecedor e finalidade da tela estática
de detalhes. A definição provisória Fixa/Variável e as limitações técnicas já
descritas continuam preservadas, sem alteração de significado por suposição.

O escopo técnico autorizado da ER-03 está encerrado. Esta finalização alterou
somente este relatório, preservando o diagnóstico, os relatórios anteriores,
os testes e a aplicação. Não houve operação no banco, migração, instalação de
ferramenta, nova revisão geral ou commit.
