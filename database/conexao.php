<?php
// Parâmetros de conexão
$servername = "200.9.22.2";
$username = "matheus";
$password = "B4nc0#2024";
$dbname = "biblioteca";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão com o banco de dados: " . $e->getMessage();
    die();
}
?>