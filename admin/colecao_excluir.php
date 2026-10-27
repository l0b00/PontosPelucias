<?php
/**
 * admin/colecao_excluir.php - Esconde ou exclui uma colecao
 *
 * Aqui a exclusao e permitida mesmo com produtos dentro: o banco apaga apenas
 * as ligacoes (tabela colecao_produto, com ON DELETE CASCADE). Os produtos
 * continuam existindo normalmente.
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/colecoes.php'));
}

exigirCsrf();

$id   = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? '';

$consulta = $pdo->prepare('SELECT id, nome, imagem, ativo FROM colecoes WHERE id = ?');
$consulta->execute([$id]);
$colecao = $consulta->fetch();

if (!$colecao) {
    definirFlash('erro', 'Colecao nao encontrada.');
    redirecionar(url('admin/colecoes.php'));
}

if ($acao === 'alternar') {
    $novoValor = $colecao['ativo'] ? 0 : 1;
    $pdo->prepare('UPDATE colecoes SET ativo = ? WHERE id = ?')->execute([$novoValor, $id]);

    definirFlash('sucesso', $novoValor ? 'Colecao publicada.' : 'Colecao escondida do site.');
    redirecionar(url('admin/colecoes.php'));
}

if ($acao === 'excluir') {
    $pdo->prepare('DELETE FROM colecoes WHERE id = ?')->execute([$id]);
    apagarImagem('colecoes', $colecao['imagem']);

    definirFlash('sucesso', 'Colecao "' . $colecao['nome'] . '" excluida. Os produtos continuam no catalogo.');
    redirecionar(url('admin/colecoes.php'));
}

definirFlash('erro', 'Acao desconhecida.');
redirecionar(url('admin/colecoes.php'));
