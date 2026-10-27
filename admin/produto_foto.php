<?php
/**
 * admin/produto_foto.php - Apaga UMA foto de um produto
 *
 * Recebe POST (com token CSRF) vindo do formulario de edicao do produto.
 * Apaga a linha do banco E o arquivo do disco.
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/produtos.php'));
}

exigirCsrf();

$fotoId    = (int) ($_POST['foto_id'] ?? 0);
$produtoId = (int) ($_POST['produto_id'] ?? 0);

// Busca a foto conferindo que ela pertence mesmo a este produto.
$consulta = $pdo->prepare('SELECT id, arquivo, principal FROM produto_fotos WHERE id = ? AND produto_id = ?');
$consulta->execute([$fotoId, $produtoId]);
$foto = $consulta->fetch();

if (!$foto) {
    definirFlash('erro', 'Foto nao encontrada.');
    redirecionar(url('admin/produto_form.php?id=' . $produtoId));
}

// 1. Tira a linha do banco
$pdo->prepare('DELETE FROM produto_fotos WHERE id = ?')->execute([$foto['id']]);

// 2. Tira o arquivo do disco
apagarImagem('produtos', $foto['arquivo']);

// 3. Se a foto apagada era a principal, promove a proxima da fila.
if ($foto['principal']) {
    $proxima = $pdo->prepare(
        'SELECT id FROM produto_fotos WHERE produto_id = ? ORDER BY ordem ASC, id ASC LIMIT 1'
    );
    $proxima->execute([$produtoId]);
    $novaPrincipal = $proxima->fetchColumn();

    if ($novaPrincipal) {
        $pdo->prepare('UPDATE produto_fotos SET principal = 1 WHERE id = ?')
            ->execute([$novaPrincipal]);
    }
}

definirFlash('sucesso', 'Foto apagada.');
redirecionar(url('admin/produto_form.php?id=' . $produtoId));
