<?php
// Conexão com o MariaDB do XAMPP (usuário padrão: root, sem senha).
$host  = 'localhost';
$banco = 'pontospelucias';
$usuario = 'root';
$senha = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$banco;charset=utf8mb4", $usuario, $senha, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    exit('Erro ao conectar no banco de dados. Verifique se o MySQL está ligado no XAMPP e se o arquivo banco/pontospelucias.sql foi importado.');
}

// Atalho para escapar texto antes de colocar no HTML
function e($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}
