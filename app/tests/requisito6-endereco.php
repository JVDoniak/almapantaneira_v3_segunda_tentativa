<?php
require_once __DIR__ . '/../controller/RegraEnderecoDoador.php';
function conferir($ok, $mensagem) {
    if (!$ok) throw new RuntimeException($mensagem);
    echo "OK: $mensagem\n";
}
foreach (['0.01' => 1, '999.99' => 99999, '1000.00' => 100000, '1200' => 120000,
    '1.000,00' => null, '-1' => null, '1000.001' => null, 'abc' => null] as $valor => $esperado) {
    conferir(RegraEnderecoDoador::centavos($valor) === $esperado, 'Conversao monetaria sem arredondamento: ' . $valor);
}
$vazio = RegraEnderecoDoador::dados([]);
conferir(RegraEnderecoDoador::erroPreenchimento($vazio) === null, 'Cadastro de doador aceita secao vazia');
conferir(RegraEnderecoDoador::erroDoacao(3, '999.99') === null, 'Doacao abaixo do limite dispensa consulta de endereco');
$completo = RegraEnderecoDoador::dados(['cep' => '79000-000', 'logradouro' => 'Rua teste', 'numero' => '10',
    'bairro' => 'Centro', 'cidade' => 'Campo Grande', 'estado' => 'Mato Grosso do Sul']);
conferir(RegraEnderecoDoador::completo($completo), 'Endereco completo sem complemento aceito');
conferir(RegraEnderecoDoador::erroPreenchimento($completo) === null, 'Endereco compativel com schema aceito');
$parcial = $completo; $parcial['cidade'] = '';
conferir(!RegraEnderecoDoador::completo($parcial), 'Endereco incompleto identificado');
foreach (['25', '1500', 'S/N'] as $numero) {
    $dados = $completo; $dados['numero'] = $numero;
    conferir(RegraEnderecoDoador::erroPreenchimento($dados) === null, 'Numero textual preservado: ' . $numero);
}
echo "Testes puros: nenhuma conexao com banco. Integracao do limite e das varias doacoes na suite isolada.\n";

conferir(!Endereco::numeroValido(str_repeat('1', 21)), 'Limite de 20 caracteres');
