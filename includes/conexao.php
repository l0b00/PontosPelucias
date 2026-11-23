<?php
// Conexão com o banco.
// Padrão: MariaDB do XAMPP (usuário root, sem senha).
// Na hospedagem, crie includes/config.php (copiando config.exemplo.php) com os dados do painel.
// O config.php não vai para o GitHub (está no .gitignore).
$host  = 'localhost';
$banco = 'pontospelucias';
$usuario = 'root';
$senha = '';

if (file_exists(__DIR__ . '/config.php')) {
    require __DIR__ . '/config.php';
}

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro ao conectar no banco de dados. Tente novamente mais tarde.');
}

// Atalho para escapar texto antes de colocar no HTML
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
