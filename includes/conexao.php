<?php
/**
 * conexao.php - Conexao unica com o MySQL usando PDO
 *
 * Todas as paginas do site incluem este arquivo e usam a variavel $pdo.
 * Nenhuma outra parte do projeto abre conexao com o banco.
 */

require_once __DIR__ . '/config.php';

$dsn = 'mysql:host=' . BD_HOST . ';dbname=' . BD_NOME . ';charset=utf8mb4';

$opcoes = [
    // Erro de SQL vira excecao (e nao um aviso silencioso que passa batido).
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // fetch() devolve array com nomes de coluna, sem indices numericos repetidos.
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    // false = o MySQL prepara a consulta de verdade, em vez do PHP "fingir"
    // que preparou. E isso que garante a protecao contra SQL Injection.
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, BD_USUARIO, BD_SENHA, $opcoes);
} catch (PDOException $erro) {
    // Em producao nao se mostra a mensagem tecnica para o visitante.
    http_response_code(500);
    echo '<!doctype html><html lang="pt-br"><meta charset="utf-8">';
    echo '<title>Erro de conexao</title>';
    echo '<p style="font-family:sans-serif;padding:2rem">';
    echo 'Nao foi possivel conectar ao banco de dados. ';
    echo 'Verifique se o MySQL do XAMPP esta ligado e se o banco "' . BD_NOME . '" ';
    echo 'foi importado (database/schema.sql).';
    echo '</p>';
    // Durante o desenvolvimento ajuda ver o motivo exato:
    if (ini_get('display_errors')) {
        echo '<pre style="font-family:monospace;padding:0 2rem;color:#a00">';
        echo htmlspecialchars($erro->getMessage(), ENT_QUOTES, 'UTF-8');
        echo '</pre>';
    }
    exit;
}
