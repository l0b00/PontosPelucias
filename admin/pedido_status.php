<?php
/**
 * admin/pedido_status.php - Troca a situacao de um pedido
 * Recebe o POST da tela de detalhes (pedido_ver.php).
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar(url('admin/pedidos.php'));
}

exigirCsrf();

$id     = (int) ($_POST['id'] ?? 0);
$status = $_POST['status'] ?? '';

// So aceita um dos status da lista oficial: nada de valor inventado.
if (!array_key_exists($status, statusDePedido())) {
    definirFlash('erro', 'Situacao invalida.');
    redirecionar(url('admin/pedido_ver.php?id=' . $id));
}

$consulta = $pdo->prepare('SELECT id FROM pedidos WHERE id = ?');
$consulta->execute([$id]);

if (!$consulta->fetch()) {
    definirFlash('erro', 'Pedido nao encontrado.');
    redirecionar(url('admin/pedidos.php'));
}

$pdo->prepare('UPDATE pedidos SET status = ? WHERE id = ?')->execute([$status, $id]);

definirFlash('sucesso', 'Situacao alterada para "' . statusDePedido()[$status] . '".');
redirecionar(url('admin/pedido_ver.php?id=' . $id));
