-- ---------------------------------------------------------------------------
-- Ponntos & Pelucias by Carol - dados de exemplo
--
-- Importe este arquivo DEPOIS do schema.sql.
-- As fotos citadas aqui ja estao na pasta uploads/ do projeto; elas foram
-- recortadas do proprio prototipo e devem ser trocadas pelas fotos reais
-- da Carol pelo painel admin.
-- ---------------------------------------------------------------------------

USE pelucias;

-- Limpa os dados antigos mantendo a estrutura (util para recomecar do zero).
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE colecao_produto;
TRUNCATE TABLE produto_fotos;
TRUNCATE TABLE pedidos;
TRUNCATE TABLE produtos;
TRUNCATE TABLE colecoes;
TRUNCATE TABLE categorias;
TRUNCATE TABLE banners;
TRUNCATE TABLE admins;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------------------------------------------------------------------------
-- Administrador padrao
-- E-mail: admin@pelucias.com     Senha: admin123
--
-- ATENCAO: troque esta senha no primeiro acesso, em Admin > Alterar senha.
-- O hash abaixo foi gerado com password_hash('admin123', PASSWORD_DEFAULT),
-- que e o mesmo formato que o password_verify() do PHP confere no login.
-- ---------------------------------------------------------------------------
INSERT INTO admins (nome, email, senha_hash) VALUES
('Carol', 'admin@pelucias.com',
 '$2y$10$LwgXfcrYBPRqvYxAz9DcVOCoQmHhf8flDurSRzmskIoUUCCX4dwzC');

-- ---------------------------------------------------------------------------
-- Categorias (as 5 do prototipo)
-- ---------------------------------------------------------------------------
INSERT INTO categorias (id, nome, slug, imagem, ordem, ativo) VALUES
(1, 'Chaveiros',   'chaveiros',   'cat-chaveiros.jpg',   1, 1),
(2, 'Personagens', 'personagens', 'cat-personagens.jpg', 2, 1),
(3, 'Animais',     'animais',     'cat-animais.jpg',     3, 1),
(4, 'Pelucias',    'pelucias',    'cat-pelucias.jpg',    4, 1),
(5, 'Outros',      'outros',      'cat-outros.jpg',      5, 1);

-- ---------------------------------------------------------------------------
-- Colecoes
-- ---------------------------------------------------------------------------
INSERT INTO colecoes (id, nome, slug, descricao, imagem, ordem, ativo) VALUES
(1, 'Dupla Pinguim', 'dupla-pinguim',
 'Os pinguins beijoqueiros e seus amigos: perfeitos para presentear quem voce gosta.',
 'col-pinguins.jpg', 1, 1),
(2, 'Chapeuzinhos', 'chapeuzinhos',
 'Kit de chapeuzinhos de croche para dar personalidade aos seus amigurumis.',
 'col-chapeuzinhos.jpg', 2, 1);

-- ---------------------------------------------------------------------------
-- Banners do carrossel da home
-- ---------------------------------------------------------------------------
INSERT INTO banners (id, titulo, imagem, link, ordem, ativo) VALUES
(1, 'Pinguins beijoqueiros',  'banner-pinguins.jpg',     'produto.php?id=1',             1, 1),
(2, 'Colecao Chapeuzinhos',   'banner-chapeuzinhos.jpg', 'colecao.php?slug=chapeuzinhos', 2, 1),
(3, 'Patinho de chapeu',      'banner-patinho.jpg',      'produto.php?id=2',             3, 1);

-- ---------------------------------------------------------------------------
-- Produtos
-- ---------------------------------------------------------------------------
INSERT INTO produtos
  (id, categoria_id, nome, slug, descricao, materiais, tamanho, preco, mais_vendido, ativo) VALUES
(1, 1, 'Pinguins beijoqueiros', 'pinguins-beijoqueiros',
 'Casal de pinguins de croche em fio veludo, feitos para ficarem de maos dadas. Acompanha argola e mosquetao para usar na bolsa ou na chave.',
 'Fio veludo antialergico, enchimento de fibra siliconada, olhos de seguranca',
 'Cerca de 8 cm cada', 60.00, 1, 1),

(2, 1, 'Patinho de chapeu', 'patinho-de-chapeu',
 'Patinho branquinho usando chapeuzinho de croche. Pode ser feito na cor de chapeu que voce preferir.',
 'Fio veludo, enchimento de fibra siliconada, olhos de seguranca',
 'Cerca de 9 cm', 45.00, 1, 1),

(3, 5, 'Mao mao', 'mao-mao',
 'Peca decorativa inspirada no personagem Mao mao, feita ponto a ponto com muito carinho.',
 'Fio de algodao e fio veludo, enchimento de fibra siliconada',
 'Cerca de 10 cm', 50.00, 1, 1),

(4, 1, 'Chaveiro abelhinha', 'chaveiro-abelhinha',
 'Abelhinha pequenininha de chaveiro, otima para dar de lembrancinha.',
 'Fio de algodao, enchimento de fibra siliconada, olhos de seguranca',
 'Cerca de 5 cm', 25.00, 1, 1),

(5, 2, 'Bonequinho ninja', 'bonequinho-ninja',
 'Bonequinho inspirado em personagem de anime, com bandana e roupinha laranja. Feito sob encomenda no personagem que voce escolher.',
 'Fio de algodao, enchimento de fibra siliconada, olhos bordados',
 'Cerca de 12 cm', 80.00, 0, 1),

(6, 3, 'Gatinho laranja', 'gatinho-laranja',
 'Gatinho sentadinho de fio de algodao, com carinha bordada a mao.',
 'Fio de algodao, enchimento de fibra siliconada',
 'Cerca de 14 cm', 55.00, 1, 1),

(7, 4, 'Gatinho de laco', 'gatinho-de-laco',
 'Pelucia de gatinho branco com lacinho rosa. Macio, pode dormir abracado.',
 'Fio veludo, enchimento de fibra siliconada, fita de cetim',
 'Cerca de 18 cm', 70.00, 0, 1),

(8, 5, 'Bolsinha de morango', 'bolsinha-de-morango',
 'Bolsinha de croche em formato de morango, com cordao para fechar. Cabe moedas, fones ou pequenas lembrancas.',
 'Fio de algodao, forro de tecido',
 'Cerca de 12 cm de altura', 65.00, 0, 1),

(9, 5, 'Kit chapeuzinhos', 'kit-chapeuzinhos',
 'Kit com chapeuzinhos de croche variados (cogumelo, sapinho, ursinho e outros) para trocar no seu amigurumi.',
 'Fio de algodao',
 'Cerca de 6 cm de diametro cada', 35.00, 1, 1),

(10, 1, 'Pinguim avulso', 'pinguim-avulso',
 'Um pinguim sozinho, para quem quer so um. Escolha a cor na hora da encomenda.',
 'Fio veludo, enchimento de fibra siliconada, olhos de seguranca',
 'Cerca de 8 cm', 35.00, 0, 1);

-- ---------------------------------------------------------------------------
-- Fotos dos produtos (o produto 1 tem tres fotos, para mostrar a galeria)
-- ---------------------------------------------------------------------------
INSERT INTO produto_fotos (produto_id, arquivo, principal, ordem) VALUES
(1,  'pinguins-beijoqueiros.jpg', 1, 1),
(1,  'pinguim-rosa.jpg',          0, 2),
(1,  'pinguim-azul.jpg',          0, 3),
(2,  'patinho-de-chapeu.jpg',     1, 1),
(3,  'mao-mao.jpg',               1, 1),
(4,  'chaveiro-abelhinha.jpg',    1, 1),
(5,  'bonequinho.jpg',            1, 1),
(6,  'gatinho.jpg',               1, 1),
(7,  'gatinho-de-laco.jpg',       1, 1),
(8,  'bolsinha-morango.jpg',      1, 1),
(9,  'chapeuzinhos.jpg',          1, 1),
(10, 'pinguim-avulso.jpg',        1, 1);

-- ---------------------------------------------------------------------------
-- Quais produtos estao em cada colecao (N:N)
-- ---------------------------------------------------------------------------
INSERT INTO colecao_produto (colecao_id, produto_id) VALUES
(1, 1), (1, 10), (1, 2),
(2, 9), (2, 2);

-- ---------------------------------------------------------------------------
-- Dois pedidos de exemplo, para o painel nao abrir vazio
-- ---------------------------------------------------------------------------
INSERT INTO pedidos (produto_id, nome_cliente, telefone, quantidade, observacao, status) VALUES
(1, 'Maria Silva',  '11988887777', 1, 'Pode ser na cor rosa e azul mesmo?', 'novo'),
(4, 'Joao Pereira', '11977776666', 3, 'Para lembrancinha de aniversario.',  'em_producao');
