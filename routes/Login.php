<?php
require_once __DIR__ . '/../controllers/LoginController.php';

$acao = $_GET['acao'] ?? 'login';

$controller = new LoginController();
switch ($acao) {
    case 'cadastrar':
        $controller->Cadastro();
        break;

        case 'login':
            $controller->Login();
            break;

            default:
            echo json_encode(["erro" => true, "mensagem" => "Ação inválida no Login"]);
            break;
}
?>