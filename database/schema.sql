-- ---------------------------------------------------------------------------
-- Ponntos & Pelucias by Carol - estrutura do banco de dados
-- Projeto integrador - Lais, Aline, Vitor e Matheus
--
-- COMO USAR: abra o phpMyAdmin (http://localhost/phpmyadmin), clique em
-- "Importar", escolha este arquivo e clique em "Executar".
-- Depois faca o mesmo com o arquivo seed.sql (os dados de exemplo).
-- ---------------------------------------------------------------------------

CREATE DATABASE IF NOT EXISTS pelucias
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE pelucias;

-- Apaga as tabelas antigas antes de criar de novo, para o arquivo poder ser
-- importado mais de uma vez sem dar erro. A ordem importa por causa das
-- chaves estrangeiras: primeiro as tabelas "filhas", depois as "pais".
DROP TABLE IF EXISTS colecao_produto;
DROP TABLE IF EXISTS produto_fotos;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS colecoes;
DROP TABLE IF EXISTS categorias;
DROP TABLE IF EXISTS banners;
DROP TABLE IF EXISTS admins;

-- ---------------------------------------------------------------------------
-- admins: quem pode entrar na area restrita (/admin). So a Carol, por enquanto.
-- A senha NUNCA e guardada em texto puro: guardamos o hash gerado pelo PHP
-- com password_hash() e conferimos com password_verify().
-- ---------------------------------------------------------------------------
CREATE TABLE admins (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- categorias: chaveiros, personagens, animais, pelucias, outros.
-- "slug" e o nome que aparece na URL (categoria.php?slug=chaveiros).
-- "ordem" controla a posicao na home; "ativo" esconde sem precisar apagar.
-- ---------------------------------------------------------------------------
CREATE TABLE categorias (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  nome   VARCHAR(100) NOT NULL,
  slug   VARCHAR(120) NOT NULL UNIQUE,
  imagem VARCHAR(255) DEFAULT NULL,
  ordem  INT          NOT NULL DEFAULT 0,
  ativo  TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- colecoes: kits e conjuntos (ex.: "Dupla Pinguim", "Chapeuzinhos").
-- Um produto pode estar em varias colecoes (ver tabela colecao_produto).
-- ---------------------------------------------------------------------------
CREATE TABLE colecoes (
  id        INT AUTO_INCREMENT PRIMARY KEY,
  nome      VARCHAR(100) NOT NULL,
  slug      VARCHAR(120) NOT NULL UNIQUE,
  descricao TEXT,
  imagem    VARCHAR(255) DEFAULT NULL,
  ordem     INT          NOT NULL DEFAULT 0,
  ativo     TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- produtos: o catalogo em si.
-- preco e DECIMAL(10,2) porque dinheiro nunca deve ser guardado como FLOAT
-- (FLOAT arredonda errado: 0.1 + 0.2 nao da exatamente 0.3).
-- Se a categoria for apagada, categoria_id vira NULL e o produto continua
-- existindo (o admin ainda assim impede apagar categoria com produtos).
-- ---------------------------------------------------------------------------
CREATE TABLE produtos (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  categoria_id  INT           DEFAULT NULL,
  nome          VARCHAR(150)  NOT NULL,
  slug          VARCHAR(170)  NOT NULL UNIQUE,
  descricao     TEXT,
  materiais     VARCHAR(255)  DEFAULT NULL,
  tamanho       VARCHAR(100)  DEFAULT NULL,
  preco         DECIMAL(10,2) NOT NULL DEFAULT 0.00,
  mais_vendido  TINYINT(1)    NOT NULL DEFAULT 0,
  ativo         TINYINT(1)    NOT NULL DEFAULT 1,
  criado_em     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP
                              ON UPDATE CURRENT_TIMESTAMP,
  CONSTRAINT fk_produtos_categoria FOREIGN KEY (categoria_id)
    REFERENCES categorias (id) ON DELETE SET NULL,
  INDEX idx_produtos_categoria (categoria_id),
  INDEX idx_produtos_ativo (ativo),
  INDEX idx_produtos_mais_vendido (mais_vendido)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- produto_fotos: um produto pode ter varias fotos.
-- "principal = 1" e a foto que aparece nos cards e no topo da galeria.
-- ON DELETE CASCADE: apagou o produto, o banco apaga as linhas das fotos
-- sozinho (os arquivos do disco o PHP apaga em funcoes.php).
-- ---------------------------------------------------------------------------
CREATE TABLE produto_fotos (
  id         INT AUTO_INCREMENT PRIMARY KEY,
  produto_id INT          NOT NULL,
  arquivo    VARCHAR(255) NOT NULL,
  principal  TINYINT(1)   NOT NULL DEFAULT 0,
  ordem      INT          NOT NULL DEFAULT 0,
  CONSTRAINT fk_fotos_produto FOREIGN KEY (produto_id)
    REFERENCES produtos (id) ON DELETE CASCADE,
  INDEX idx_fotos_produto (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- colecao_produto: relacao N:N entre colecoes e produtos.
-- A chave primaria composta impede repetir o mesmo par duas vezes.
-- ---------------------------------------------------------------------------
CREATE TABLE colecao_produto (
  colecao_id INT NOT NULL,
  produto_id INT NOT NULL,
  PRIMARY KEY (colecao_id, produto_id),
  CONSTRAINT fk_cp_colecao FOREIGN KEY (colecao_id)
    REFERENCES colecoes (id) ON DELETE CASCADE,
  CONSTRAINT fk_cp_produto FOREIGN KEY (produto_id)
    REFERENCES produtos (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- banners: as imagens do carrossel da home.
-- ---------------------------------------------------------------------------
CREATE TABLE banners (
  id     INT AUTO_INCREMENT PRIMARY KEY,
  titulo VARCHAR(150) NOT NULL,
  imagem VARCHAR(255) NOT NULL,
  link   VARCHAR(255) DEFAULT NULL,
  ordem  INT          NOT NULL DEFAULT 0,
  ativo  TINYINT(1)   NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ---------------------------------------------------------------------------
-- pedidos: o que chega pelo formulario "Encomendar pelo WhatsApp".
-- Nao e uma venda fechada: e um contato que a Carol vai responder.
-- Se o produto for apagado, produto_id vira NULL e o pedido continua no
-- historico (so perde o link para o produto).
-- ---------------------------------------------------------------------------
CREATE TABLE pedidos (
  id           INT AUTO_INCREMENT PRIMARY KEY,
  produto_id   INT          DEFAULT NULL,
  nome_cliente VARCHAR(120) NOT NULL,
  telefone     VARCHAR(20)  NOT NULL,
  quantidade   INT          NOT NULL DEFAULT 1,
  observacao   TEXT,
  status       ENUM('novo','em_producao','pago','enviado','entregue','cancelado')
               NOT NULL DEFAULT 'novo',
  criado_em    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_pedidos_produto FOREIGN KEY (produto_id)
    REFERENCES produtos (id) ON DELETE SET NULL,
  INDEX idx_pedidos_status (status),
  INDEX idx_pedidos_produto (produto_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
