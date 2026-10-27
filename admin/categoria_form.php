<?php
/**
 * admin/categoria_form.php - Cria e edita categoria
 * Com ?id= na URL esta editando; sem id, criando.
 */

require __DIR__ . '/proteger.php';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$categoria = ['nome' => '', 'imagem' => null, 'ordem' => 0, 'ativo' => 1];

if ($editando) {
    $consulta = $pdo->prepare('SELECT * FROM categorias WHERE id = ?');
    $consulta->execute([$id]);
    $encontrada = $consulta->fetch();

    if (!$encontrada) {
        definirFlash('erro', 'Categoria nao encontrada.');
        redirecionar(url('admin/categorias.php'));
    }
    $categoria = $encontrada;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $nome  = trim($_POST['nome'] ?? '');
    $ordem = (int) ($_POST['ordem'] ?? 0);
    $ativo = isset($_POST['ativo']) ? 1 : 0;

    $erros = [];
    if (mb_strlen($nome, 'UTF-8') < 2) {
        $erros[] = 'Escreva o nome da categoria.';
    }
    if (mb_strlen($nome, 'UTF-8') > 100) {
        $erros[] = 'O nome passou de 100 caracteres.';
    }

    // --- Imagem (opcional) ---
    $imagem = $categoria['imagem'];
    $imagemAntiga = $categoria['imagem'];

    if (houveEnvioDeArquivo($_FILES['imagem'] ?? [])) {
        $erroUpload = null;
        $novaImagem = salvarImagem($_FILES['imagem'], 'categorias', $erroUpload);

        if ($novaImagem === null) {
            $erros[] = $erroUpload;
        } else {
            $imagem = $novaImagem;
        }
    }

    if ($erros) {
        definirFlash('erro', implode(' ', $erros));
        $categoria = array_merge($categoria, ['nome' => $nome, 'ordem' => $ordem, 'ativo' => $ativo]);
    } else {
        $slug = slugUnico($pdo, 'categorias', gerarSlug($nome), $editando ? $id : null);

        if ($editando) {
            $pdo->prepare(
                'UPDATE categorias SET nome = ?, slug = ?, imagem = ?, ordem = ?, ativo = ?
                  WHERE id = ?'
            )->execute([$nome, $slug, $imagem, $ordem, $ativo, $id]);
        } else {
            $pdo->prepare(
                'INSERT INTO categorias (nome, slug, imagem, ordem, ativo) VALUES (?, ?, ?, ?, ?)'
            )->execute([$nome, $slug, $imagem, $ordem, $ativo]);
            $id = (int) $pdo->lastInsertId();
        }

        // Trocou a imagem? Entao a antiga nao serve mais para ninguem.
        if ($imagem !== $imagemAntiga) {
            apagarImagem('categorias', $imagemAntiga);
        }

        definirFlash('sucesso', 'Categoria salva.');
        redirecionar(url('admin/categorias.php'));
    }
}

$tituloAdmin = $editando ? 'Editar categoria' : 'Nova categoria';
$paginaAtiva = 'categorias';
require __DIR__ . '/includes/header.php';
?>

<p><a class="botao-pequeno" href="<?= e(url('admin/categorias.php')) ?>">&larr; Voltar para a lista</a></p>

<form method="post" enctype="multipart/form-data"
      action="<?= e(url('admin/categoria_form.php' . ($editando ? '?id=' . $id : ''))) ?>">
  <?= csrfCampo() ?>

  <div class="cartao">
    <div class="campo">
      <label for="nome">Nome *</label>
      <input type="text" id="nome" name="nome" maxlength="100" required
             value="<?= e($categoria['nome']) ?>">
      <small>O endereco da pagina (slug) e criado automaticamente a partir do nome.</small>
    </div>

    <div class="linha-campos">
      <div class="campo">
        <label for="ordem">Ordem de exibicao</label>
        <input type="number" id="ordem" name="ordem" min="0" max="999"
               value="<?= (int) $categoria['ordem'] ?>">
        <small>Menor numero aparece primeiro na home.</small>
      </div>

      <div class="campo caixa-checkbox" style="align-items:center">
        <label>
          <input type="checkbox" name="ativo" value="1" <?= $categoria['ativo'] ? 'checked' : '' ?>>
          Publicada no site
        </label>
      </div>
    </div>

    <div class="campo">
      <label for="imagem">Imagem redonda da home</label>
      <?php if (!empty($categoria['imagem'])): ?>
        <div class="previa">
          <img class="previa-imagem" src="<?= e(urlUpload('categorias', $categoria['imagem'])) ?>"
               alt="Imagem atual da categoria">
        </div>
        <small>Enviar uma nova imagem substitui esta.</small>
      <?php endif; ?>
      <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp"
             data-previa="previaCategoria">
      <small>JPG, PNG ou WEBP, ate 3 MB. Funciona melhor com imagem quadrada.</small>
      <div class="previa" id="previaCategoria"></div>
    </div>
  </div>

  <div class="botoes-formulario">
    <button type="submit" class="botao">Salvar categoria</button>
    <a class="botao botao-secundario" href="<?= e(url('admin/categorias.php')) ?>">Cancelar</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
