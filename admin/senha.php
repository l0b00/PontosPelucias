<?php
/**
 * admin/senha.php - A Carol troca a propria senha
 *
 * Pede a senha atual de proposito: se alguem deixar o computador aberto,
 * essa pessoa ainda assim nao consegue trocar a senha sem saber a antiga.
 */

require __DIR__ . '/proteger.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    exigirCsrf();

    $senhaAtual  = $_POST['senha_atual'] ?? '';
    $senhaNova   = $_POST['senha_nova'] ?? '';
    $senhaRepete = $_POST['senha_repete'] ?? '';

    $erros = [];

    // Busca o hash guardado para conferir a senha atual.
    $consulta = $pdo->prepare('SELECT senha_hash FROM admins WHERE id = ?');
    $consulta->execute([$_SESSION['admin_id']]);
    $hashGuardado = $consulta->fetchColumn();

    if (!$hashGuardado || !password_verify($senhaAtual, $hashGuardado)) {
        $erros[] = 'A senha atual nao confere.';
    }
    if (strlen($senhaNova) < 8) {
        $erros[] = 'A nova senha precisa ter pelo menos 8 caracteres.';
    }
    if ($senhaNova !== $senhaRepete) {
        $erros[] = 'A confirmacao nao e igual a nova senha.';
    }
    if ($senhaNova !== '' && $senhaNova === $senhaAtual) {
        $erros[] = 'A nova senha precisa ser diferente da atual.';
    }

    if ($erros) {
        definirFlash('erro', implode(' ', $erros));
    } else {
        // password_hash gera um hash novo (com "sal" aleatorio) a cada chamada.
        $novoHash = password_hash($senhaNova, PASSWORD_DEFAULT);
        $pdo->prepare('UPDATE admins SET senha_hash = ? WHERE id = ?')
            ->execute([$novoHash, $_SESSION['admin_id']]);

        // Troca o numero da sessao depois de mudar a senha, por seguranca.
        session_regenerate_id(true);

        definirFlash('sucesso', 'Senha alterada. Use a nova no proximo login.');
        redirecionar(url('admin/index.php'));
    }
}

$tituloAdmin = 'Alterar senha';
$paginaAtiva = 'senha';
require __DIR__ . '/includes/header.php';
?>

<div class="cartao" style="max-width:460px">
  <h2>Trocar a senha de <?= e($adminEmail) ?></h2>

  <form method="post" action="<?= e(url('admin/senha.php')) ?>">
    <?= csrfCampo() ?>

    <div class="campo">
      <label for="senha_atual">Senha atual</label>
      <input type="password" id="senha_atual" name="senha_atual" required
             autocomplete="current-password">
    </div>

    <div class="campo">
      <label for="senha_nova">Nova senha</label>
      <input type="password" id="senha_nova" name="senha_nova" required minlength="8"
             autocomplete="new-password">
      <small>Pelo menos 8 caracteres. Misture letras e numeros.</small>
    </div>

    <div class="campo">
      <label for="senha_repete">Repita a nova senha</label>
      <input type="password" id="senha_repete" name="senha_repete" required minlength="8"
             autocomplete="new-password">
    </div>

    <button type="submit" class="botao" style="width:auto">Salvar nova senha</button>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
