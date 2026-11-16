<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';

$controller = new UsuarioController();
$metodo = $_SERVER['REQUEST_METHOD'];

if ($metodo === 'GET') {
    $controller->ListarUsuarios();
} elseif ($metodo === 'POST') {
    $controller->atualizarPerfil();
} else {
    http_response_code(405);
    echo json_encode(["erro" => true, "mensagem" => "Método não suportado"]);
    exit;
}