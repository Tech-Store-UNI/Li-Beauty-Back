<?php

$host = $_ENV['DB_HOST'];
$banco = $_ENV['DB_BANK'];
$usuario = $_ENV['DB_USR'];
$senha = $_ENV['DB_PASSWORD'];

try {
    // Configuração do DSN (Data Source Name)
    $dsn = "mysql:host=$host;dbname=$banco;charset=utf8mb4";

    //  Criando a conexão do PDO
    $pdo = new PDO($dsn, $usuario, $senha);

    // Configuração de Segurança e erros.
    $pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    // Tratamento de erro (caso a conexão falhe)
    echo "Erro na conexão: " . $e->getMessage();
    exit; // Para a execução do código
}
?>