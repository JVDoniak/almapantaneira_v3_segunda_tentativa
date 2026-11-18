# Requisito 6 — Endereço do doador

## 1. Problema identificado

O cadastro exigia endereço de todos os doadores, independentemente do valor de suas doações. A exigência solicitada é por doação individual igual ou superior a R$ 1.000,00, e não pelo total acumulado do doador.

## 2. Solução implementada

O doador pode ser cadastrado e editado sem endereço. Quando o grupo de endereço é preenchido, os campos necessários devem estar completos; complemento permanece opcional. Cadastro e edição de doações validam no servidor o endereço do doador a partir de R$ 1.000,00. Doações inferiores não exigem endereço, mesmo quando a soma ultrapassa esse limite. A edição do doador impede remover o endereço quando existe uma doação individual nesse patamar.

O formulário de doação orienta a completar o endereço no cadastro do doador. Não foram criados campos duplicados nem reclassificados registros históricos.

## 3. Principais arquivos modificados

- `app/controller/RegraEnderecoDoador.php`: nova validação compartilhada.
- `app/actions/cadastro-doador.php` e `editar-doador.php`: endereço opcional e atualização pelo vínculo efetivo do doador.
- `app/actions/cadastro-doacao.php` e `editar-doacao.php`: aplicação da regra pelo valor individual.
- `app/view/pages/cadastro-doador.php`, `edicao-doador.php`, `cadastro-doacao.php` e `edicao-doacao.php`: rótulos e orientações.
- `app/view/JS/cadastro-doador.js`: validação opcional do endereço e consulta de estados somente quando há um seletor compatível.
- `app/view/JS/mascara-validacao.js`: remoção de chamada duplicada a função inexistente, encontrada no teste de navegador dos formulários de doador.
- `tests/requisito6-endereco.php`, `tests/doacao-projeto-opcional.php` e `tests/doacao-integracao-isolada.cjs`: cobertura da regra e adequação das fixtures.

## 4. Explicação resumida do código

Valores normalizados são convertidos em centavos inteiros antes da comparação. A validação consulta o endereço por `LEFT JOIN`, sem tornar o vínculo obrigatório para valores menores. O backend aceita `id_endereco = NULL`; não cria endereços vazios. A atualização utiliza o endereço recuperado no servidor, sem confiar no identificador enviado pelo formulário.

A estrutura real foi inspecionada somente por leitura: `doador.id_endereco` permite `NULL`. Não é necessária migração para tornar o endereço opcional.

## 5. Testes realizados

- `tests/requisito6-endereco.php`: 15 verificações aprovadas, incluindo limite em centavos, precisão inválida, endereço vazio, parcial e completo.
- Integração HTTP com PDO/SQLite temporário: cadastro sem endereço; doações de R$ 999,99 e R$ 700,00 sem exigência cumulativa; rejeição de R$ 1.000,00 e R$ 1.200,00 sem endereço; edição de doação; inclusão posterior de endereço; bloqueio de remoção após doação alta; rejeição de endereço parcial e número incompatível.
- Chrome automatizado: cadastro de doador sem endereço aprovado, com gravação somente no SQLite isolado.
- Execução conjunta `node tests/doacao-integracao-isolada.cjs --browser --er03 --requisitos`: **37 verificações aprovadas**, incluindo regressões anteriores e requisito 7. Evidência: `C:\Users\jvdon\AppData\Local\Temp\alma-integracao-WKsMO4\resultados.json`.
- Regressões aprovadas: `doacao-projeto-opcional.php`, `doacao-insercao-pdo.php`, `er02-sem-banco.php`, `doacao-doador-obrigatorio.cjs`, `doacao-projeto-opcional.cjs`, `er02-formulario.cjs` e `er03-terminologias.cjs`.
- Sintaxe PHP e JavaScript dos arquivos desta execução aprovada. Nenhuma gravação em MariaDB real, migração, instalação ou commit.

## 6. Resultado final e limitações

Regra implementada e aprovada nos cenários executáveis. Fornecedor/projeto opcionais, separação de tipo/categoria e modais foram preservados. A captura por câmera foi verificada no código; não houve teste com câmera física de celular. A integração com gravação utilizou SQLite isolado, não MariaDB, e não substitui homologação nesse SGBD.

**Pendência de estrutura existente:** `endereco.numero` é `TINYINT(4)` assinado e obrigatório. Números superiores a 127 e valores como “S/N” não cabem nessa coluna. O código rejeita esses valores explicitamente, sem truncar ou inventar números. A ampliação dessa coluna exige autorização para mudança de estrutura; não foi executada nem preparada migração automática. Isso limita o cadastro de endereços reais incompatíveis com a estrutura atual.
