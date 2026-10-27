<?php
/**
 * admin/banner_excluir.php - Esconde ou exclui um banner
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/banners.php'));
}

exigirCsrf();

$id   = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? '';

$consulta = $pdo->prepare('SELECT id, titulo, imagem, ativo FROM banners WHERE id = ?');
$consulta->execute([$id]);
$banner = $consulta->fetch();

if (!$banner) {
    definirFlash('erro', 'Banner nao encontrado.');
    redirecionar(url('admin/banners.php'));
}

if ($acao === 'alternar') {
    $novoValor = $banner['ativo'] ? 0 : 1;
    $pdo->prepare('UPDATE banners SET ativo = ? WHERE id = ?')->execute([$novoValor, $id]);

    definirFlash('sucesso', $novoValor ? 'Banner publicado.' : 'Banner escondido da home.');
    redirecionar(url('admin/banners.php'));
}

if ($acao === 'excluir') {
    $pdo->prepare('DELETE FROM banners WHERE id = ?')->execute([$id]);
    apagarImagem('banners', $banner['imagem']);

    definirFlash('sucesso', 'Banner "' . $banner['titulo'] . '" excluido.');
    redirecionar(url('admin/banners.php'));
}

definirFlash('erro', 'Acao desconhecida.');
redirecionar(url('admin/banners.php'));
