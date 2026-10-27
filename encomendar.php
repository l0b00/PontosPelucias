<?php
/**
 * encomendar.php - Recebe o formulario de encomenda da pagina do produto
 *
 * O que ele faz, nesta ordem:
 *   1. confere o token CSRF
 *   2. valida todos os campos AQUI no servidor (nao confia no required do HTML)
 *   3. grava o pedido na tabela `pedidos` com status "novo"
 *   4. manda a pessoa para o WhatsApp da Carol com a mensagem pronta
 *
 * Esta pagina nunca mostra HTML: ela so redireciona.
 */

require __DIR__ . '/includes/conexao.php';
require __DIR__ . '/includes/funcoes.php';

// So aceita POST. Quem digitar o endereco no navegador volta para a home.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('index.php'));
}

exigirCsrf();

// --- 1. Leitura dos campos ------------------------------------------------
$produtoId  = (int) ($_POST['produto_id'] ?? 0);
$nome       = trim($_POST['nome_cliente'] ?? '');
$telefone   = trim($_POST['telefone'] ?? '');
$quantidade = (int) ($_POST['quantidade'] ?? 1);
$observacao = trim($_POST['observacao'] ?? '');

// Guarda o que foi digitado para devolver ao formulario se der erro.
$_SESSION['encomenda_dados'] = [
    'nome_cliente' => $nome,
    'telefone'     => $telefone,
    'quantidade'   => $quantidade,
    'observacao'   => $observacao,
];

// --- 2. Validacao no servidor --------------------------------------------
$erros = [];

// O produto precisa existir e estar ativo.
$consulta = $pdo->prepare('SELECT id, nome, preco FROM produtos WHERE id = ? AND ativo = 1');
$consulta->execute([$produtoId]);
$produto = $consulta->fetch();

if (!$produto) {
    definirFlash('erro', 'Este produto nao esta mais disponivel.');
    redirecionar(url('index.php'));
}

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'Escreva seu nome completo.';
}
if (mb_strlen($nome, 'UTF-8') > 120) {
    $erros[] = 'O nome e grande demais.';
}

// Telefone: entre 10 e 13 digitos cobre "DDD + numero" e "55 + DDD + numero".
$telefoneNumeros = somenteNumeros($telefone);
if (strlen($telefoneNumeros) < 10 || strlen($telefoneNumeros) > 13) {
    $erros[] = 'Informe um WhatsApp valido, com DDD.';
}

if ($quantidade < 1 || $quantidade > 99) {
    $erros[] = 'A quantidade deve ser de 1 a 99.';
}

if (mb_strlen($observacao, 'UTF-8') > 500) {
    $erros[] = 'A observacao passou de 500 caracteres.';
}

if ($erros) {
    definirFlash('erro', implode(' ', $erros));
    redirecionar(url('produto.php?id=' . $produtoId));
}

// --- 3. Grava o pedido ----------------------------------------------------
// Prepared statement: os valores vao separados do SQL, entao nada do que a
// pessoa digitou pode virar comando.
$consulta = $pdo->prepare(
    "INSERT INTO pedidos (produto_id, nome_cliente, telefone, quantidade, observacao, status)
          VALUES (?, ?, ?, ?, ?, 'novo')"
);
$consulta->execute([
    $produto['id'],
    $nome,
    $telefoneNumeros,
    $quantidade,
    $observacao !== '' ? $observacao : null,
]);

// Deu certo: nao precisa mais devolver os dados ao formulario.
unset($_SESSION['encomenda_dados']);

// --- 4. Monta a mensagem e abre o WhatsApp da Carol -----------------------
$enderecoDoProduto = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http')
                   . '://' . $_SERVER['HTTP_HOST']
                   . url('produto.php?id=' . $produto['id']);

$mensagem  = "Oi, Carol! Vim pelo site.\n\n";
$mensagem .= "Produto: {$produto['nome']}\n";
$mensagem .= 'Preco: ' . formatarPreco($produto['preco']) . "\n";
$mensagem .= "Quantidade: {$quantidade}\n";
$mensagem .= "Meu nome: {$nome}\n";
if ($observacao !== '') {
    $mensagem .= "Observacao: {$observacao}\n";
}
$mensagem .= "\nLink: {$enderecoDoProduto}";

redirecionar(linkWhatsApp($mensagem));
