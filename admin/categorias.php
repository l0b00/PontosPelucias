<?php
/**
 * admin/categorias.php - Lista das categorias
 */

require __DIR__ . '/proteger.php';

// Junto de cada categoria, conta quantos produtos estao ligados a ela.
$categorias = $pdo->query(
    'SELECT c.id, c.nome, c.slug, c.imagem, c.ordem, c.ativo,
            (SELECT COUNT(*) FROM produtos p WHERE p.categoria_id = c.id) AS quantidade
       FROM categorias c
      ORDER BY c.ordem ASC, c.nome ASC'
)->fetchAll();

$tituloAdmin = 'Categorias';
$paginaAtiva = 'categorias';
require __DIR__ . '/includes/header.php';
?>

<div class="acoes-topo">
  <p style="margin:0;color:var(--texto-suave);font-size:.9rem">
    A ordem define a posicao na home (menor numero aparece primeiro).
  </p>
  <a class="botao" style="width:auto;padding:11px 22px"
     href="<?= e(url('admin/categoria_form.php')) ?>">+ Nova categoria</a>
</div>

<?php if ($categorias): ?>
  <div class="tabela-rolagem">
    <table class="lista">
      <thead>
        <tr>
          <th>Imagem</th><th>Nome</th><th>Endereco (slug)</th>
          <th>Produtos</th><th>Ordem</th><th>Situacao</th><th>Acoes</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categorias as $categoria): ?>
          <tr>
            <td>
              <img class="miniatura" src="<?= e(urlUpload('categorias', $categoria['imagem'])) ?>"
                   alt="Imagem de <?= e($categoria['nome']) ?>" loading="lazy">
            </td>
            <td><?= e($categoria['nome']) ?></td>
            <td><code><?= e($categoria['slug']) ?></code></td>
            <td><?= (int) $categoria['quantidade'] ?></td>
            <td><?= (int) $categoria['ordem'] ?></td>
            <td>
              <span class="etiqueta <?= $categoria['ativo'] ? 'etiqueta-ativo' : 'etiqueta-inativo' ?>">
                <?= $categoria['ativo'] ? 'no ar' : 'escondida' ?>
              </span>
            </td>
            <td>
              <div class="acoes-linha">
                <a class="botao-pequeno"
                   href="<?= e(url('admin/categoria_form.php?id=' . (int) $categoria['id'])) ?>">Editar</a>

                <form method="post" action="<?= e(url('admin/categoria_excluir.php')) ?>">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="alternar">
                  <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
                  <button type="submit" class="botao-pequeno">
                    <?= $categoria['ativo'] ? 'Esconder' : 'Publicar' ?>
                  </button>
                </form>

                <form method="post" action="<?= e(url('admin/categoria_excluir.php')) ?>"
                      onsubmit="return confirm('Excluir esta categoria?');">
                  <?= csrfCampo() ?>
                  <input type="hidden" name="acao" value="excluir">
                  <input type="hidden" name="id" value="<?= (int) $categoria['id'] ?>">
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
    <h2>Nenhuma categoria cadastrada</h2>
    <p>As categorias sao as bolinhas redondas da home. Crie a primeira.</p>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
