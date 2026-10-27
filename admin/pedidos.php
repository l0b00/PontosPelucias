<?php
/**
 * admin/pedidos.php - Lista dos pedidos que chegaram pelo site
 * Pode filtrar por situacao: pedidos.php?status=novo
 */

require __DIR__ . '/proteger.php';

$status = $_GET['status'] ?? '';
// So aceita um status que exista mesmo na lista do sistema.
if (!array_key_exists($status, statusDePedido())) {
    $status = '';
}

$sql = 'SELECT pe.id, pe.nome_cliente, pe.telefone, pe.quantidade, pe.status, pe.criado_em,
               p.id AS produto_id, p.nome AS produto_nome
          FROM pedidos pe
          LEFT JOIN produtos p ON p.id = pe.produto_id';

$parametros = [];
if ($status !== '') {
    $sql .= ' WHERE pe.status = ?';
    $parametros[] = $status;
}
$sql .= ' ORDER BY pe.criado_em DESC, pe.id DESC';

$consulta = $pdo->prepare($sql);
$consulta->execute($parametros);
$pedidos = $consulta->fetchAll();

$tituloAdmin = 'Pedidos';
$paginaAtiva = 'pedidos';
require __DIR__ . '/includes/header.php';
?>

<div class="acoes-topo">
  <form method="get" action="<?= e(url('admin/pedidos.php')) ?>">
    <label for="status" class="so-leitor">Filtrar por situacao</label>
    <select name="status" id="status">
      <option value="">Todas as situacoes</option>
      <?php foreach (statusDePedido() as $chave => $rotulo): ?>
        <option value="<?= e($chave) ?>" <?= $status === $chave ? 'selected' : '' ?>>
          <?= e($rotulo) ?>
        </option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="botao-pequeno">Filtrar</button>
  </form>

  <p style="margin:0;color:var(--texto-suave);font-size:.9rem">
    <?= count($pedidos) ?> pedido(s) nesta lista
  </p>
</div>

<?php if ($pedidos): ?>
  <div class="tabela-rolagem">
    <table class="lista">
      <thead>
        <tr>
          <th>Quando</th><th>Cliente</th><th>Produto</th>
          <th>Qtd</th><th>Situacao</th><th>Acoes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($pedidos as $pedido): ?>
          <tr>
            <td><?= e(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></td>
            <td>
              <?= e($pedido['nome_cliente']) ?><br>
              <small style="color:var(--texto-suave)"><?= e($pedido['telefone']) ?></small>
            </td>
            <td>
              <?php if ($pedido['produto_id']): ?>
                <a href="<?= e(url('produto.php?id=' . (int) $pedido['produto_id'])) ?>"
                   target="_blank" rel="noopener"><?= e($pedido['produto_nome']) ?></a>
              <?php else: ?>
                <span style="color:var(--texto-suave)">produto removido</span>
              <?php endif; ?>
            </td>
            <td><?= (int) $pedido['quantidade'] ?></td>
            <td>
              <span class="etiqueta status-<?= e($pedido['status']) ?>">
                <?= e(statusDePedido()[$pedido['status']]) ?>
              </span>
            </td>
            <td>
              <div class="acoes-linha">
                <a class="botao-pequeno"
                   href="<?= e(url('admin/pedido_ver.php?id=' . (int) $pedido['id'])) ?>">Ver</a>
                <a class="botao-pequeno"
                   href="<?= e(linkWhatsAppCliente($pedido['telefone'],
                        'Oi, ' . $pedido['nome_cliente'] . '! Aqui e a Carol, da Ponntos & Pelucias.')) ?>"
                   target="_blank" rel="noopener">WhatsApp</a>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="cartao">
    <h2>Nenhum pedido nesta lista</h2>
    <p>
      <?= $status !== ''
            ? 'Nenhum pedido com essa situacao. Experimente "Todas as situacoes".'
            : 'Quando alguem preencher o formulario de encomenda no site, o pedido aparece aqui.' ?>
    </p>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
