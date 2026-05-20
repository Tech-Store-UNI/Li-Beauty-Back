<?php

require_once __DIR__ . '/../vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv -> load();

$host = $_ENV['DB_HOST'];
$banco = $_ENV['DB_BANK'];
$usuario = $_ENV['DB_USR'];
$senha = $_ENV['DB_PASSWORD'];

try {

    $dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";
    $pdo = new PDO($dsn, $usuario, $senha);
    $pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro na conexão: " . $e->getMessage();
    exit; 
}
?>