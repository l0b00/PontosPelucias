<?php
/**
 * admin/pedido_ver.php - Detalhes de um pedido
 * Mostra tudo que o cliente escreveu e permite trocar a situacao.
 */

require __DIR__ . '/proteger.php';

$id = (int) ($_GET['id'] ?? 0);

$consulta = $pdo->prepare(
    'SELECT pe.*, p.id AS produto_id, p.nome AS produto_nome, p.preco AS produto_preco
       FROM pedidos pe
       LEFT JOIN produtos p ON p.id = pe.produto_id
      WHERE pe.id = ?'
);
$consulta->execute([$id]);
$pedido = $consulta->fetch();

if (!$pedido) {
    definirFlash('erro', 'Pedido nao encontrado.');
    redirecionar(url('admin/pedidos.php'));
}

// Mensagem pronta para a Carol mandar para o cliente.
$mensagemCliente = 'Oi, ' . $pedido['nome_cliente'] . '! Aqui e a Carol, da Ponntos & Pelucias. '
                 . 'Recebi sua encomenda';
if ($pedido['produto_nome']) {
    $mensagemCliente .= ' de ' . $pedido['quantidade'] . 'x ' . $pedido['produto_nome'];
}
$mensagemCliente .= '. Vou te passar o valor e o prazo!';

$tituloAdmin = 'Pedido #' . (int) $pedido['id'];
$paginaAtiva = 'pedidos';
require __DIR__ . '/includes/header.php';
?>

<p><a class="botao-pequeno" href="<?= e(url('admin/pedidos.php')) ?>">&larr; Voltar para a lista</a></p>

<div class="cartao">
  <h2>Quem pediu</h2>
  <ul class="ficha">
    <li><strong>Nome</strong><?= e($pedido['nome_cliente']) ?></li>
    <li><strong>Telefone</strong><?= e($pedido['telefone']) ?></li>
    <li><strong>Recebido em</strong><?= e(date('d/m/Y H:i', strtotime($pedido['criado_em']))) ?></li>
  </ul>

  <a class="botao botao-whatsapp" style="width:auto"
     href="<?= e(linkWhatsAppCliente($pedido['telefone'], $mensagemCliente)) ?>"
     target="_blank" rel="noopener">Chamar no WhatsApp</a>
</div>

<div class="cartao">
  <h2>O que foi pedido</h2>
  <ul class="ficha">
    <li>
      <strong>Produto</strong>
      <?php if ($pedido['produto_id']): ?>
        <a href="<?= e(url('produto.php?id=' . (int) $pedido['produto_id'])) ?>"
           target="_blank" rel="noopener"><?= e($pedido['produto_nome']) ?></a>
      <?php else: ?>
        produto removido do catalogo
      <?php endif; ?>
    </li>
    <li><strong>Quantidade</strong><?= (int) $pedido['quantidade'] ?></li>
    <?php if ($pedido['produto_preco'] !== null): ?>
      <li>
        <strong>Valor de tabela</strong>
        <?= e(formatarPreco($pedido['produto_preco'])) ?> a unidade &mdash;
        total <?= e(formatarPreco($pedido['produto_preco'] * $pedido['quantidade'])) ?>
      </li>
    <?php endif; ?>
    <li>
      <strong>Observacao do cliente</strong>
      <?= $pedido['observacao'] !== null && $pedido['observacao'] !== ''
            ? nl2br(e($pedido['observacao']))
            : '<span style="color:var(--texto-suave)">nenhuma</span>' ?>
    </li>
  </ul>
</div>

<div class="cartao">
  <h2>Situacao do pedido</h2>

  <form method="post" action="<?= e(url('admin/pedido_status.php')) ?>">
    <?= csrfCampo() ?>
    <input type="hidden" name="id" value="<?= (int) $pedido['id'] ?>">

    <div class="campo">
      <label for="status">Em que pe esta?</label>
      <select name="status" id="status">
        <?php foreach (statusDePedido() as $chave => $rotulo): ?>
          <option value="<?= e($chave) ?>" <?= $pedido['status'] === $chave ? 'selected' : '' ?>>
            <?= e($rotulo) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <button type="submit" class="botao" style="width:auto">Salvar situacao</button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
