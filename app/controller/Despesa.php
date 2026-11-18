<?php
require_once __DIR__ . '/../model/Database.php';

class Despesa {
    public $id_despesa;
    public $nome;
    public $data_vencimento;
    public $descricao;
    public $valor;
    public $forma_pagamento;
    public $anexo_nota;
    public $data_lancamento;
    public $id_categoria_despesa;
    public $id_tipo_despesa;
    public $id_fornecedor;
    public $id_projeto;
    public $status_despesa;

    public function cadastrar() {
        $db = new Database('despesa');

        return $db->insert([
            'nome'                 => $this->nome,
            'id_categoria_despesa' => $this->id_categoria_despesa,
            'id_tipo_despesa'      => $this->id_tipo_despesa,
            'data_lancamento'      => $this->data_lancamento,
            'data_vencimento'      => $this->data_vencimento,
            'descricao'            => $this->descricao,
            'valor'                => $this->valor,
            'forma_pagamento'      => $this->forma_pagamento,
            'anexo_nota'           => $this->anexo_nota,
            'id_fornecedor'        => $this->id_fornecedor,
            'id_projeto'           => $this->id_projeto
        ]);
    }

    public function editar() {
        $db = new Database('despesa');

        return $db->update([
            'id_despesa'           => $this->id_despesa,
            'nome'                 => $this->nome,
            'id_categoria_despesa' => $this->id_categoria_despesa,
            'id_tipo_despesa'      => $this->id_tipo_despesa,
            'data_vencimento'      => $this->data_vencimento,
            'descricao'            => $this->descricao,
            'valor'                => $this->valor,
            'forma_pagamento'      => $this->forma_pagamento,
            'id_fornecedor'        => $this->id_fornecedor,
            'id_projeto'           => $this->id_projeto
        ]);
    }

    // Busca a despesa com o nome do fornecedor e do projeto (para preencher a edição)
    public function buscar_despesa_por_id($id_despesa) {
        $db = new Database('despesa');

        $query = "
            SELECT
                d.*,
                f.nome AS nome_fornecedor,
                p.nome AS nome_projeto
            FROM despesa d
            LEFT JOIN fornecedor f ON f.id_fornecedor = d.id_fornecedor
            LEFT JOIN projeto p    ON p.id_projeto    = d.id_projeto
            WHERE d.id_despesa = ?;";

        $stmt = $db->execute($query, [(int) $id_despesa]);
        return $stmt ? $stmt->fetch(PDO::FETCH_ASSOC) : false;
    }

    // Listagem com filtros
    public function buscarComFiltros($filtros = []) {
        $db = new Database('despesa');

        $query = "
            SELECT
                d.id_despesa,
                d.nome AS nome_despesa,
                d.data_vencimento,
                d.valor,
                d.forma_pagamento,
                d.status_despesa,
                d.id_categoria_despesa,
                d.id_tipo_despesa,
                t.descricao AS nome_tipo,
                d.id_projeto,
                c.descricao AS nome_categoria,
                p.nome AS nome_projeto
            FROM despesa d
            LEFT JOIN tipo_despesa t ON t.id_tipo_despesa = d.id_tipo_despesa
            LEFT JOIN categoria_despesa c ON c.id_categoria_despesa = d.id_categoria_despesa
            LEFT JOIN projeto p           ON p.id_projeto           = d.id_projeto
            WHERE 1 = 1";

        $params = [];

        if (!empty($filtros['nome'])) {
            $query .= " AND d.nome LIKE ?";
            $params[] = '%' . $filtros['nome'] . '%';
        }

        if (!empty($filtros['forma_pagamento'])) {
            $query .= " AND d.forma_pagamento = ?";
            $params[] = $filtros['forma_pagamento'];
        }

        if (!empty($filtros['id_categoria_despesa'])) {
            $query .= " AND d.id_categoria_despesa = ?";
            $params[] = (int) $filtros['id_categoria_despesa'];
        }

        if (!empty($filtros['id_tipo_despesa'])) {
            $query .= ' AND d.id_tipo_despesa = ?';
            $params[] = (int) $filtros['id_tipo_despesa'];
        }

        if (!empty($filtros['id_projeto'])) {
            $query .= " AND d.id_projeto = ?";
            $params[] = (int) $filtros['id_projeto'];
        }

        if (!empty($filtros['data_inicio']) && $this->dataValida($filtros['data_inicio'])) {
            $query .= " AND d.data_vencimento >= ?";
            $params[] = $filtros['data_inicio'];
        }

        if (!empty($filtros['data_fim']) && $this->dataValida($filtros['data_fim'])) {
            $query .= " AND d.data_vencimento <= ?";
            $params[] = $filtros['data_fim'];
        }

        $query .= " ORDER BY d.status_despesa DESC, d.data_vencimento ASC;";

        $stmt = $db->execute($query, $params);
        return $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    }

    public function buscarPorStatus() {
        return $this->buscarComFiltros();
    }

    public function categoriaExiste($id) {
        $db = new Database('categoria_despesa');
        $stmt = $db->execute('SELECT id_categoria_despesa FROM categoria_despesa WHERE id_categoria_despesa = ?', [$id]);
        return $stmt && $stmt->fetchColumn() !== false;
    }

    public function listarCategorias() {
        $db = new Database('categoria_despesa');
        return $db->select_all_with_where("1 = 1 ORDER BY id_categoria_despesa", "id_categoria_despesa, descricao");
    }

    // Todos os fornecedores do banco (ativos primeiro)
    public function listarFornecedores() {
        $db = new Database('fornecedor');
        return $db->select_all_with_where("1 = 1 ORDER BY status_fornecedor DESC, nome", "id_fornecedor, nome, status_fornecedor");
    }

    // Todos os projetos do banco (ativos primeiro)
    public function listarProjetos() {
        $db = new Database('projeto');
        return $db->select_all_with_where("1 = 1 ORDER BY status_projeto DESC, nome", "id_projeto, nome, status_projeto");
    }

    // Formas de pagamento que já existem nas despesas cadastradas
    public function listarFormasPagamento() {
        $db = new Database('despesa');

        $stmt = $db->execute("
            SELECT DISTINCT forma_pagamento
            FROM despesa
            WHERE forma_pagamento <> ''
            ORDER BY forma_pagamento;");

        return $stmt ? $stmt->fetchAll(PDO::FETCH_COLUMN) : [];
    }

    public function atualizarStatus($id_despesa, $status)
    {
        $db = new Database('despesa');

        $res = $db->update_in_id($id_despesa, [
            "status_despesa" => $status
        ]);

        return $res;
    }

    private function dataValida($data) {
        return (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $data);
    }

}