<?php
/**
 * admin/colecao_form.php - Cria e edita colecao
 * E quase igual ao categoria_form.php; a diferenca e o campo "descricao".
 */

require __DIR__ . '/proteger.php';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$colecao = ['nome' => '', 'descricao' => '', 'imagem' => null, 'ordem' => 0, 'ativo' => 1];

if ($editando) {
    $consulta = $pdo->prepare('SELECT * FROM colecoes WHERE id = ?');
    $consulta->execute([$id]);
    $encontrada = $consulta->fetch();

    if (!$encontrada) {
        definirFlash('erro', 'Colecao nao encontrada.');
        redirecionar(url('admin/colecoes.php'));
    }
    $colecao = $encontrada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $nome      = trim($_POST['nome'] ?? '');
    $descricao = trim($_POST['descricao'] ?? '');
    $ordem     = (int) ($_POST['ordem'] ?? 0);
    $ativo     = isset($_POST['ativo']) ? 1 : 0;

    $erros = [];
    if (mb_strlen($nome, 'UTF-8') < 2) {
        $erros[] = 'Escreva o nome da colecao.';
    }
    if (mb_strlen($nome, 'UTF-8') > 100) {
        $erros[] = 'O nome passou de 100 caracteres.';
    }
    if (mb_strlen($descricao, 'UTF-8') > 500) {
        $erros[] = 'A descricao passou de 500 caracteres.';
    }

    $imagem = $colecao['imagem'];
    $imagemAntiga = $colecao['imagem'];

    if (houveEnvioDeArquivo($_FILES['imagem'] ?? [])) {
        $erroUpload = null;
        $novaImagem = salvarImagem($_FILES['imagem'], 'colecoes', $erroUpload);

        if ($novaImagem === null) {
            $erros[] = $erroUpload;
        } else {
            $imagem = $novaImagem;
        }
    }

    if ($erros) {
        definirFlash('erro', implode(' ', $erros));
        $colecao = array_merge($colecao, [
            'nome' => $nome, 'descricao' => $descricao, 'ordem' => $ordem, 'ativo' => $ativo,
        ]);
    } else {
        $slug = slugUnico($pdo, 'colecoes', gerarSlug($nome), $editando ? $id : null);

        if ($editando) {
            $pdo->prepare(
                'UPDATE colecoes SET nome = ?, slug = ?, descricao = ?, imagem = ?,
                        ordem = ?, ativo = ?
                  WHERE id = ?'
            )->execute([$nome, $slug, $descricao, $imagem, $ordem, $ativo, $id]);
        } else {
            $pdo->prepare(
                'INSERT INTO colecoes (nome, slug, descricao, imagem, ordem, ativo)
                 VALUES (?, ?, ?, ?, ?, ?)'
            )->execute([$nome, $slug, $descricao, $imagem, $ordem, $ativo]);
            $id = (int) $pdo->lastInsertId();
        }

        if ($imagem !== $imagemAntiga) {
            apagarImagem('colecoes', $imagemAntiga);
        }

        definirFlash('sucesso', 'Colecao salva.');
        redirecionar(url('admin/colecoes.php'));
    }
}

$tituloAdmin = $editando ? 'Editar colecao' : 'Nova colecao';
$paginaAtiva = 'colecoes';
require __DIR__ . '/includes/header.php';
?>

<p><a class="botao-pequeno" href="<?= e(url('admin/colecoes.php')) ?>">&larr; Voltar para a lista</a></p>

<form method="post" enctype="multipart/form-data"
      action="<?= e(url('admin/colecao_form.php' . ($editando ? '?id=' . $id : ''))) ?>">
  <?= csrfCampo() ?>

  <div class="cartao">
    <div class="campo">
      <label for="nome">Nome *</label>
      <input type="text" id="nome" name="nome" maxlength="100" required
             value="<?= e($colecao['nome']) ?>">
    </div>

    <div class="campo">
      <label for="descricao">Descricao</label>
      <textarea id="descricao" name="descricao" maxlength="500"
                placeholder="Uma frase curta explicando a colecao."><?= e($colecao['descricao']) ?></textarea>
    </div>

    <div class="linha-campos">
      <div class="campo">
        <label for="ordem">Ordem de exibicao</label>
        <input type="number" id="ordem" name="ordem" min="0" max="999"
               value="<?= (int) $colecao['ordem'] ?>">
      </div>

      <div class="campo caixa-checkbox" style="align-items:center">
        <label>
          <input type="checkbox" name="ativo" value="1" <?= $colecao['ativo'] ? 'checked' : '' ?>>
          Publicada no site
        </label>
      </div>
    </div>

    <div class="campo">
      <label for="imagem">Imagem da colecao</label>
      <?php if (!empty($colecao['imagem'])): ?>
        <div class="previa">
          <img class="previa-imagem" src="<?= e(urlUpload('colecoes', $colecao['imagem'])) ?>"
               alt="Imagem atual da colecao">
        </div>
        <small>Enviar uma nova imagem substitui esta.</small>
      <?php endif; ?>
      <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp"
             data-previa="previaColecao">
      <small>Esta imagem aparece no carrossel de colecoes da home.</small>
      <div class="previa" id="previaColecao"></div>
    </div>
  </div>

  <div class="botoes-formulario">
    <button type="submit" class="botao">Salvar colecao</button>
    <a class="botao botao-secundario" href="<?= e(url('admin/colecoes.php')) ?>">Cancelar</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
