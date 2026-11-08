<?php 
require_once __DIR__ . '/../config/Conexao.php';

class UsuarioController {
    public function ListarUsuarios() {
        try {
            global $pdo;

            if ($pdo === null) {
                echo json_encode(["erro" => true, "mensagem" => "Erro interno de conexão com o banco."]);
                return;
            }

            $sql = "SELECT id, Nome, Email, Telefone FROM usuarios ORDER BY id ASC";
            $start = $pdo->prepare($sql);
            $start->execute();

            $usuarios = $start->fetchAll(PDO::FETCH_ASSOC);

            if (count($usuarios) > 0) {
                echo json_encode([
                    "erro" => false,
                    "total" => count($usuarios),
                    "usuarios" => $usuarios
                ]);
            } else {
                echo json_encode([
                    "erro" => false,
                    "total" => "Nenhum usuário encontrado",
                    "usuarios" => []
                ]);
            }
        } catch (PDOException $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro SQL: ". $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro PHP: ". $e->getMessage()]);
        }
    }
}
?>