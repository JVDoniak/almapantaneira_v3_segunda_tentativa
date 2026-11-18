<?php
require_once __DIR__ . '/../utils/configLocal.php';

class Database
{
    private $conn;  //ATRIBUTO DA CLASSE
    private string $local; //ATRIBUTO DA CLASSE
    private string $db; //ATRIBUTO DA CLASSE
    private string $user; //ATRIBUTO DA CLASSE
    private string $password; //ATRIBUTO DA CLASSE
    private string $table; //ATRIBUTO DA CLASSE

    function __construct($table = null)
    { //MÉTODO CONSTRUTOR RECEBE A TABLE QUE IRÁ MANIPULAR NO BANCO
        // def __init__(self)
        $this->table = $table;
        $this->local = almaConfiguracao('DB_HOST');
        $this->db = almaConfiguracao('DB_NAME');
        $this->user = almaConfiguracao('DB_USER');
        $this->password = almaConfiguracao('DB_PASS');
        $this->conectar();
    }

    public function conectar()
    { //MÉTODO CONECTAR AO BANCO
        try {

            $this->conn = new PDO("mysql:host=" . $this->local . ";dbname=" . $this->db, $this->user, $this->password);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            return true;
            //echo 'conectado com sucesso';
        } catch (\PDOException $erro) {
            return false;
        }
    }

    public function execute($query, $params = [])
    {
        try {
            //$command será o SQL executado no servidor de banco de dados!!!!!!
            $command = $this->conn->prepare($query);
            $command->execute($params);
            return $command;
        } catch (\PDOException $erro) { // se der erro cai no catch
            // Diagnóstico no servidor, sem expor parâmetros ou SQL ao navegador.
            error_log(sprintf('Database::execute falhou [SQLSTATE=%s; driver=%s]: %s',
                $erro->errorInfo[0] ?? $erro->getCode(), $erro->errorInfo[1] ?? '', $erro->getMessage()));
            return false;
        }
    }

    public function select($fields='*'){
         $query = "SELECT " . $fields . " FROM " . $this->table . ";";
         $res = $this->execute($query);

         // ESSE ESTA COM fetchall MINUSCULO CORRIGIDO PARA MAIUSCULO
         $dados = $res->fetchAll(\PDO::FETCH_ASSOC);
         return $dados;
    }

    public function select_order_by($order){
         $query = "SELECT * FROM " . $this->table . " " . $order;

         $res = $this->execute($query);

         // ESSE ESTA COM fetchall MINUSCULO CORRIGIDO PARA MAIUSCULO
         $dados = $res->fetchAll(\PDO::FETCH_ASSOC);

         return $dados;
    }

    // metodo usado somente para filtros onde teremos apenas um registro no retorno
    // com where
    public function select_one_with_where($where = "", $fields = "*")
    {
        $query = "SELECT {$fields} FROM {$this->table}";
        if (!empty($where)) {
            $query .= " WHERE {$where}";
        }
        $query .= ";";
        $stmt = $this->conn->query($query);
        // echo $query;
        // exit;
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // metodo usado somente para filtros onde teremos varios registros no retorno
    // com where
    public function select_all_with_where($where = "", $fields = "*")
    {
        try {
            $query = "SELECT {$fields} FROM {$this->table}";
            if (!empty($where)) {
                $query .= " WHERE {$where}";
            }
            $query .= ";";
            $stmt = $this->conn->query($query);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {

            throw $th;
        }
    }

    public function insert($values)
    { // o método insert recebe um array com chave=>valor como parametro
        //ESTE INSERT SERVE PARA QUALQUER TABLE DO BANCO

        try {
            $fields = array_keys($values); // extrair as chaves do array
            $params = array_pad([], count($fields), '?'); //criar o esquema de insert SQL

            $query = 'INSERT INTO ' . $this->table . ' (' . implode(',', $fields) . ') VALUES (' . implode(',', $params) . ')';
            $res = $this->execute($query, array_values($values)); //extrair os valores do array
            return $res;
        } catch (\Throwable $th) {
            echo "Erro no banco: " . $th->getMessage();
            exit;
        }
    }

    public function update_in_id($id, $data)
    {
        $fields = array_keys($data);

        $set = implode('=?, ', $fields) . '=?';

        $query = "UPDATE " . $this->table . "
            SET " . $set . "
            WHERE id_" . $this->table . " = ?";

        $values = array_values($data);
        $values[] = $id;

        return $this->execute($query, $values);
    }

    public function update($data)
    {

        $id = array_shift($data);
        $fields = array_keys($data);

        $query = "UPDATE " . $this->table . ' SET ' . implode('=?,', $fields) . '=? WHERE id_'.$this->table.'='.$id;
        $res = $this->execute($query, array_values($data));
        return $res;
    }

   public function select_perfil($where)
    {
        $query = "SELECT id_usuario, nome, cpf, tipo, telefone, email, foto, status_usuario FROM {$this->table}";
        if (!empty($where)) {
            $query .= " WHERE {$where}";
        }
        $query .= ";";
        $stmt = $this->conn->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function delete($id_tratado)
    {

        try {
            $query = "DELETE FROM " . $this->table . " WHERE " . $id_tratado;
            $res = $this->execute($query);

            if ($res) {
                return true;
            } else {
                return false;
            }
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function select_projetos_with_doacao(){
        try {
            $query = 'SELECT projeto.id_projeto,projeto.nome, projeto.tipo_projeto, projeto.data_inicio,
                projeto.responsavel, projeto.andamento, projeto.local_projeto, sum(doacao.valor) as valor_projeto, projeto.status_projeto
                FROM doacao INNER JOIN projeto
                ON doacao.id_projeto = projeto.id_projeto
                GROUP BY projeto.id_projeto';

            return $this->execute($query);
        }catch (\Throwable $th) {
            //throw $th;
        }
    }

    public function select_fornecedores_with_enderecos($id_fornecedor, $id_endereco){

        try {
            $query = "
                    SELECT fornecedor.id_fornecedor, fornecedor.nome, fornecedor.razao_social, fornecedor.cpf_cnpj, fornecedor.email, fornecedor.telefone, fornecedor.contato,
                    endereco.id_endereco, endereco.cep, endereco.logradouro, endereco.numero, endereco.complemento, endereco.bairro, endereco.cidade, endereco.estado
                    FROM fornecedor INNER JOIN endereco
                    ON  fornecedor.id_endereco = endereco.id_endereco
                    WHERE fornecedor.id_fornecedor = ? AND endereco.id_endereco = ?";
            return $this->execute($query, [$id_fornecedor, $id_endereco]);
        }catch (\Throwable $th) {
            return false;
        }
    }

     public function select_doacoes_with_projetos(){

            // CORRIGIR AK

            try {
                $query = 'SELECT doador.nome as nome_doador, doacao.tipo, doacao.data_doacao,projeto.nome as projeto,doacao.valor, doacao.id_doacao, doacao.status_doacao
                    FROM doador INNER JOIN doacao
                    ON doador.id_doador = doacao.id_doador
                    LEFT JOIN projeto
                    ON doacao.id_projeto = projeto.id_projeto
                    ORDER BY doacao.data_cadastro DESC';

                $stmt = $this->execute($query);
                $res = $stmt->fetchAll(PDO::FETCH_ASSOC);
                return $res;
            }catch (\Throwable $th) {
                return false;
            }
        }

    public function buscarUsuariosFiltrados($nome = '', $cargo = '', $email = '')
        {
            try {
                $query = "SELECT id_usuario, nome, email, tipo, status_usuario
                        FROM usuario
                        WHERE (:nome1 = '' OR nome LIKE :nome2)
                            AND (:cargo1 = '' OR tipo = :cargo2)
                            AND (:email1 = '' OR email LIKE :email2)
                        ORDER BY status_usuario DESC, nome ASC";

                $stmt = $this->conn->prepare($query);

                $stmt->execute([
                    ':nome1'  => $nome,
                    ':nome2'  => '%' . $nome . '%',
                    ':cargo1' => $cargo,
                    ':cargo2' => $cargo,
                    ':email1' => $email,
                    ':email2' => '%' . $email . '%',
                ]);

                return $stmt->fetchAll(PDO::FETCH_ASSOC);
            } catch (\Throwable $th) {
                return [];
            }
        }

            public function insertLastId($values){

                try {
                    $fields = array_keys($values);
                    $binds = array_fill(0, count($fields), '?');

                    $query = "INSERT INTO " . $this->table . " (" . implode(',', $fields) . " ) VALUES ( " . implode(',', $binds) . " )";

                    $res = $this->execute($query, array_values($values));

                    return $this->conn->lastInsertId();

                } catch (\Throwable $th) {
                    //throw $th;
                }
            }



        public function buscarProjetosFiltrados($nome = '', $data_inicio = '', $tipo = '', $status = '')
    {
        try {
            $query = "SELECT id_projeto, nome, tipo_projeto, data_inicio, responsavel, local_projeto, status_projeto
                    FROM projeto
                    WHERE (:nome1 = '' OR nome LIKE :nome2)
                        AND (:data1 = '' OR data_inicio >= :data2)
                        AND (:tipo1 = '' OR tipo_projeto LIKE :tipo2)
                        AND (:status1 = '' OR status_projeto = :status2)
                    ORDER BY nome";

            $stmt = $this->execute($query, [
                ':nome1'   => $nome,
                ':nome2'   => "%{$nome}%",
                ':data1'   => $data_inicio,
                ':data2'   => $data_inicio,
                ':tipo1'   => $tipo,
                ':tipo2'   => $tipo,
                ':status1' => $status,
                ':status2' => $status,
            ]);

            if ($stmt === false) {
                return [];
            }

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    // public function select_doadores_with_enderecos($id_doador){

    //     try {
    //         $query = "
    //                SELECT doador.id_doador, doador.nome, doador.cpf_cnpj, doador.email, doador.telefone, doador.contato,
    //                     endereco.id_endereco, endereco.cep, endereco.logradouro, endereco.numero, endereco.complemento,
    //                     endereco.bairro, endereco.cidade, endereco.estado
    //                 FROM doador
    //                 INNER JOIN endereco ON doador.id_endereco = endereco.id_endereco
    //                 WHERE doador.id_doador = ?;";
    //         return $this->execute($query, [$id_doador]);
    //     }catch (\Throwable $th) {
    //         return false;
    //     }
    // }

    public function select_despesa_categoria(){

            try {
            $query = "
                         SELECT
                            d.id_despesa,
                            d.nome as nome_despesa,
                            d.data_vencimento,
                            d.descricao,
                            d.valor,
                            d.forma_pagamento,
                            d.anexo_nota,
                            d.data_lancamento,
                            d.id_categoria_despesa,
                            c.descricao AS nome_categoria,
                            d.id_tipo_despesa,
                            t.descricao AS nome_tipo,
                            d.status_despesa
                        FROM despesa d
                        LEFT JOIN tipo_despesa t ON t.id_tipo_despesa = d.id_tipo_despesa
                        LEFT JOIN categoria_despesa c
                            ON d.id_categoria_despesa = c.id_categoria_despesa
                        ORDER BY d.status_despesa DESC;";

            $stmt = $this->execute($query);
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function select_doadores_with_enderecos($id_doador){
        try {
            $query = "
                SELECT
                    doador.id_doador,
                    doador.tipo,
                    doador.nome,
                    doador.cpf_cnpj,
                    doador.contato_empresa,
                    doador.razao_social,
                    doador.email,
                    doador.telefone,
                    doador.id_endereco,
                    endereco.cep,
                    endereco.logradouro,
                    endereco.numero,
                    endereco.complemento,
                    endereco.bairro,
                    endereco.cidade,
                    endereco.estado
                FROM doador
                LEFT JOIN endereco ON doador.id_endereco = endereco.id_endereco
                WHERE doador.id_doador = ?;";

            $stmt = $this->execute($query, [$id_doador]);
            return  $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }
    }

    public function select_doacao_doador($id_doacao){

            try {

            $query = "SELECT doacao.id_doacao, doador.id_doador, doador.nome, doacao.tipo, doacao.descricao, doacao.valor,
                        doacao.forma_pagamento, doacao.data_doacao, doacao.id_projeto, projeto.nome as projeto_nome
                        FROM doacao
                        LEFT JOIN projeto ON projeto.id_projeto = doacao.id_projeto
                        INNER JOIN doador ON doador.id_doador = doacao.id_doador
                        WHERE doacao.id_doacao = ?;";

                    $stmt = $this->execute($query, [(int) $id_doacao]);
                    return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
            }
            catch (\Throwable $th) {

                return false;
            }

    }
    public function select_usuarios_filtro($nome, $tipo, $email){

            try {
            $query = "
                SELECT
                    id_usuario,
                    nome,
                    email,
                    tipo
                FROM usuario
                WHERE (
                    LOWER(nome) LIKE '%$nome%'
                    OR LOWER(nome) LIKE '%$nome%'
                )
                OR tipo LIKE '%$tipo%'
                ORDER BY nome ASC;";

            $stmt = $this->execute($query);
            return  $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (\Throwable $th) {
            return $th->getMessage();
        }

    }

    public function buscarFornecedores($nome = '', $cnpj = '', $status = '') {
        $sql = "SELECT *
            FROM {$this->table}
            WHERE 1 = 1
            AND (
                CHAR_LENGTH(?) < 3
                OR LOWER(nome) LIKE CONCAT('%', LOWER(?), '%')
                OR LOWER(razao_social) LIKE CONCAT('%', LOWER(?), '%')
            )
            AND (
                ? = ''
                OR cpf_cnpj LIKE CONCAT('%', ?, '%')
            )
            AND (
                ? = ''
                OR status_fornecedor = IF(? IN ('ativo', '1'), 1, 0)
            )
            ORDER BY status_fornecedor DESC, nome ASC;";

        $cnpjLimpo = preg_replace('/[^0-9]/', '', $cnpj);

        $params = [
            $nome,
            $nome,
            $nome,
            $cnpjLimpo,
            $cnpjLimpo,
            $status,
            $status
        ];

        $stmt = $this->execute($sql, $params);

        if ($stmt) {
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }

        return [];
    }



    private function removerAcentos($string)
    {
        return preg_replace(
            ['/[áàãâä]/ui', '/[éèêë]/ui', '/[íìîï]/ui', '/[óòõôö]/ui', '/[úùûü]/ui', '/[ç]/ui'],
            ['a', 'e', 'i', 'o', 'u', 's'],
            $string
        );
    }

    public function select_doadores_com_filtros($filtros = null) {
        $where = [];

        if (!empty($filtros['nome'])) {
            array_push($where,"d.nome LIKE '%{$filtros['nome']}%'");
        }

        if (!empty($filtros['cpf_cnpj'])) {
            array_push($where,"d.cpf_cnpj LIKE '%{$filtros['cpf']}%'");
        }

        array_push($where, "d.status_doador = {$filtros['status_doador']}");

        $sql = !empty($where) ? implode(' AND ', $where) : null;

        try {
            $sql = "
                SELECT
                    d.id_doador,
                    d.tipo,
                    d.nome,
                    d.razao_social,
                    d.cpf_cnpj,
                    d.telefone,
                    d.email,
                    d.status_doador
                FROM doador d";

            if ($where) {
                $sql .= " WHERE ";
                foreach($where as $rule){
                    $sql .= " {$rule} ";
                    $sql .= $rule != end($where) ? " AND " : "";
                }
            }

            $sql .= " ORDER BY d.id_doador DESC;";
            $stmt = $this->execute($sql);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        } catch (\Throwable $th) {
            return [];
        }
    }
}
