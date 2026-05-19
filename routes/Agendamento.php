<?php
require_once __DIR__ . '/../controllers/AgendamentoController.php';

$controller == new AgendamentoController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $controller->agendar();
} else {
    http_response_code(405);
    echo_json_encode(['erro' => true, "mensagem" => "Método não permitido!"]);
    
}
?>