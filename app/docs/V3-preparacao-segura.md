# Preparação segura da V3

## Resultado

Cópia preparada em C:\xampp\htdocs\almapantaneira_v3_preparacao, sem integração ao GitHub e sem pasta .git. A V2 e o repositório existente em C:\xampp\htdocs\almapantaneira não foram alterados.

Foram copiados **262 arquivos** da origem; **38 arquivos** foram excluídos da cópia (dump cadastral e 37 uploads, incluindo comprovantes e fotos de perfil). Dependências locais de vendor foram preservadas sem instalação. As pastas de uploads permanecem vazias, com .gitkeep. Nenhuma credencial real foi gravada na preparação.

## Adaptações de segurança

- app/model/Database.php: remove credenciais antigas comentadas e valores locais; mantém a classe, métodos e conexão PDO, lendo DB_HOST, DB_NAME, DB_USER e DB_PASS.
- app/utils/configEmail.php: mantém as mesmas chaves do array consumido por Email.php, lendo SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_FROM e SMTP_FROM_NAME.
- app/utils/configLocal.php (novo): lê ambiente ou JSON privado externo. Rejeita arquivo interno ao projeto e configuração obrigatória ausente. Não possui credenciais padrão.
- .env.example: somente exemplos fictícios; não é carregado automaticamente. .gitignore: protege ambientes, configurações locais, dumps, bancos, logs e uploads; preserva migrações.
- tests/endereco-numero-mariadb.php: remove conexão local literal; exige configuração e ALMA_MIGRATION_CONFIRM=ENDERECO_NUMERO. A ferramenta foi preservada, mas NÃO executada nesta entrega.
- tests/doacao-integracao-isolada.cjs: fornece somente configurações fictícias ao servidor e provisionamento isolados.
- conciliacao-doacao.js e conciliacao-despesa.js: anonimização de identidades dos mocks de demonstração. Comportamentos e valores de negócio mantidos.
- editar-perfil2.js: remoção de logs que imprimiam senhas. Três páginas (edicao-perfil, listagem-usuario e recuperacao-senha): e-mails de exemplo substituídos por domínio reservado.
- app/model/backup.sql não foi copiado. docs/schema/estrutura-sem-dados.sql contém somente os CREATE TABLE do SQL versionado, sem dados, sem DROP e com numero VARCHAR(20). É referência para banco vazio, não migração de banco existente. Não foi executado e não substitui backup/restauração.

## Configuração no XAMPP

Escolher uma das opções abaixo, sem gravar credenciais na pasta de preparação:

1. Definir as variáveis DB_* e SMTP_* no ambiente do Apache/processo PHP.
2. Criar manualmente um JSON privado **fora de htdocs**, por exemplo C:/xampp/private/alma-config.local.json, e configurar no Apache: SetEnv ALMA_CONFIG_FILE "C:/xampp/private/alma-config.local.json". Restringir a leitura ao usuário do Apache/administrador e reiniciar o Apache depois de configurar.

Exemplo fictício de JSON (não utilizar estes valores para conexão real):

~~~json
{
  "DB_HOST": "localhost.exemplo.invalid",
  "DB_NAME": "banco_exemplo",
  "DB_USER": "usuario_exemplo",
  "DB_PASS": "substituir_localmente",
  "SMTP_HOST": "smtp.exemplo.invalid",
  "SMTP_PORT": 587,
  "SMTP_USER": "usuario_exemplo",
  "SMTP_PASS": "substituir_localmente",
  "SMTP_FROM": "remetente@example.invalid",
  "SMTP_FROM_NAME": "Alma Pantaneira"
}
~~~

Variáveis do processo têm precedência sobre o JSON. Senha vazia somente será aceita se explicitamente configurada; não há fallback automático para root ou para banco real. Sem configuração, banco e e-mail falham explicitamente; isso é intencional. Nenhum arquivo privado real foi criado nem o Apache foi reconfigurado nesta execução.

## Verificações

- Sintaxe: **176 PHP e 45 JavaScripts**, aprovados, incluindo as dependências existentes.
- tests/v3-configuracao-segura.php: ambiente, JSON externo, precedência, configuração ausente e rejeição de caminho interno aprovados; Database usa uma subclasse sem conexão e SMTP somente verifica o array de configuração.
- Dez scripts de teste aprovados: configuração segura, inserção PDO simulada, projeto opcional PHP/JS, ER-02 PHP/JS, ER-03, doador obrigatório e requisitos 6 e 7.
- Integração: **37 verificações aprovadas**, com Chrome, SQLite temporário e API CNPJ simulada. Evidência externa: pasta alma-integracao-2mILLI em %LOCALAPPDATA%/Temp. Nenhum banco de produção foi utilizado.
- Presença das funcionalidades preservada por arquivos e regressões: modais, fornecedor/projeto opcionais, tipo/categoria independentes, endereço por valor e número textual, consulta CNPJ e textos ER-03. Câmera preservada por comparação do código; não houve teste em aparelho físico.
- Escaneamento dos arquivos preparados: nenhum segredo literal identificado na configuração original permaneceu; sem dump cadastral, uploads reais, bancos locais, logs ou histórico .git. Dados sintéticos de testes e demonstração permanecem.
- Inventário de hashes confirma todos os arquivos da V2 inalterados. Relatórios anteriores e migrações foram preservados; evidências resumidas em v3-preparacao-verificacoes.json.

## Pendências para integração futura

Obter autorização para integrar ao repositório correto; fornecer configurações privadas do ambiente e definir o banco de destino. Não reaplicar automaticamente migrações já existentes. As rotas atuais que pressupõem /almapantaneira foram preservadas: mapear a aplicação para esse caminho ou revisar o mapeamento na implantação, sem apontar para a pasta V3 existente.

Nenhuma credencial externa foi revogada ou alterada; a credencial SMTP e as antigas credenciais de banco já expostas no histórico original continuam exigindo tratamento pelo responsável. Não importar esse histórico para a V3. Não foram realizados commits, instalação, alteração em banco real ou execução de migração.
