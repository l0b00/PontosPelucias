<?php
/**
 * admin/produto_excluir.php - Exclui ou esconde um produto
 *
 * Recebe POST com o campo "acao":
 *   acao = alternar -> liga/desliga o produto (some do site sem apagar nada)
 *   acao = excluir  -> apaga o produto, as fotos do banco e os arquivos
 *
 * Nunca por GET: um link de exclusao poderia ser clicado sem querer (ou
 * acessado por um robo) e apagaria o produto.
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/produtos.php'));
}

exigirCsrf();

$id   = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? '';

$consulta = $pdo->prepare('SELECT id, nome, ativo FROM produtos WHERE id = ?');
$consulta->execute([$id]);
$produto = $consulta->fetch();

if (!$produto) {
    definirFlash('erro', 'Produto nao encontrado.');
    redirecionar(url('admin/produtos.php'));
}

// --- Esconder / publicar --------------------------------------------------
if ($acao === 'alternar') {
    $novoValor = $produto['ativo'] ? 0 : 1;
    $pdo->prepare('UPDATE produtos SET ativo = ? WHERE id = ?')->execute([$novoValor, $id]);

    definirFlash('sucesso', $novoValor
        ? 'Produto publicado no site.'
        : 'Produto escondido do site (continua salvo aqui).');
    redirecionar(url('admin/produtos.php'));
}

// --- Excluir de vez -------------------------------------------------------
if ($acao === 'excluir') {
    // Avisa quando existem pedidos ligados a este produto: nesses casos e
    // melhor esconder, para nao perder o historico de quem encomendou.
    $contagem = $pdo->prepare('SELECT COUNT(*) FROM pedidos WHERE produto_id = ?');
    $contagem->execute([$id]);
    $pedidosLigados = (int) $contagem->fetchColumn();

    // Pega os nomes dos arquivos ANTES de apagar as linhas do banco.
    $consulta = $pdo->prepare('SELECT arquivo FROM produto_fotos WHERE produto_id = ?');
    $consulta->execute([$id]);
    $arquivos = $consulta->fetchAll(PDO::FETCH_COLUMN);

    // O banco apaga sozinho as linhas de produto_fotos e colecao_produto
    // (ON DELETE CASCADE no schema.sql) e deixa pedidos.produto_id como NULL.
    $pdo->prepare('DELETE FROM produtos WHERE id = ?')->execute([$id]);

    foreach ($arquivos as $arquivo) {
        apagarImagem('produtos', $arquivo);
    }

    $mensagem = 'Produto "' . $produto['nome'] . '" excluido.';
    if ($pedidosLigados > 0) {
        $mensagem .= " Atencao: {$pedidosLigados} pedido(s) antigo(s) ficaram sem o link do produto.";
    }

    definirFlash('sucesso', $mensagem);
    redirecionar(url('admin/produtos.php'));
}

definirFlash('erro', 'Acao desconhecida.');
redirecionar(url('admin/produtos.php'));
