<?php
/**
 * admin/banner_form.php - Cria e edita banner
 *
 * Diferente de categoria e colecao, aqui a imagem e OBRIGATORIA quando o
 * banner e novo (um banner sem imagem nao tem o que mostrar).
 */

require __DIR__ . '/proteger.php';

$id = (int) ($_GET['id'] ?? 0);
$editando = $id > 0;

$banner = ['titulo' => '', 'imagem' => null, 'link' => '', 'ordem' => 0, 'ativo' => 1];

if ($editando) {
    $consulta = $pdo->prepare('SELECT * FROM banners WHERE id = ?');
    $consulta->execute([$id]);
    $encontrado = $consulta->fetch();

    if (!$encontrado) {
        definirFlash('erro', 'Banner nao encontrado.');
        redirecionar(url('admin/banners.php'));
    }
    $banner = $encontrado;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $titulo = trim($_POST['titulo'] ?? '');
    $link   = trim($_POST['link'] ?? '');
    $ordem  = (int) ($_POST['ordem'] ?? 0);
    $ativo  = isset($_POST['ativo']) ? 1 : 0;

    $erros = [];
    if (mb_strlen($titulo, 'UTF-8') < 2) {
        $erros[] = 'Escreva um titulo (serve de texto alternativo da imagem).';
    }
    if (mb_strlen($titulo, 'UTF-8') > 150) {
        $erros[] = 'O titulo passou de 150 caracteres.';
    }
    if (mb_strlen($link, 'UTF-8') > 255) {
        $erros[] = 'O link passou de 255 caracteres.';
    }

    $imagem = $banner['imagem'];
    $imagemAntiga = $banner['imagem'];

    if (houveEnvioDeArquivo($_FILES['imagem'] ?? [])) {
        $erroUpload = null;
        $novaImagem = salvarImagem($_FILES['imagem'], 'banners', $erroUpload);

        if ($novaImagem === null) {
            $erros[] = $erroUpload;
        } else {
            $imagem = $novaImagem;
        }
    }

    if (!$editando && $imagem === null) {
        $erros[] = 'Escolha a imagem do banner.';
    }

    if ($erros) {
        definirFlash('erro', implode(' ', $erros));
        $banner = array_merge($banner, [
            'titulo' => $titulo, 'link' => $link, 'ordem' => $ordem, 'ativo' => $ativo,
        ]);
    } else {
        if ($editando) {
            $pdo->prepare(
                'UPDATE banners SET titulo = ?, imagem = ?, link = ?, ordem = ?, ativo = ?
                  WHERE id = ?'
            )->execute([$titulo, $imagem, $link !== '' ? $link : null, $ordem, $ativo, $id]);
        } else {
            $pdo->prepare(
                'INSERT INTO banners (titulo, imagem, link, ordem, ativo) VALUES (?, ?, ?, ?, ?)'
            )->execute([$titulo, $imagem, $link !== '' ? $link : null, $ordem, $ativo]);
        }

        if ($imagem !== $imagemAntiga) {
            apagarImagem('banners', $imagemAntiga);
        }

        definirFlash('sucesso', 'Banner salvo.');
        redirecionar(url('admin/banners.php'));
    }
}

$tituloAdmin = $editando ? 'Editar banner' : 'Novo banner';
$paginaAtiva = 'banners';
require __DIR__ . '/includes/header.php';
?>

<p><a class="botao-pequeno" href="<?= e(url('admin/banners.php')) ?>">&larr; Voltar para a lista</a></p>

<form method="post" enctype="multipart/form-data"
      action="<?= e(url('admin/banner_form.php' . ($editando ? '?id=' . $id : ''))) ?>">
  <?= csrfCampo() ?>

  <div class="cartao">
    <div class="campo">
      <label for="titulo">Titulo *</label>
      <input type="text" id="titulo" name="titulo" maxlength="150" required
             value="<?= e($banner['titulo']) ?>">
      <small>Nao aparece escrito na tela: e a descricao da imagem para quem usa leitor de tela.</small>
    </div>

    <div class="campo">
      <label for="link">Para onde o banner leva</label>
      <input type="text" id="link" name="link" maxlength="255"
             placeholder="produto.php?id=1"
             value="<?= e($banner['link'] ?? '') ?>">
      <small>
        Pode ser uma pagina daqui (<code>produto.php?id=1</code>,
        <code>colecao.php?slug=chapeuzinhos</code>) ou um endereco completo
        comecando com https://. Deixe vazio para o banner nao ser clicavel.
      </small>
    </div>

    <div class="linha-campos">
      <div class="campo">
        <label for="ordem">Ordem no carrossel</label>
        <input type="number" id="ordem" name="ordem" min="0" max="999"
               value="<?= (int) $banner['ordem'] ?>">
      </div>

      <div class="campo caixa-checkbox" style="align-items:center">
        <label>
          <input type="checkbox" name="ativo" value="1" <?= $banner['ativo'] ? 'checked' : '' ?>>
          Aparecendo na home
        </label>
      </div>
    </div>

    <div class="campo">
      <label for="imagem">Imagem <?= $editando ? '' : '*' ?></label>
      <?php if (!empty($banner['imagem'])): ?>
        <div class="previa">
          <img class="previa-imagem" style="width:200px;height:90px"
               src="<?= e(urlUpload('banners', $banner['imagem'])) ?>" alt="Banner atual">
        </div>
        <small>Enviar uma nova imagem substitui esta.</small>
      <?php endif; ?>
      <input type="file" id="imagem" name="imagem" accept="image/jpeg,image/png,image/webp"
             <?= $editando ? '' : 'required' ?> data-previa="previaBanner">
      <small>Imagens deitadas (largas) ficam melhores. Ate 3 MB.</small>
      <div class="previa" id="previaBanner"></div>
    </div>
  </div>

  <div class="botoes-formulario">
    <button type="submit" class="botao">Salvar banner</button>
    <a class="botao botao-secundario" href="<?= e(url('admin/banners.php')) ?>">Cancelar</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
