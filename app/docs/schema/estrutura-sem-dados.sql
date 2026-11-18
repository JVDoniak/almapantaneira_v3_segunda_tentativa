-- Estrutura sem registros, derivada do SQL versionado da V2.
-- NÃO EXECUTADO. Somente para revisão/instalação futura em banco vazio.
-- Inclui ER-02, tipo_despesa e endereco.numero VARCHAR(20); não reaplicar migrações equivalentes.
-- Sem DROP, credenciais ou dados pessoais; somente INSERT de valores de referência de tipo_despesa.
SET NAMES utf8mb4;
SET @alma_fk_anterior = @@FOREIGN_KEY_CHECKS;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `cpf` char(11) UNIQUE NOT NULL,
  `tipo` varchar(100) NOT NULL,
  `telefone` char(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `senha` varchar(255) NOT NULL,
  `foto` varchar(255) DEFAULT NULL,
  `status_usuario` tinyint(4) DEFAULT 1,
   `primeiro_acesso` tinyint(4) DEFAULT 1,
  PRIMARY KEY (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `endereco` (
  `id_endereco` int(11) NOT NULL AUTO_INCREMENT,
  `cep` char(8) NOT NULL,
  `logradouro` varchar(150) NOT NULL,
  `numero` varchar(20) NOT NULL,
  `complemento` varchar(150) NOT NULL,
  `bairro` varchar(150) DEFAULT NULL,
  `cidade` varchar(150) DEFAULT NULL,
  `estado` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id_endereco`)
)ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `categoria_despesa` (
  `id_categoria_despesa` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`id_categoria_despesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `fornecedor` (
  `id_fornecedor` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(150) NOT NULL,
  `razao_social` varchar(150) NOT NULL,
  `cpf_cnpj` char(14) UNIQUE NOT NULL,
  `telefone` char(11) NOT NULL,
  `email` varchar(150) NOT NULL,
  `contato` varchar(100) NOT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `status_fornecedor` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_fornecedor`),
  KEY `id_endereco` (`id_endereco`),
  CONSTRAINT `fornecedor_ibfk_1` FOREIGN KEY (`id_endereco`) REFERENCES `endereco` (`id_endereco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `doador` (
  `id_doador` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(50) NOT NULL,
  `nome` varchar(100) DEFAULT NULL,
  `cpf_cnpj` char(14) UNIQUE NOT NULL,
  `contato_empresa` varchar(150) DEFAULT NULL,
  `razao_social` varchar(150) DEFAULT NULL,
  `telefone` char(11) NOT NULL,
  `email` varchar(100) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_endereco` int(11) DEFAULT NULL,
  `status_doador` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_doador`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_endereco` (`id_endereco`),
  CONSTRAINT `doador_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`),
  CONSTRAINT `doador_ibfk_2` FOREIGN KEY (`id_endereco`) REFERENCES `endereco` (`id_endereco`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `projeto` (
  `id_projeto` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `responsavel` varchar(100) NOT NULL,
  `local_projeto` varchar(100) NOT NULL,
  `tipo_projeto` varchar(100) NOT NULL,
  `valor_projeto` decimal(10,2) NOT NULL,
  `data_inicio` date NOT NULL,
  `data_termino` date NOT NULL,
  `andamento` enum('Em planejamento','Em andamento','Concluído') DEFAULT 'Em planejamento',
  `descricao` tinytext DEFAULT NULL,
  `status_projeto` tinyint(1) NOT NULL DEFAULT 1,
  `id_usuario` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_projeto`),
  KEY `id_usuario` (`id_usuario`),
  CONSTRAINT `projeto_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE tipo_despesa (
    id_tipo_despesa INT NOT NULL AUTO_INCREMENT,
    descricao VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_tipo_despesa),
    UNIQUE KEY uq_tipo_despesa_descricao (descricao)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Tipo de despesa consolidado da migracao 20261031.
CREATE TABLE `tipo_despesa` (
  `id_tipo_despesa` int NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_despesa`),
  UNIQUE KEY `uq_tipo_despesa_descricao` (`descricao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `despesa` (
  `id_despesa` int(11) NOT NULL AUTO_INCREMENT,
  `nome` varchar(100) NOT NULL,
  `data_vencimento` date NOT NULL,
  `descricao` varchar(100) NOT NULL,
  `valor` decimal(10,2) NOT NULL,
  `forma_pagamento` varchar(100) NOT NULL,
  `anexo_nota` varchar(255) DEFAULT NULL,
  `status_despesa` tinyint(1) NOT NULL DEFAULT 1,
  `data_lancamento` datetime DEFAULT current_timestamp(),
  `id_tipo_despesa` int DEFAULT NULL,
  `id_categoria_despesa` int(11) DEFAULT NULL,
  `id_fornecedor` int(11) DEFAULT NULL,
   `id_projeto` int(11) DEFAULT NULL,
  PRIMARY KEY (`id_despesa`),
  KEY `idx_despesa_tipo_despesa` (`id_tipo_despesa`),
  CONSTRAINT `fk_despesa_tipo_despesa` FOREIGN KEY (`id_tipo_despesa`) REFERENCES `tipo_despesa` (`id_tipo_despesa`),
  KEY `id_fornecedor` (`id_fornecedor`),
  KEY `id_categoria_despesa` (`id_categoria_despesa`),
  CONSTRAINT `despesa_ibfk_1` FOREIGN KEY (`id_fornecedor`) REFERENCES `fornecedor` (`id_fornecedor`),
  CONSTRAINT `despesa_ibfk_2` FOREIGN KEY (`id_categoria_despesa`) REFERENCES `categoria_despesa` (`id_categoria_despesa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `tipo_doacao` (
  `id_tipo_doacao` int(11) NOT NULL AUTO_INCREMENT,
  `descricao` varchar(100) NOT NULL,
  PRIMARY KEY (`id_tipo_doacao`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `doacao` (
  `id_doacao` int(11) NOT NULL AUTO_INCREMENT,
  `tipo` varchar(150) NOT NULL,
  `descricao` tinytext DEFAULT NULL,
  `valor` decimal(10,2) NOT NULL,
  `forma_pagamento` varchar(50) NOT NULL,
  `data_doacao` date NOT NULL,
  `id_doador` int(11) DEFAULT NULL,
  `id_projeto` int(11) DEFAULT NULL,
  `data_cadastro` datetime DEFAULT current_timestamp(),
  `status_doacao` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id_doacao`),
  KEY `id_doador` (`id_doador`),
  KEY `id_projeto` (`id_projeto`),
  CONSTRAINT `doacao_ibfk_1` FOREIGN KEY (`id_doador`) REFERENCES `doador` (`id_doador`),
  CONSTRAINT `doacao_ibfk_2` FOREIGN KEY (`id_projeto`) REFERENCES `projeto` (`id_projeto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dados de referencia previstos na migracao de tipos de despesa.
INSERT INTO `tipo_despesa` (`descricao`) VALUES ('Fixa'), ('Variável');

SET FOREIGN_KEY_CHECKS = @alma_fk_anterior;
