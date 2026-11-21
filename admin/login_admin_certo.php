<?php
// Inicia a sessão no topo absoluto do arquivo
session_start();

// Inclui a conexão com o banco de dados
require_once 'includes/conexao.php';

$erro = '';

// Verifica se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!empty($usuario) && !empty($senha)) {
        try {
            // Busca o administrador no banco de dados pelo e-mail/usuário
            $sql = "SELECT * FROM administradores WHERE email = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$usuario]);
            $admin = $stmt->fetch(PDO::FETCH_ASSOC);

            // Verifica se o admin existe e se a senha confere
            if ($admin && password_verify($senha, $admin['senha'])) {
                // Configura as variáveis de sessão de acesso
                $_SESSION['is_admin'] = true;
                $_SESSION['admin_email'] = $admin['email'];
                
                // Força o redirecionamento para o painel administrativo
                header('Location: index_admin.php');
                exit;
            } else {
                $erro = "Usuário ou senha incorretos!";
            }
        } catch (Exception $e) {
            $erro = "Não foi possível entrar agora. Tente novamente.";
        }
    } else {
        $erro = "Por favor, preencha todos os campos.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Administrador - Pontos e Pelúcias</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <h2 class="titulo_login">Login Admin</h2>

        <!-- Exibe mensagem de erro na tela caso exista -->
        <?php if (!empty($erro)): ?>
            <div class="erro-msg">
                <?= htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>

        <!-- ATENÇÃO: O action aponta exatamente para o nome deste arquivo -->
        <form class="login" action="login_admin_certo.php" method="POST">
            <div class="usuario">
                <label class="usuario_txt" for="usuario">Usuário ou E-mail:</label><br>
                <input class="usuario_box" type="text" id="usuario" name="usuario" required>
            </div>
            
            <br>

            <div class="senha">
                <label class="senha_txt" for="senha">Senha:</label><br>
                <input class="senha_box" type="password" id="senha" name="senha" required>
            </div>

            <br>

            <button class="entrar" type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>