<?php
/**
 * admin/categoria_excluir.php - Esconde ou exclui uma categoria
 *
 * Regra do trabalho: nao deixar excluir categoria que tenha produtos ligados.
 * Nesse caso mostramos um aviso e sugerimos apenas esconder.
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/categorias.php'));
}

exigirCsrf();

$id   = (int) ($_POST['id'] ?? 0);
$acao = $_POST['acao'] ?? '';

$consulta = $pdo->prepare('SELECT id, nome, imagem, ativo FROM categorias WHERE id = ?');
$consulta->execute([$id]);
$categoria = $consulta->fetch();

if (!$categoria) {
    definirFlash('erro', 'Categoria nao encontrada.');
    redirecionar(url('admin/categorias.php'));
}

if ($acao === 'alternar') {
    $novoValor = $categoria['ativo'] ? 0 : 1;
    $pdo->prepare('UPDATE categorias SET ativo = ? WHERE id = ?')->execute([$novoValor, $id]);

    definirFlash('sucesso', $novoValor ? 'Categoria publicada.' : 'Categoria escondida do site.');
    redirecionar(url('admin/categorias.php'));
}

if ($acao === 'excluir') {
    $contagem = $pdo->prepare('SELECT COUNT(*) FROM produtos WHERE categoria_id = ?');
    $contagem->execute([$id]);
    $quantidade = (int) $contagem->fetchColumn();

    if ($quantidade > 0) {
        definirFlash('erro',
            'Nao da para excluir "' . $categoria['nome'] . '": existem ' . $quantidade .
            ' produto(s) nesta categoria. Troque a categoria deles primeiro, ' .
            'ou apenas esconda a categoria do site.');
        redirecionar(url('admin/categorias.php'));
    }

    $pdo->prepare('DELETE FROM categorias WHERE id = ?')->execute([$id]);
    apagarImagem('categorias', $categoria['imagem']);

    definirFlash('sucesso', 'Categoria "' . $categoria['nome'] . '" excluida.');
    redirecionar(url('admin/categorias.php'));
}

definirFlash('erro', 'Acao desconhecida.');
redirecionar(url('admin/categorias.php'));
