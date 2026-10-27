<?php
/**
 * admin/includes/menu.php - Menu lateral do painel
 *
 * No computador ele fica fixo na esquerda; no celular vira o mesmo menu
 * que desliza (o JavaScript de main.js cuida do abrir/fechar).
 */

// Cada item: endereco => [rotulo, chave para destacar]
$itensDoMenu = [
    'admin/index.php'      => ['Inicio',     'inicio'],
    'admin/produtos.php'   => ['Produtos',   'produtos'],
    'admin/categorias.php' => ['Categorias', 'categorias'],
    'admin/colecoes.php'   => ['Colecoes',   'colecoes'],
    'admin/banners.php'    => ['Banners',    'banners'],
    'admin/pedidos.php'    => ['Pedidos',    'pedidos'],
    'admin/senha.php'      => ['Alterar senha', 'senha'],
];
?>
<nav class="menu-lateral painel-menu" id="menuLateral" aria-label="Menu do painel">
  <button type="button" class="menu-fechar" id="fecharMenu" aria-label="Fechar menu">&times;</button>

  <span class="painel-logo">Ponntos &amp; Pel&uacute;cias</span>
  <p class="painel-usuario">Logada como <strong><?= e($adminNome) ?></strong></p>

  <ul>
    <?php foreach ($itensDoMenu as $endereco => $item): ?>
      <li>
        <a href="<?= e(url($endereco)) ?>"
           class="<?= $paginaAtiva === $item[1] ? 'ativo' : '' ?>">
          <?= e($item[0]) ?>
        </a>
      </li>
    <?php endforeach; ?>
    <li><a href="<?= e(url('admin/logout.php')) ?>" class="sair">Sair</a></li>
  </ul>
</nav>
