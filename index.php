<?php

use Dotenv\Dotenv;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Content-Type: application/json; charset=utf-8');
// header("Access-Control-Allow-Headers: Content-Type");

if($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv -> load();

$rota_completa = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$partes = explode('/', trim($rota_completa, '/'));

$rota = end($partes);

switch ($rota) {
    case 'login':
        require_once __DIR__ . '/routes/Login.php';
        break;

        case 'contato':
        require_once __DIR__ . '/routes/Contato.php';
        break;

        case 'agendamento':
            require_once __DIR__ . '/routes/Agendamento.php';
            break;

            case '':
                echo json_encode(["mensagem" => "A API está online!"]);
                break;

                default:
                // Caso a rota não seja reconhecida, r
                http_response_code(404);
                echo json_encode(["erro" => true, "mensagem" => "Rota [$rota] não encontrada"]);
                break;
}
?>