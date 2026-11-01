-- Banco de dados do site Pontos e Pelúcias
-- Importar pelo phpMyAdmin (aba "Importar") ou pelo terminal:
--   C:\xampp\mysql\bin\mysql.exe -u root < banco/pontospelucias.sql
-- Rodar de novo recria a tabela do zero com os dados abaixo.

CREATE DATABASE IF NOT EXISTS pontospelucias
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE pontospelucias;

DROP TABLE IF EXISTS produtos;

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
