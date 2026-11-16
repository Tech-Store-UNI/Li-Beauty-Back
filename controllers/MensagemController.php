<?php 
require_once __DIR__ . '/../config/Conexao.php';

class MensagemController {
    public function ListarMensagem() {
        try {
            global $pdo;

            if ($pdo === null) {
                echo json_encode(["erro" => true, "mensagem" => "Erro interno de conexão com o banco."]);
                return;
            }

            $sql = "SELECT id, Nome, Email, Mensagem, DtEnv, Lida, Telefone FROM mensagemcontato ORDER BY id ASC";
            $start = $pdo->prepare($sql);
            $start->execute();

            $mensagens = $start->fetchAll(PDO::FETCH_ASSOC);

            if (count($mensagens) > 0) {
                echo json_encode([
                    "erro" => false,
                    "total" => count($mensagens),
                    "mensagens" => $mensagens
                ]);
                exit;
            } else {
                echo json_encode([
                    "erro" => false,
                    "total" => "Nenhum mensagem encontrada",
                    "mensagens" => []
                ]);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro SQL: " . $e->getMessage()]);
            exit;
        } catch (Exception $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro PHP: " . $e->getMessage()]);
            exit;
        }
    }
}
?>