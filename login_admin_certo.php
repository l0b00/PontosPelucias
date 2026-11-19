<?php
session_start();

require_once 'includes/conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario = trim($_POST['usuario'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (!empty($usuario) && !empty($senha)) {
        // Busca o administrador no banco de dados pelo e-mail
        $sql = "SELECT * FROM administradores WHERE email = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$usuario]);
        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se o admin existe e se a senha confere com o hash do banco
        if ($admin && password_verify($senha, $admin['senha'])) {
            $_SESSION['is_admin'] = true;
            $_SESSION['admin_email'] = $admin['email'];
            
            // Redireciona para o painel administrativo principal na mesma pasta admin/
            header('Location: index_admin.php');
            exit;
        } else {
            $erro = "Usuário ou senha incorretos!";
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
    <!-- Como está na pasta admin/, o CSS na raiz é chamado voltando uma pasta -->
    <link rel="stylesheet" href="style.css">

    <link href="https://fonts.googleapis.com/css2?family=DynaPuff:wght@600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@300..700&display=swap" rel="stylesheet">
</head>

<body>
    <div class="login-container">
        <h2 class="titulo_login">Login Admin</h2>

        <!-- Exibe mensagem de erro caso exista -->
        <?php if (!empty($erro)): ?>
            <div style="color: #d9534f; margin-bottom: 10px; font-weight: 500;"><?= e($erro); ?></div>
        <?php endif; ?>

        <form class="login" action="login_admin.php" method="POST">
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

            <button class="entrar" type="submit"><a class="botao_entrar" href="index_admin.php">Entrar</a></button>
        </form>
    </div>
</body>
</html>