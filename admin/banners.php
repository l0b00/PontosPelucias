<?php
/**
 * admin/banners.php - Lista dos banners do carrossel da home
 */

require __DIR__ . '/proteger.php';

$banners = $pdo->query(
    'SELECT id, titulo, imagem, link, ordem, ativo FROM banners ORDER BY ordem ASC, id ASC'
)->fetchAll();

$tituloAdmin = 'Banners';
$paginaAtiva = 'banners';
require __DIR__ . '/includes/header.php';
?>

<div class="acoes-topo">
  <p style="margin:0;color:var(--texto-suave);font-size:.9rem">
    Sao as imagens grandes que giram no topo da home, a cada 5 segundos.
  </p>
  <a class="botao" style="width:auto;padding:11px 22px"
     href="<?= e(url('admin/banner_form.php')) ?>">+ Novo banner</a>
</div>

<?php if ($banners): ?>
  <div class="tabela-rolagem">
    <table class="lista">
      <thead>
        <tr><th>Imagem</th><th>Titulo</th><th>Link</th><th>Ordem</th><th>Situacao</th><th>Acoes</th></tr>
      </thead>
      <tbody>
        <?php foreach ($banners as $banner): ?>
          <tr>
            <td>
              <img class="miniatura" src="<?= e(urlUpload('banners', $banner['imagem'])) ?>"
                   alt="Banner <?= e($banner['titulo']) ?>" loading="lazy">
            </td>
            <td><?= e($banner['titulo']) ?></td>
            <td>
              <?php if (!empty($banner['link'])): ?>
                <code><?= e($banner['link']) ?></code>
              <?php else: ?>
                <span style="color:var(--texto-suave)">sem link</span>
              <?php endif; ?>
            </td>
            <td><?= (int) $banner['ordem'] ?></td>
            <td>
              <span class="etiqueta <?= $banner['ativo'] ? 'etiqueta-ativo' : 'etiqueta-inativo' ?>">
                <?= $banner['ativo'] ? 'no ar' : 'escondido' ?>
              </span>
            </td>
            <td>
              <div class="acoes-linha">
                <a class="botao-pequeno"
                   href="<?= e(url('admin/banner_form.php?id=' . (int) $banner['id'])) ?>">Editar</a>

                <form method="post" action="<?= e(url('admin/banner_excluir.php')) ?>">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="alternar">
                  <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                  <button type="submit" class="botao-pequeno">
                    <?= $banner['ativo'] ? 'Esconder' : 'Publicar' ?>
                  </button>
                </form>

                <form method="post" action="<?= e(url('admin/banner_excluir.php')) ?>"
                      onsubmit="return confirm('Excluir este banner?');">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="excluir">
                  <input type="hidden" name="id" value="<?= (int) $banner['id'] ?>">
                  <button type="submit" class="botao-pequeno perigo">Excluir</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
<?php else: ?>
  <div class="cartao">
    <h2>Nenhum banner cadastrado</h2>
    <p>Sem banner, a home comeca direto pela faixa de diferenciais.</p>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
