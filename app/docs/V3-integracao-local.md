# Integração local das melhorias na V3

## Segurança e histórico

Destino: C:/xampp/htdocs/almapantaneira_v3, branch main. A integração começou com a árvore limpa e alinhada à referência local origin/main. HEAD permanece da17166e6cb118655fd6ffd178dd404e905459eb; os três commits anteriores foram preservados. Nenhum commit, push, fetch, pull, merge, mudança de branch ou remoto foi executado nesta integração.

O backup anterior ao fast-forward foi novamente conferido por hashes. Um backup adicional dos 277 arquivos locais, sem .git, foi criado e validado em %LOCALAPPDATA%/AlmaPantaneira/backups/v3-pre-integracao-2026-11-02T21-23-05-957Z. ACL restrita ao usuário atual e SYSTEM. Credenciais e dados cadastrais presentes no backup não foram transcritos para relatórios.

## Integração e conciliação

Foram trazidos 95 arquivos diferentes/novos da preparação; três arquivos compartilhados foram conciliados: cadastro-despesa.js, cadastro-despesa.php e forms.css. Os três arquivos de componentes/script adicionados pelos commits remotos foram mantidos e corrigidos. Configurações seguras, relatórios históricos, testes, migrações e esquema sem registros foram preservados.

Os modais de projeto e fornecedor permanecem na tela de despesa. O caminho incorreto componets foi corrigido para components. O script vazio formulario-cadastro-projeto.js passou a carregar e submeter ambos os formulários, tratar falhas, atualizar as opções e selecionar o registro novo sem apagar o preenchimento da despesa. O modal não fecha ao clicar dentro do formulário; fecha pelo botão, fundo ou Escape quando não está salvando. Após confirmação de cadastro, uma falha na atualização das opções não permite reenviar inadvertidamente a mesma gravação.

O componente de fornecedor deixou de carregar cabeçalho/rodapé ou incluir a si próprio. O de projeto passou a conter os campos já exigidos pelo cadastro existente, sem inventar uma modalidade de cadastro incompleto. IDs internos receberam prefixos para não colidir com os da despesa; names e contratos dos endpoints foram preservados. A consulta de CNPJ pode ser inicializada no formulário dinâmico e mapeia os campos dentro desse formulário. O autocomplete atualiza seu conjunto de opções sem duplicar listeners. O CSS dos commits remotos foi mantido com as correções necessárias à exibição do modal.

Foram mantidos: cadastro de categoria por modal, tipo/categoria independentes, fornecedor/projeto opcionais em despesas, projeto opcional em doações, validação de doador, endereço por doação individual e número textual, CNPJ não bloqueante, terminologias ER-03 e captura de comprovantes pela câmera.

## Testes e correções verificadas

- Sintaxe: 178 PHP e 46 JavaScripts aprovados.
- Dez scripts automatizados aprovados, incluindo configuração de banco/SMTP sem conexões, PDO simulado, doação opcional, ER-02/ER-03, endereço e CNPJ com mocks.
- Chrome/HTTP/PDO com SQLite temporário: 41 verificações aprovadas. Quatro verificações adicionais cobrem projeto completo/obrigatoriedade, fornecedor com CNPJ simulado e S/N, categoria dinâmica e gravação da despesa vinculada aos três registros.
- Evidência de integração: %LOCALAPPDATA%/Temp/alma-integracao-yr81OR/resultados.json. Resumo em V3-integracao-verificacoes.json.
- O teste inicial identificou o mapeamento por ID incompatível com campos prefixados do modal; o componente CNPJ foi corrigido e seus testes anteriores continuaram aprovados.
- A fixture SQLite foi ampliada com as colunas reais necessárias aos novos cenários e função LAST_INSERT_ID na conexão temporária. Essa compatibilidade existe apenas na cópia de testes; o código de produção continua usando MariaDB normalmente.
- Finais de linha inconsistentes e espaços apontados na integração foram normalizados. git diff --check e a checagem do índice aprovados.
- Nenhuma gravação em MariaDB real, migração, envio SMTP ou instalação. Câmera física e operação com credenciais reais não foram testadas.

## Dados sensíveis

O dump cadastral e 37 uploads foram retirados do rastreamento com git rm --cached, mantendo todos os 38 arquivos locais com hashes idênticos e backup privado verificado. Estão protegidos pelas regras de ignore. Isso deixa 38 remoções do índice preparadas, sem excluir dados do disco; as demais mudanças ainda não foram adicionadas ao staging.

O escaneamento dos arquivos versionáveis não encontrou os segredos literais identificados nas configurações antigas. O banco e o SMTP agora leem ambiente ou JSON externo privado; não foi criada configuração real dentro do projeto nem usado fallback para credenciais locais.

O histórico Git anterior ainda contém as credenciais antigas de banco/SMTP, o dump e uploads. Retirar arquivos do índice não apaga commits antigos. O histórico não foi reescrito, por determinação do usuário. A credencial SMTP exposta deve ser revogada/substituída pelo responsável; credenciais antigas de banco devem ser substituídas onde ainda forem válidas. Nenhum serviço externo foi alterado nesta execução.

## Pendências antes de entrega/publicação

Revisar o diff e as remoções já preparadas; configurar banco e SMTP privadamente antes de usar a aplicação com serviços reais, conforme V3-preparacao-segura.md. Na ausência dessas configurações, o acesso falha explicitamente. Conferir a estrutura do banco de destino antes de decidir sobre migrações, sem reaplicá-las automaticamente.

Tratar as credenciais expostas e decidir sobre a permanência dos dados antigos no histórico antes de compartilhar o repositório. O próximo commit não elimina a exposição histórica. Commit e envio ao GitHub dependem de nova autorização.

A V2 original e a pasta de preparação permaneceram byte a byte inalteradas, conforme hashes registrados no backup privado.
