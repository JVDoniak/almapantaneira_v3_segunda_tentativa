# Requisito 7 — Consulta automática de CNPJ

## 1. Problema identificado

Dados de pessoas jurídicas eram preenchidos manualmente. Os formulários compatíveis são cadastro e edição de fornecedores e de doadores pessoa jurídica. Doações selecionam um doador existente e não precisam de consulta própria.

## 2. Solução implementada

Consulta direta à OpenCNPJ após saída do campo, com validação dos dígitos verificadores do CNPJ numérico. Somente campos existentes e compatíveis são preenchidos. Todos continuam editáveis; dados manuais anteriores ou alterados durante a consulta são preservados.

CNPJ inválido, não encontrado, resposta incompatível, limite de requisições, indisponibilidade e timeout apresentam orientação, sem impedir o cadastro manual. Consultas antigas são canceladas ou ignoradas. CPF não provoca consulta.

## 3. Principais arquivos modificados

- `app/view/JS/consulta-cnpj.js`: novo componente compartilhado.
- `app/view/pages/cadastro-doador.php` e `edicao-doador.php`: inclusão do componente para pessoa jurídica.
- `app/view/pages/cadastro-fornecedor.php` e `edicao-fornecedor.php`: inclusão do componente.
- `tests/requisito7-cnpj.cjs`: testes unitários e mocks.
- `tests/doacao-integracao-isolada.cjs`: testes no navegador com interceptação da API.

## 4. Explicação resumida do código

O componente consulta `GET https://api.opencnpj.org/{cnpj}` com timeout de oito segundos, sem credenciais e sem referenciador. Mapeia razão social, nome fantasia conforme o formulário, endereço, e-mail e telefone compatível. Não utiliza quadro societário ou outros dados sem campo correspondente, nem registra a resposta integral.

O número do endereço somente é preenchido quando compatível com o `TINYINT` atual; caso contrário há aviso para preenchimento manual. O estado retornado como UF é associado ao nome utilizado pelos formulários. Máscaras e validações existentes permanecem ativas.

Referência primária consultada: [contrato do worker OpenCNPJ](https://github.com/Hitmasu/OpenCNPJ/blob/main/src/Worker/README.md). Consulta pública de leitura ao endpoint confirmou HTTP 200 e CORS permissivo. Essa verificação de disponibilidade não foi usada como substituta dos testes com mocks.

## 5. Testes realizados

- `node tests/requisito7-cnpj.cjs`: aprovado. Cobertura de dígitos verificadores, quatro formulários, mapeamento, preservação manual, ausência de consulta para CPF, HTTP 404/429/503, timeout, resposta atrasada, CNPJ divergente e números incompatíveis. Nenhuma consulta externa nesses testes.
- Chrome automatizado com API interceptada: preenchimento com máscaras e estado; CNPJ não encontrado preservando dados manuais; indisponibilidade sem bloqueio ou limpeza do formulário. Todos aprovados.
- Execução conjunta `node tests/doacao-integracao-isolada.cjs --browser --er03 --requisitos`: **37 verificações aprovadas**, incluindo os requisitos anteriores e requisito 6. Evidência: `C:\Users\jvdon\AppData\Local\Temp\alma-integracao-WKsMO4\resultados.json`.
- Sete scripts anteriores de regressão e o teste unitário do requisito 6 aprovados; sintaxe PHP e JavaScript dos arquivos desta execução aprovada.
- As gravações da integração ocorreram somente em SQLite temporário. Nenhum registro real de MariaDB foi alterado; nenhuma migração, instalação ou commit foi realizado.

## 6. Resultado final e limitações

Consulta implementada nos quatro formulários existentes, com tratamento não bloqueante de falhas. O serviço recebe apenas o CNPJ consultado; não são transmitidos dados pessoais adicionais do formulário.

O suporte atual é ao CNPJ numérico de 14 dígitos, coerente com máscaras e normalização existentes. CNPJ alfanumérico exige adaptação coordenada dessas validações e do backend, não realizada nesta entrega. A limitação da coluna `endereco.numero`, detalhada no relatório do requisito 6, também pode impedir salvar determinados endereços; não foi alterada a estrutura sem autorização.
