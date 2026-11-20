<?php
$senha_pura = "1234";

$hash_gerado = password_hash($senha_pura, PASSWORD_DEFAULT);

echo $hash_gerado;
?>