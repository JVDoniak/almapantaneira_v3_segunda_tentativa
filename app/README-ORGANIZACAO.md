# Organização do projeto (PHP puro)

Todo o projeto está na pasta `app/`: `actions/`, `controller/`, `model/`, `utils/`, `view/`, `public/`, `docs/`, `tests/`, `index.php` e arquivos Composer.

- `vendor/` foi excluído do pacote. Dentro de `app/`, execute `composer install` para restaurar a dependência PHPMailer definida em `composer.json`/`composer.lock`. Composer gerencia a dependência; a aplicação permanece em PHP puro, sem framework.
- O SQL consolidado para **instalação nova em banco vazio** é `docs/schema/estrutura-sem-dados.sql`. Contém a tabela `tipo_despesa`, sua relação com `despesa`, os dois valores de referência e `endereco.numero VARCHAR(20)`. Os scripts SQL de migração separados foram retirados do pacote para não serem aplicados novamente por engano; documentos de histórico foram preservados.
- **Não importe o schema consolidado sobre um banco existente**. Ele não substitui um processo de migração de dados em produção. Faça backup e valide a estrutura real antes de alterar bancos existentes.
- O dump privado `model/backup.sql` não foi incluído na distribuição.
- Ponto de entrada: `app/index.php`; configure o servidor para apontar à pasta `app/` ou acesse `/app/index.php`.
- As rotas relativas foram ajustadas para `public/` estar dentro de `app/`. Verifique integração e banco no ambiente PHP/MySQL real antes de publicar.
