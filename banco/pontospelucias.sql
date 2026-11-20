-- Banco de dados do site Pontos e Pelúcias
-- Importar pelo phpMyAdmin (aba "Importar") ou pelo terminal:
--   C:\xampp\mysql\bin\mysql.exe -u root < banco/pontospelucias.sql
-- Rodar de novo recria a tabela do zero com os dados abaixo.

CREATE DATABASE IF NOT EXISTS pontospelucias
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pontospelucias;

DROP TABLE IF EXISTS produtos;
DROP TABLE IF EXISTS categorias;

-- Cards de categoria da página inicial
CREATE TABLE categorias (
    id     INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome   VARCHAR(50)   NOT NULL,
    imagem VARCHAR(255)  NOT NULL,   -- caminho da imagem, ex: img/Chaveiro_Abelha.jpeg
    ordem  INT           NOT NULL DEFAULT 0,  -- posição do card na página inicial
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO categorias (id, nome, imagem, ordem) VALUES
(1, 'chaveiros',   'img/Chaveiro_Abelha.jpeg', 1),
(2, 'personagens', 'img/Boneco_Naruto.jpeg',   2),
(3, 'animais',     'img/gato_amarelo.jpeg',    3),
(4, 'pelúcias',    'img/marrie.jpeg',          4),
(5, 'outros',      'img/bolsa_morango.jpeg',   5);

CREATE TABLE produtos (
    id        INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    nome      VARCHAR(100)  NOT NULL,
    imagem    VARCHAR(255)  NOT NULL,   -- caminho da imagem, ex: img/Chaveiro_Morango.jpeg
    preco     DECIMAL(10,2) NULL,
    cores     VARCHAR(255)  NULL,
    opcoes    VARCHAR(255)  NULL,
    categoria VARCHAR(50)   NOT NULL,
    ativo     TINYINT(1)    NOT NULL DEFAULT 1,  -- 0 deixa inativo, 1 ativo
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO produtos (id, nome, imagem, preco, cores, opcoes, categoria) VALUES
( 1, 'Morango',            'img/Chaveiro_Morango.jpeg',        20.00, 'Vermelho/Rosa', 'Com miçanga/Sem miçanga', 'Chaveiro'),
( 2, 'Cereja',             'img/Decoracao_Amora.jpeg',         18.00, NULL, NULL, 'Chaveiro'),
( 3, 'Banana',             'img/Chaveiro_Banana.jpeg',         20.00, NULL, NULL, 'Chaveiro'),
( 4, 'Estrela',            'img/Chaveiro_Estrelinha.jpeg',     23.00, 'Amarelo/Amarelo pastel/Rosa/Roxo/Verde/Azul/Branco', 'Com miçanga/Sem miçanga', 'Chaveiro'),
( 5, 'Laços',              'img/Chaveiro_Lacos.jpeg',          20.00, 'Rosa escuro/Rosa claro/Lilás', NULL, 'Chaveiro'),
( 6, 'Abelha (preto)',     'img/Chaveiro_Abelha.jpeg',         NULL,  NULL, NULL, 'Chaveiro'),
( 7, 'Abelha (marrom)',    'img/Chaveiro_Abelha2.jpeg',        NULL,  NULL, NULL, 'Chaveiro'),
( 8, 'Pinguins',           'img/Boneco_Pinguins.jpeg',         NULL,  NULL, NULL, 'Boneco'),
( 9, 'Hello Kitty',        'img/Chaveiro_Gatinho.jpeg',        NULL,  NULL, NULL, 'Chaveiro'),
(10, 'Chapéu Luffy',       'img/Chaveiro_ChapeuDePalha.jpeg',  NULL,  NULL, NULL, 'Chaveiro'),
(11, 'Chapéu Ace',         'img/Chaveiro_ChapeuLaranja.jpeg',  NULL,  NULL, NULL, 'Chaveiro'),
(12, 'Chapéu Chopper',     'img/Chaveiro_ChapeuRosa.jpeg',     NULL,  NULL, NULL, 'Chaveiro'),
(13, 'Lontra',             'img/Boneco_Ursinho.jpeg',          NULL,  NULL, NULL, 'Boneco'),
(14, 'Patinho',            'img/Boneco_Patinho.jpeg',          NULL,  NULL, NULL, 'Boneco'),
(15, 'Totoro',             'img/Boneco_Bichinho.jpeg',         NULL,  NULL, NULL, 'Boneco'),
(16, 'Luffy',              'img/Chapeu_Luffy.jpeg',            NULL,  NULL, NULL, 'Boneco'),
(17, 'Naruto',             'img/Boneco_Naruto.jpeg',           NULL,  NULL, NULL, 'Boneco'),
(18, 'Marca páginas Flor', 'img/Paginas_Flores.jpeg',          NULL,  NULL, NULL, 'Marca páginas'),
(19, 'Rosa',               'img/Decoracao_Flores.jpeg',        NULL,  NULL, NULL, 'Decoração');

-- parte do administrador
CREATE TABLE administradores (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS contatos_redes;

CREATE TABLE contatos_redes (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    nome        VARCHAR(50)  NOT NULL,
    icone       VARCHAR(255) NOT NULL,
    descricao   VARCHAR(255) NOT NULL,
    classe_css  VARCHAR(50)  NULL,
    link        VARCHAR(255) NOT NULL,
    texto_botao VARCHAR(50)  NOT NULL,
    ordem       INT          NOT NULL DEFAULT 0,
    ativo       TINYINT(1)   NOT NULL DEFAULT 1,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


INSERT INTO contatos_redes
(nome, icone, descricao, classe_css, link, texto_botao, ordem, ativo)
VALUES
('WhatsApp', 'img/whatsapp.png',
 'Faça seu pedido ou tire dúvidas!',
 'whatsapp', 'https://wa.me',
 'Chamar no WhatsApp', 1, 1),

('Instagram', 'img/instagram.png',
 'Acompanhe as novidades e bastidores.',
 'instagram', 'https://instagram.com',
 'Seguir no Instagram', 2, 1),

('TikTok', 'img/tiktok.png',
 'Acompanhe meus vídeos, novidades e bastidores das peças feitas à mão!',
 'tiktok', 'https://www.tiktok.com/@pontosepelucias',
 'Ver meu TikTok', 3, 1);

CREATE TABLE configuracoes_rodape (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    mensagem VARCHAR(255) NOT NULL,
    texto_final VARCHAR(255) NOT NULL,
    ativo TINYINT(1) NOT NULL DEFAULT 1
);

INSERT INTO configuracoes_rodape
(titulo, mensagem, texto_final, ativo)
VALUES
(
    'Feito à mão, com muito amor ♡',
    'Obrigada por apoiar meu trabalho artesanal!',
    '♡ Pontos e Pelúcias by Carol ♡',
    1
);

-- Insere o seu e-mail e o hash gerado --
INSERT INTO administradores (email, senha) 
VALUES ('admin', '$2y$10$GorLDq6WgNxj9O7Xw15w4.v1WRDNclbXjfKgwoRvoun/z0a6UVpa.');
