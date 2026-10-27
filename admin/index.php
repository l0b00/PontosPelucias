<?php
/**
 * admin/index.php - Inicio do painel
 * Mostra os numeros do site e os 5 pedidos mais recentes.
 */

require __DIR__ . '/proteger.php';

// Cada consulta devolve um numero (fetchColumn pega a primeira coluna).
$produtosAtivos   = (int) $pdo->query('SELECT COUNT(*) FROM produtos WHERE ativo = 1')->fetchColumn();
$produtosInativos = (int) $pdo->query('SELECT COUNT(*) FROM produtos WHERE ativo = 0')->fetchColumn();
$totalCategorias  = (int) $pdo->query('SELECT COUNT(*) FROM categorias')->fetchColumn();
$totalColecoes    = (int) $pdo->query('SELECT COUNT(*) FROM colecoes')->fetchColumn();
$pedidosNovos     = (int) $pdo->query("SELECT COUNT(*) FROM pedidos WHERE status = 'novo'")->fetchColumn();

$ultimosPedidos = $pdo->query(
    'SELECT pe.id, pe.nome_cliente, pe.quantidade, pe.status, pe.criado_em,
            p.nome AS produto_nome
       FROM pedidos pe
       LEFT JOIN produtos p ON p.id = pe.produto_id
      ORDER BY pe.criado_em DESC, pe.id DESC
      LIMIT 5'
)->fetchAll();

$tituloAdmin = 'Inicio';
$paginaAtiva = 'inicio';
require __DIR__ . '/includes/header.php';
?>

<p>Ola, <?= e($adminNome) ?>! Este e o resumo da loja hoje.</p>

<div class="cartoes-numeros">
  <a class="cartao-numero" href="<?= e(url('admin/produtos.php')) ?>">
    <strong><?= $produtosAtivos ?></strong>
    <span>produtos no ar</span>
  </a>
  <a class="cartao-numero" href="<?= e(url('admin/produtos.php')) ?>">
    <strong><?= $produtosInativos ?></strong>
    <span>produtos escondidos</span>
  </a>
  <a class="cartao-numero" href="<?= e(url('admin/categorias.php')) ?>">
    <strong><?= $totalCategorias ?></strong>
    <span>categorias</span>
  </a>
  <a class="cartao-numero" href="<?= e(url('admin/colecoes.php')) ?>">
    <strong><?= $totalColecoes ?></strong>
    <span>colecoes</span>
  </a>
</div>

<div class="cartao">
  <h2>Pedidos novos: <?= $pedidosNovos ?></h2>
  <?php if ($pedidosNovos > 0): ?>
    <p>Tem gente esperando resposta no WhatsApp.</p>
  <?php else: ?>
    <p>Nenhum pedido novo esperando. Tudo em dia!</p>
  <?php endif; ?>
  <a class="botao-pequeno" href="<?= e(url('admin/pedidos.php?status=novo')) ?>">Ver pedidos novos</a>
</div>

<div class="cartao">
  <h2>Ultimos 5 pedidos</h2>

  <?php if ($ultimosPedidos): ?>
    <div class="tabela-rolagem">
      <table class="lista">
        <thead>
          <tr>
            <th>Quando</th>
            <th>Cliente</th>
            <th>Produto</th>
            <th>Qtd</th>
            <th>Situacao</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($ultimosPedidos as $pedido): ?>
            <tr>
              <td><?= e(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></td>
              <td><?= e($pedido['nome_cliente']) ?></td>
              <td><?= e($pedido['produto_nome'] ?? 'produto removido') ?></td>
              <td><?= (int) $pedido['quantidade'] ?></td>
              <td>
                <span class="etiqueta status-<?= e($pedido['status']) ?>">
                  <?= e(statusDePedido()[$pedido['status']] ?? $pedido['status']) ?>
                </span>
              </td>
              <td>
                <a class="botao-pequeno"
                   href="<?= e(url('admin/pedido_ver.php?id=' . (int) $pedido['id'])) ?>">Ver</a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php else: ?>
    <p>Ainda nao chegou nenhum pedido pelo site.</p>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
