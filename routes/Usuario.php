<?php
require_once __DIR__ . '/../controllers/UsuarioController.php';

$controller = new UsuarioController();
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $controller->ListarUsuarios();
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->atualizarPerfil();
} else {
    echo json_encode(["erro" => true, "mensagem" => "Método HTTP não suportado."]);
}