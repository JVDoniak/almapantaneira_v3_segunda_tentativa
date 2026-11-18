# ER-03 — Diagnóstico de terminologias e UX Writing

Projeto Integrador Alma Pantaneira — Técnico em Desenvolvimento de Sistemas — SENAC  
Data da análise: 02/11/2026  
Etapa: análise estática e documentação inicial. **Nenhuma correção de código autorizada ou implementada nesta etapa.**

## 1. Problema, escopo e método

Os módulos apresentam textos copiados de outras telas, variações de nomenclatura,
capitalização e mensagens genéricas. Alguns rótulos financeiros também confundem
meio de recebimento com recibo, situação cadastral com confirmação financeira e
valor cadastrado de projeto com total de doações. Essas diferenças podem induzir
interpretações incorretas mesmo quando os campos técnicos permanecem distintos.

Foram examinados os arquivos de páginas, componentes, JavaScripts, actions e
consultas dos módulos de despesas, doações/receitas, fornecedores, doadores,
projetos, relatório por projeto e telas de conciliação/painel relacionadas.
O procedimento combinou busca de textos com leitura do contexto dos formulários,
eventos, respostas JSON e consultas. As localizações abaixo indicam linhas ou
elementos/funções identificáveis no código atual; linhas poderão mudar depois.

Não houve acesso ao banco, execução de SQL, requisições aos endpoints, alteração
de arquivos da aplicação, testes de gravação ou commits. O diagnóstico não é
auditoria contábil nem definição de política de reconhecimento de receitas.
As propostas semânticas condicionais não substituem a decisão da cliente.

Há alterações anteriores das ER-01/ER-02 no diretório de trabalho. Elas são a
base desta análise e não devem ser revertidas nem atribuídas à ER-03.

## 2. Critérios e prioridades

- **P1 — alta:** texto aponta para outro módulo, denomina incorretamente um campo
  ou descreve uma ação diferente daquela executada. Corrigir apenas após autorização.
- **P2 — média:** melhorar clareza de mensagens, uniformidade entre telas e
  orientação sobre preenchimento, sem alterar o significado funcional.
- **P3 — baixa:** ortografia, capitalização, concisão e apresentação uniforme.
- **VC — validação da cliente:** significado financeiro/administrativo não pode
  ser decidido apenas pelo texto. A prioridade indica relevância, não autorização
  para alterar. Manter esses termos até validar a decisão.
- **T — dependência técnica:** o texto está acoplado a dados, opções ou eventos;
  revisar antes de implementar. Não inclui autorização para corrigir lógica.

Convenções propostas: títulos e rótulos em caixa de frase (ex.: “Nova despesa”),
siglas preservadas (CPF, CNPJ, CEP e PIX), “E-mail”, mensagens com entidade e ação
específicas, “Não foi possível…” para falhas e pontuação final uniforme.
Termos de domínio corretos como doador, fornecedor, despesa e doação serão mantidos.

## 3. Inventário rastreável

Cada caminho é relativo à raiz da V2. A coluna de proposta representa uma revisão
futura, não uma correção já realizada. Nas linhas VC, a proposta é uma alternativa
para discussão e o texto atual deve permanecer até aprovação semântica.

| ID / prioridade | Arquivo e localização do texto | Terminologia atual | Terminologia sugerida | Justificativa | Impacto esperado / cuidado |
| --- | --- | --- | --- | --- | --- |
| 01 · P1 | `app/view/JS/cadastro-doacao.js:77,85`, erros de `buscar-doador-input.php` | “Erro ao carregar fornecedores e projetos. Veja o console (F12).”; “Não foi possível carregar fornecedores e projetos.” | “Não foi possível carregar os doadores. Tente novamente.” | O fluxo consulta doadores, não fornecedores/projetos; o console não orienta o usuário administrativo. | Mensagem corresponde ao módulo; manter tratamento de erro e logs técnicos. |
| 02 · P1 | `app/view/pages/listagem-fornecedor.php:13`, subtítulo | “Acompanhe os Fornecedores cadastrados, seus responsáveis e valores arrecadados.” | “Consulte e gerencie os fornecedores cadastrados.” | Arrecadação não é informação apresentada pela tabela de fornecedores. | Remove expectativa de informação financeira inexistente. |
| 03 · P1 | `app/view/pages/cadastro-doacao.php:100`; `app/view/pages/conciliacao-doacao.php:120,166` | “Forma de Recibo” | “Forma de recebimento” | As opções são PIX, transferência, dinheiro e cartão; recibo é documento, não meio de recebimento. A edição já usa “Forma de Recebimento”. | Harmoniza com `edicao-doacao.php:78`; alterar só rótulos, sem nomes/valores dos campos. |
| 04 · P2 | `app/view/pages/cadastro-doacao.php:102`, opção inicial de forma de recebimento | “Selecione o tipo” | “Selecione a forma de recebimento” | Evita confusão com o campo separado de tipo de doação. | Orientação específica; preservar `value`, estado desabilitado e seleção inicial. |
| 05 · P2 | `app/view/pages/listagem-doacao.php:32`, filtro Período | “Selecione o tipo” | “Selecione o período” | Placeholder copiado de filtro de tipo. | Entendimento do filtro sem alterar opções/payload. |
| 06 · P2 | `app/view/pages/listagem-doador.php:44`; `listagem-fornecedor.php:35`; `listagem-projeto.php:46`; `relatorio-projeto.php:46`; `listagem-conciliacao.php`, filtro Status | “Selecione o tipo” | “Selecione a situação” (ou “Selecione a situação cadastral”, onde aplicável) | O filtro não seleciona um tipo de entidade. | Preservar todos os valores de opções e a regra do filtro; rótulo final conforme linhas 22–24. |
| 07 · P2 | `app/view/pages/cadastro-despesa.php:140`, forma de pagamento | “Selecione a opção” | “Selecione a forma de pagamento” | Nomeia o dado solicitado, sem confundir com tipo/categoria. | Clareza de preenchimento; ER-02 permanece intacta. |
| 08 · P2 | `app/view/pages/cadastro-fornecedor.php:30,34`; `edicao-fornecedor.php:32,36` | “Nome ou Instituição” nos campos Nome Fantasia e Razão Social | “Informe o nome fantasia”; “Informe a razão social” | Dois conceitos diferentes recebiam a mesma orientação genérica. | Menos troca de dados entre campos; não alterar obrigatoriedade nem aceitar novos tipos de fornecedor. |
| 09 · P2 | `app/view/pages/cadastro-doador.php:106`; `edicao-doador.php:94` em comparação com fornecedor | “Rua” versus “Logradouro” | “Logradouro” | O campo técnico já é `logradouro` e também pode representar avenida ou outro logradouro. | Uniformidade entre cadastros sem alterar máscaras, CEP ou nomes de campos. |
| 10 · P2 | `app/view/pages/cadastro-doador.php:79`; `edicao-doador.php:66`; `cadastro-fornecedor.php:49`; `edicao-fornecedor.php:51` | “Contato na Empresa”, “Pessoa de Contato na Empresa”, “Contato” | “Pessoa de contato”; placeholder “Nome da pessoa de contato” | Distingue pessoa de contato do campo Telefone separado; evita restringir instituição à palavra empresa. | Melhor identificação do responsável pelo contato; preservar campos e fluxo PF/PJ. |
| 11 · P3 | `app/view/pages/cadastro-fornecedor.php:45`; `cadastro-doador.php:90`; `edicao-doador.php:77`; `listagem-fornecedor.php:60`; `listagem-doador.php:68` | “Email” versus “E-mail” em `edicao-fornecedor.php:47` | “E-mail” | Uma grafia para o mesmo rótulo. | Coerência visual; não renomear `email`. |
| 12 · P2 / T | `app/view/JS/cadastro-doador.js`, `configurarCamposTipo()`; `app/view/JS/mascara-doacao.js:29` | “CNPJ da Empresa” versus “CPF do Doador” | “CNPJ” e “CPF”, alinhados ao respectivo formulário | Rótulo de documento não precisa restringir pessoa jurídica a empresa. | Revisar texto gerado dinamicamente; manter alternância PF/PJ, IDs, máscaras e asteriscos. Confirmar se a máscara está efetivamente usada na tela antes de editar. |
| 13 · P1 / T | `app/view/pages/cadastro-fornecedor.php:94`; `cadastro-doacao.php:149`; `cadastro-projeto.php`, ações; `edicao-doacao.php:135`; `edicao-projeto.php`, ações | “Cancelar” em botão `type="reset"` | “Limpar formulário” | O botão restaura os valores iniciais; não realiza a navegação de cancelamento dos links de edição de fornecedor/doador. | Rótulo deve refletir o reset já existente. Na edição, documentar restauração dos dados iniciais; não mudar o evento/tipo do botão. Não substituir “Cancelar” dos modais ou links. |
| 14 · P2 | `app/view/pages/cadastro-despesa.php:195`; `edicao-despesa.php`, `#modalCategoriaDespesa`; `cadastro-doacao.php:159`, modal de tipo | “Categoria da Despesa”; “Tipo de Doação” como título de cadastro | “Nova categoria de despesa”; “Novo tipo de doação” | Explicita a ação do modal sem trocar tipo por categoria. | Mantém ER-02, IDs dos modais e inclusão/seleção dinâmica. |
| 15 · P3 | `app/view/pages/cadastro-despesa.php:18,25`; `cadastro-doacao.php:21,26`; `cadastro-fornecedor.php:10,15`; `cadastro-doador.php:10,15`; `cadastro-projeto.php:8,13` | “Cadastro Despesa”, “Cadastro De Doações”, “Gerenciar e Cadastrar …” | “Cadastro de despesa/doação/fornecedor/doador/projeto”; subtítulo “Preencha os dados para cadastrar …” | Acrescenta preposição e uniformiza ação/singular. | Títulos de aba e instruções consistentes; apenas cópia estática. |
| 16 · P3 | `app/view/pages/listagem-despesa.php:12`; `listagem-doacao.php:12`; `listagem-fornecedor.php:12`; `listagem-doador.php:12`; `listagem-projeto.php:12` | “Lista de …” versus “Listagem de Projetos” | “Despesas”, “Doações”, “Fornecedores”, “Doadores”, “Projetos” | Padroniza títulos de telas equivalentes e reduz redundância. | Navegação mais previsível; não mudar rotas nem links. |
| 17 · P3 | `app/view/components/sidebar.php:34–49`, opções de doações/despesas | “Nova Doação”, “Listar Doações”, “Conciliação Doação”; “Conciliação Despesas” | “Nova doação”, “Consultar doações”, “Conciliação de doações”; equivalentes de despesas | Consistência de caixa, verbos e preposições. | Alterar apenas nós textuais, mantendo estrutura, ícones e navegação. O significado de conciliação continua em VC, linha 26. |
| 18 · P3 | `app/view/pages/cadastro-despesa.php:41,46,58,133,138`; `edicao-despesa.php`, campos equivalentes | “Nome da Despesa”, “Tipo de Despesa”, “Categoria da Despesa”, “Data de Vencimento”, “Forma de Pagamento” | “Nome da despesa”, “Tipo de despesa”, “Categoria da despesa”, “Data de vencimento”, “Forma de pagamento” | Uniformiza caixa sem mexer na distinção da ER-02. | Apenas apresentação; manter Fixa/Variável e categorias dinâmicas como estão. |
| 19 · P3 | `app/actions/cadastro-projeto.php:35` | “Cadastrado com succeso”; “Não foi cadastrado” | “Projeto cadastrado com sucesso.”; “Não foi possível cadastrar o projeto.” | Erro ortográfico e ausência da entidade na mensagem. | Melhora retorno da operação, mantendo chaves e indicadores JSON. |
| 20 · P2 | `app/actions/cadastro-fornecedor.php:55`; `cadastro-doador.php:56`; `cadastro-despesa.php:126` | “Cadastrado com Sucesso”, “Cadastrado com Sucesso!”; “Não Cadastrado” | “[Fornecedor/Doador/Despesa] cadastrado(a) com sucesso.”; “Não foi possível cadastrar o fornecedor.” | Sucesso deve indicar qual registro foi salvo; evitar capitalização/exclamação inconsistentes. | Mensagens específicas sem modificar critérios de sucesso. |
| 21 · P2 | `app/actions/editar-fornecedor.php:23,28,83`; `editar-doacao.php:15`; `editar-projeto.php:18`; `editar-doador.php:69,120` | “Fornecedor ou Endereço não Encontrado!”, “Parametros Invalidos!”, “Doação inexistente!!”, “Projeto inexistente!!”, “Doador e Endereço atualizados com sucesso!” | “Fornecedor ou endereço não encontrado.”; “Não foi possível identificar o registro solicitado.”; “Doação não encontrada.”; “Projeto não encontrado.”; “Dados do doador e do endereço atualizados com sucesso.” | Ortografia, tom neutro e linguagem orientada à tarefa. | Manter códigos, ramos, chaves `msg` e notificações; não substituir mensagens técnicas indiscriminadamente. |
| 22 · P2 / T | `app/view/pages/listagem-fornecedor.php:33`; `listagem-doador.php:42`; `listagem-projeto.php:44`, filtros 1/0 ou ativo/inativo | “Status” / “Status do Projeto” | “Situação cadastral” | Filtro representa ativação do cadastro, não pagamento, recebimento ou andamento. | Separação administrativa; manter Ativo/Inativo e respectivos valores e eventos. |
| 23 · P2 / T | `app/view/pages/cadastro-projeto.php`, `label[for="andamento"]`; `edicao-projeto.php`, mesmo campo; `relatorio-projeto.php:69` e `app/view/JS/relatorio-projeto.js:22` | “Status” para `andamento`; filtro Status do Projeto usa Ativo/Inativo | “Andamento do projeto” para campo/coluna que exibem `andamento`; “Situação cadastral” para o filtro ativo/inativo | Planejamento/execução/conclusão e cadastro ativo/inativo são dimensões distintas já existentes. | Só alterar rótulos; manter opções textuais e consultas. Não unificar campos. |
| 24 · P1 / VC / T | `app/view/pages/edicao-doacao.php:101–105`; `listagem-doacao.php:59–63`; `app/view/JS/listagem-doacao.js`, switch; `app/actions/atualizar-status-doacao.php`; `status-doacao.php` | “Status da Doação”, “Não confirmada/Confirmada”, “Confirmado/Pendente”, ativar/desativar | **Manter até validar.** Distinguir “Situação cadastral” de “Situação do recebimento” conforme o que cada fluxo realmente representa. | Há confirmação financeira e ativação cadastral na interface; equivalência não está comprovada. | Alto risco de mudar sentido de 0/1 ou sugerir recebimento confirmado indevidamente. Não trocar valores/status nem adaptar regra nesta ER. |
| 25 · P1 / VC | `app/view/pages/dashboard.php:67,89,91,114,135`; `app/view/JS/tela-dashboard.js`, resumo/gráficos | “Receitas Confirmadas”, “Saldo do Período”, “Receitas por Projeto” versus módulo “Doações” | **Manter até validar.** Alternativas: “Doações confirmadas” e “Resultado do período”, somente se compatíveis com origem, regime e cálculo acordados. | Receita, entrada de caixa, doação e saldo não são sinônimos automáticos. Painel usa mocks; nomes não comprovam apuração contábil. | Evita apresentar saldo bancário ou resultado contábil sem definição; não alterar cálculo, datasets nem chaves. |
| 26 · P1 / VC | `app/view/pages/conciliacao-doacao.php:15,138`; `conciliacao-despesas.php:15,154`; `listagem-conciliacao.php:13`; JS de conciliação, funções de confirmação | “Conciliação”, “Confirme o pagamento”, “Confirmar Recebimento”, “conciliação de notas de entrada e saída” | **Manter até validar.** Se houver somente confirmação manual, discutir “Confirmação de recebimentos/pagamentos”; reservar “Conciliação” para o procedimento acordado. | Confirmar um registro na tela não demonstra confronto com extrato/documento; os JS atuais são protótipos. | Não afirmar que há baixa bancária ou integração real; mudança de módulo depende da cliente. |
| 27 · P1 / VC | `app/view/pages/cadastro-projeto.php`, `label[for="valor_projeto"]`; `edicao-projeto.php`, mesmo campo; `app/view/JS/editar-projeto.js`, preenchimento | “Valor” versus “Valor doado para o projeto” | **Manter até validar.** Definir se é “Valor previsto do projeto”, “Meta de arrecadação” ou “Valor cadastrado do projeto”. | No cadastro é um valor informado; a soma de doações é outra consulta. Não há evidência suficiente para escolher orçamento ou meta. | Evita transformar valor cadastrado em total arrecadado apenas pelo rótulo. Não mudar fórmulas/colunas. |
| 28 · P2 / VC | `app/view/pages/relatorio-projeto.php:8,12,13,72`; `app/model/Database.php`, `select_projetos_with_doacao()`; `app/view/JS/relatorio-projeto.js:25` | “Doações por Projetos”, “Total Arrecadado” | Para título: “Doações por projeto”. Para métrica: **validar** “Total de doações registradas” em vez de “Total arrecadado”. | Consulta usa `SUM(doacao.valor)` sem condição de confirmação financeira; não assumir recursos recebidos. | Mudança do título é editorial; métrica exige validar escopo. Não acrescentar filtro nem mudar SQL nesta etapa. |
| 29 · P1 / VC / T | `app/view/pages/listagem-doacao.php:83`; `cadastro-doacao.php:111`; `edicao-doacao.php:88`; `app/view/JS/listagem-doacao.js:26` | “Data do Recebimento” na coluna que mostra `data_doacao`; formulários usam “Data da Doação” | **Manter até validar.** Se a data for a mesma do cadastro, “Data da doação”; se representar recebimento efetivo, definir requisito próprio. | Não deduzir recebimento financeiro a partir do nome técnico `data_doacao`. | Evita associar data prevista/registrada a efetiva; não criar campo nem mudar dados. |
| 30 · P1 / T | `app/view/pages/listagem-doacao.php:85`; `app/view/JS/listagem-doacao.js:28` | “Valor / Qtd” | “Valor (R$)” para a coluna atualmente monetária; validar eventual necessidade de quantidade separadamente. | Renderização usa `formatarValor(doacao.valor)`, sem exibir quantidade ou unidade. | Alinha cabeçalho ao que é exibido; não implementar estoque, unidades ou avaliação de bens. |
| 31 · P2 / VC | `app/view/pages/cadastro-doacao.php:127`; `cadastro-despesa.php:167`; `conciliacao-despesas.php:194,228`; JS `abrirModal()` de despesas | “Anexar Nota Fiscal ou Comprovante”, “Nota Fiscal / Documento”, “Comprovante de Pagamento” | Uniformizar caixa; **validar** nomes por etapa: documento da despesa/doação versus comprovante de pagamento/recebimento. | Nota fiscal, recibo e comprovante bancário não são intercambiáveis. O cadastro pode anexar NF ou boleto; pagamento é outra etapa. | Preservar upload e foto; não trocar exigência de documento nem afirmar quitação por anexo. |
| 32 · P2 | `app/view/JS/cadastro-despesa.js:12,71`; `editar-despesa.js`, erro de JSON no envio | “Veja o console (F12)”; “Verifique o console.” | “Não foi possível carregar os dados. Tente novamente.” / “Não foi possível salvar a despesa. Tente novamente.” conforme ramo | Usuário administrativo não deve depender de diagnóstico de desenvolvedor. | Logs continuam no console; alterar só alertas, sem remover tratamento de exceção. |
| 33 · P2 / T | `app/view/JS/listagem-despesa.js`, falha no switch; `listagem-fornecedor.js:100`; `listagem-doador.js:92`; `listagem-projeto.js:120` | “Erro ao atualizar no banco de dados: …” | “Não foi possível atualizar a situação de [entidade]. Tente novamente.” | Descreve a tarefa e evita jargão do armazenamento. | Revisar mensagem concatenada; manter reversão do checkbox e logs. Não ocultar detalhes sem verificar se são necessários ao usuário. |
| 34 · P3 | `app/view/JS/listagem-doacao.js:12`; `listagem-fornecedor.js:44`; `listagem-projeto.js:59` | “Nenhuma doação Cadastrada!”, “Nenhum Fornecedor Cadastrado!”, “Nenhum Projeto Cadastrado!” | “Nenhuma doação encontrada.”; “Nenhum fornecedor encontrado.”; “Nenhum projeto encontrado.” | Uniformiza estado vazio e também serve a consultas filtradas. | Alterar só texto; problemas de HTML/colspan encontrados são técnicos e não autorizados por esta linha. |
| 35 · P2 / T | `app/view/JS/listagem-fornecedor.js:79–81`; `listagem-doador.js`, `listagem-despesa.js`, confirmações de ativação | “Deseja ATIVAR/DESATIVAR este …?” | “Deseja ativar/desativar este [registro]?” com verbo em caixa baixa | Texto mais uniforme, sem sugerir exclusão ou pagamento. | Não mudar variáveis `acao`, booleanos ou eventos; ajustar apenas composição apresentada se autorizado. |
| 36 · P1 / VC / T | `app/view/JS/conciliacao-doacao.js:200–203,232–244`, modal/toasts de recibo | “Recibo enviado para …”; “Recibo reenviado para …” | **Validar apresentação do protótipo:** “Simulação de envio de recibo…” enquanto não existir envio real; texto definitivo depende da implementação. | Os ramos apenas atualizam a tela; não há chamada de envio efetivo nessas funções. | Não prometer notificação realizada; não implementar e-mail ou novo backend na ER-03. |
| 37 · P1 / VC | `app/view/pages/listagem-fornecedor.php:28`; `cadastro-fornecedor.php:37`; `edicao-fornecedor.php:39` | “CNPJ”; campo técnico se chama `cpf_cnpj` | **Manter “CNPJ” até validar** atendimento a fornecedor pessoa física. | Nome técnico não prova suporte a CPF; cadastro/máscara atual são orientados a CNPJ. | Não ampliar formato, validação ou regra para trocar apenas um rótulo. |
| 38 · P2 / VC | `app/view/pages/visualizacao-unica-doador.php:28–29` | “Visualização de Doador”; “Gerenciar e Editar Doador” | “Detalhes do doador”; subtítulo “Consulte os dados do doador e suas doações.” após validar integração da tela | Tela de visualização não deve prometer edição sem ação correspondente; conteúdo observado é estático. | Não converter protótipo em fluxo funcional nesta ER; conferir se a tela está acessível no uso real. |

## 4. Vocabulário a preservar e decisões pendentes

| Conceito | Diretriz para a próxima etapa |
| --- | --- |
| Tipo de despesa | Preservar a ER-02: Fixa/Variável é classificação provisória, sujeita à validação da cliente. Não renomear registros, inferir tipo ou reclassificar dados. |
| Categoria de despesa | Finalidade do gasto, cadastrada dinamicamente. Não substituir por tipo nem retirar os modais. |
| Fornecedor e projeto da despesa | Permanecem opcionais pela ER-01. Manter “(opcional)” e validação apenas quando há seleção/texto informado, conforme comportamento existente. Não estender essa regra ao projeto da doação. |
| Não classificado / Não classificada | Preservar o sentido de ausência de classificação, distinto de “Inativo”, “Pendente” e “Não pago”. Não preencher NULL com rótulo no banco. |
| Receita / doação / recebimento | Definir com a cliente escopo do painel, tipos de doação e momento de reconhecimento; não fazer substituição global. |
| Ativo/Inativo | Situação cadastral, onde confirmada pelo código. Não equivaler a recebimento confirmado, despesa paga ou projeto concluído. |
| Pendente / Não confirmada / Confirmada | Definir se descrevem recebimento, conferência ou cadastro em cada tela. Não mapear automaticamente valores entre fluxos. |
| Recibo / nota fiscal / comprovante | Manter distinção entre documento de doação, documento da despesa e prova de pagamento/recebimento. Validar nomes por etapa. |
| Tipo de doação, tipo de doador e tipo de projeto | São classificações próprias. Não confundir com tipo de despesa nem trocar por categoria por uniformidade visual. |

Decisões solicitadas futuramente: significado de `valor_projeto`; alcance de
“receitas” no painel; base de “total arrecadado”; momento representado pela data
da doação; confirmação versus ativação; natureza do procedimento de conciliação;
documentos aceitos por etapa; CPF de fornecedor e comunicação dos protótipos.
Essas definições não são pré-requisitos para corrigir erros ortográficos seguros.

## 5. Preparação da implementação e inventário de arquivos

**Somente depois da autorização:** selecionar os IDs aprovados da tabela e
trabalhar por bloco. Não executar substituições globais de “tipo”, “status”,
“doação”, “receita” ou “comprovante”. Priorizar P1 editoriais (01–03, 13 e 30),
depois P2 e P3. Itens VC ficam separados até decisão da cliente. Para 30, mudar
somente o rótulo correspondente ao formato já exibido, sem decidir política de
valoração de doações não monetárias.

Inventário de candidatos, condicionado à seleção das linhas:

- **Páginas de despesas:** `app/view/pages/cadastro-despesa.php`,
  `edicao-despesa.php`, `listagem-despesa.php`, `conciliacao-despesas.php`.
- **Páginas de doações:** `app/view/pages/cadastro-doacao.php`,
  `edicao-doacao.php`, `listagem-doacao.php`, `conciliacao-doacao.php`.
- **Fornecedores e doadores:** `app/view/pages/cadastro-fornecedor.php`,
  `edicao-fornecedor.php`, `listagem-fornecedor.php`, `cadastro-doador.php`,
  `edicao-doador.php`, `listagem-doador.php`; tela de detalhes
  `visualizacao-unica-doador.php` somente após verificar seu uso.
- **Projetos e relatórios:** `app/view/pages/cadastro-projeto.php`,
  `edicao-projeto.php`, `listagem-projeto.php`, `relatorio-projeto.php`.
- **Componentes e agregados:** `app/view/components/sidebar.php`;
  `app/view/pages/dashboard.php`, `listagem-conciliacao.php` somente no escopo aprovado.
- **JavaScripts com textos identificados:** `app/view/JS/cadastro-doacao.js`,
  `cadastro-despesa.js`, `editar-despesa.js`, `cadastro-doador.js`,
  `mascara-doacao.js`, `listagem-doacao.js`, `listagem-doador.js`,
  `listagem-fornecedor.js`, `listagem-projeto.js`, `listagem-despesa.js`,
  `conciliacao-doacao.js`; `tela-dashboard.js` e `relatorio-projeto.js` apenas
  se houver aprovação para seus textos dinâmicos. Rever também os textos de
  `conciliacao-despesa.js` se a cliente definir nomenclatura documental/financeira.
- **Actions com mensagens:** `app/actions/cadastro-projeto.php`,
  `cadastro-fornecedor.php`, `cadastro-doador.php`, `cadastro-despesa.php`,
  `editar-fornecedor.php`, `editar-doador.php`, `editar-doacao.php`,
  `editar-projeto.php`. Mensagens de outras actions só entram mediante evidência
  e aprovação; não padronizar os contratos JSON.

**Arquivos de apoio lidos, sem necessidade prevista de alteração funcional:**
`app/actions/cadastro-doacao.php`, `editar-despesa.php`, `relatorio-projeto.php`,
`atualizar-status-doacao.php`, `status-doacao.php`, controllers `Doacao.php`,
`Projeto.php`, `Despesa.php`, `TipoDespesa.php` e `app/model/Database.php`.
Consultar serviu para entender a origem dos rótulos, não para propor mudanças de SQL.
`upload-comprovante.js`, `camera-documento.js`, scripts de modais e
`editar-doador.js` são pontos de regressão, sem alteração obrigatória identificada.

## 6. Dependências de JavaScript e backend

1. **Opções sem `value`:** em forma de recebimento da doação, opções como
   “Transferência Bancária”/“PIX”/“Dinheiro”/“Cartão” não têm `value` explícito.
   Mudar seu texto altera o valor enviado. `editar-doacao.js` preenche o select
   com o valor salvo. Manter essas opções intactas, inclusive capitalização, nesta
   proposta. O mesmo cuidado vale para andamento e tipo de projeto e status
   textuais. Alterar só labels externos é a alternativa segura.
2. **Opções com `value`:** preservar os valores explícitos, placeholders sentinela
   (`selecione`, vazio), seleção inicial, `disabled`, `hidden`, `required` e `name`.
   Não trocar placeholder por uma nova regra “Todos” sem avaliar comportamento.
3. **Textos reescritos pelo JS:** títulos/toasts da conciliação, documentos PF/PJ,
   mensagens de carregamento e células de listagem não se corrigem apenas no HTML.
   Mapear todas as atribuições de `textContent`, `innerText` e `innerHTML` afetadas.
4. **Labels usados em validação:** `cadastro-doador.js:29–31` lê o texto do label
   anterior para formar alertas. Alterar o label altera a mensagem derivada; manter
   estrutura, posição dos elementos e indicação de obrigatoriedade.
5. **Contrato JSON:** JS mostra `message`, `msg` ou `mensagem`; `success`, `sucesso`
   e `status` controlam fluxo. Alterar somente literais de mensagem aprovados,
   nunca uniformizar chaves, códigos, variáveis ou formato da resposta nesta ER.
6. **Erros técnicos:** `editar-doacao.php` e `editar-projeto.php` podem retornar
   exceções em `msg`. Esse é um ponto para análise técnica própria: não fazer uma
   reforma de tratamento de erros, logs ou segurança sob pretexto de UX Writing.
7. **Ações:** rótulos de botão não mudam `type`, navegação ou handlers. Não adicionar
   clique ao ícone Visualizar, remover a confirmação duplicada de projeto nem
   corrigir HTML de estado vazio nesta etapa de terminologia.
8. **ER-01/ER-02 e cache:** preservar opcionais, dois vínculos independentes,
   categorias legadas/NULL, selects dinâmicos, filtro de tipo, modais e parâmetro
   de versão do JS da listagem. Copy não exige SQL nem migração.

## 7. Plano de testes para a etapa autorizada

Os testes abaixo estão **planejados**, não executados nesta análise.

| Teste | Técnica / ramo | Evidência e resultado esperado |
| --- | --- | --- |
| CB-01 — revisão de diff | Caixa branca: comparar nós textuais/literais aprovados | Somente textos mudam; IDs, seletores, nomes, opções enviadas, funções, classes, SQL, condições e rotas permanecem iguais. |
| CB-02 — sintaxe | `php -l` nos PHP alterados; `node --check` nos JS alterados; `git diff --check` | Sem erros de sintaxe ou escapes em apóstrofos, templates e concatenações. Não executar SQL. |
| CB-03 — sucesso e falha | Simular respostas das actions, JSON inválido, rede indisponível, lista vazia e com dados | Mensagens apontam para entidade correta; códigos, retornos, redirecionamento e logs preservados. |
| CB-04 — domínio dos selects | Comparar opções, `.value` preenchido pela edição e FormData antes/depois | Mudar label não muda payload, inclusive opções sem `value`. Nenhuma alteração de dado histórico. |
| CB-05 — tipo de doador | Exercitar `configurarCamposTipo()` e geração de alerta com label | CPF/PJ mantém visibilidade, máscaras, obrigatoriedade e mensagem com rótulo correto. |
| CB-06 — modais | Abrir, cancelar, cadastrar categoria/tipo e repetir edição | Título correto; seletores intactos; categoria criada continua selecionada e demais campos permanecem preenchidos. |
| CB-07 — ativação | Cobrir confirmação, cancelamento, sucesso e falha de switch com backend simulado | Mantém booleano enviado, reversão do checkbox e estado; não passa a indicar pagamento/recebimento. |
| CB-08 — regressão ER-01 | Despesa com fornecedor/projeto vazios, selecionados e texto sem seleção | Mesma aceitação de opcionais e rejeição de texto inválido; não mudar regras da doação. |
| CB-09 — regressão ER-02 | Tipo Fixa/Variável/NULL, categoria dinâmica/legada e filtro combinado | Sem reclassificação; edição, listagem e conciliação mantêm distinção, ordem e significado dos campos. |
| CB-10 — ação real do botão | Disparar reset, fechar modal, link de cancelamento e limpar filtro | Texto corresponde à ação existente; campos e navegação não mudam. |
| UX-01 — revisão visual | Navegador desktop/móvel, zoom e leitores de rótulos | Sem cortes nos textos; siglas consistentes; título, label, tooltip e alerta não se contradizem. |
| UX-02 — validação semântica | Revisão da cliente/área financeira dos itens VC | Registrar decisão, termo aceito e escopo. Nenhum termo dependente de validação aplicado por suposição. |

Priorizar fixtures/mocks para verificar mensagens e contratos. Se testes com
gravação forem posteriormente autorizados, usar homologação e não produção.
Os scripts de regressão já existentes da ER-02 podem ser reutilizados; adicionar
casos só quando sustentarem comportamento relevante, sem testes que apenas
reproduzam um literal novo.

## 8. Resultados desta etapa e registro para o SENAC

- Produzido inventário com **38 grupos de ocorrências**, localização, proposta,
  justificativa, impacto, prioridade e sinalização de dependências.
- Identificados erros editoriais corrigíveis e questões de domínio separadas
  para decisão da cliente, evitando transformar copy em mudança de negócio.
- Mapeados acoplamentos entre labels, opções de select, FormData, JSON e textos
  dinâmicos, com plano de testes de caixa branca e de regressão das ER-01/ER-02.
- Não foi medida melhoria de usabilidade nem declarado teste funcional aprovado:
  nesta etapa o resultado é documental, baseado em análise estática de código.
- **Único arquivo criado nesta etapa:** `docs/ER-03-diagnostico.md`.
  Nenhum arquivo existente foi modificado por esta atividade; banco e dados
  permaneceram intocados e nenhum commit foi realizado.

Para o relatório técnico posterior, vincular cada mudança autorizada ao ID deste
inventário, registrar texto anterior/posterior, arquivo/linha efetiva, justificativa,
teste executado, evidência e resultado. Manter resultados ainda não verificados
como pendentes. **Aguardar autorização antes de alterar o código.**
