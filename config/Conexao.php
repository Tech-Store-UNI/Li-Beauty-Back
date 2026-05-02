<?php

$servidor = 'DESKTOP-V8GUBC5';
$banco = 'Libeauty';
// $usuario = 'DESKTOP-V8GUBC5\Admin';
// $senha = '';

try {
    // Configuração do DSN (Data Source Name)
    $dsn = "sqlsrv:Server=$servidor;Database=$banco";

    //  Criando a conexão do PDO
    $pdo = new PDO($dsn, "", "");

    // Configuração de Segurança e erros.
    $pdo ->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {

    // Tratamento de erro (caso a conexão falhe)
    echo "Erro na conexão: " . $e->getMessage();
    exit; // Para a execução do código
}
?>