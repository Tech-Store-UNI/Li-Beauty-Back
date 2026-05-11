<?php
require_once __DIR__ . '/../controllers/ContatoController.php';

$controller = new ContatoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->enviarMensagem();
} else {
    http_response_code(405);
    echo json_encode(['erro' => true, "mensagem" => "Método não permitido!"]);
}
?>