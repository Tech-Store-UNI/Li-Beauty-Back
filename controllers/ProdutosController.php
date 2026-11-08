<?php
require_once __DIR__ . '/../config/Conexao.php';

class ProdutosController {
    public function ListarProdutos() {
        try {
            global $pdo;

            if ($pdo === null) {
                echo json_encode(["erro" => true, "mensagem" => "Erro interno de conexão com o banco."]);
                return;
            }

            $sql = "SELECT id, CodProd, CodServico, VlrProd FROM produtos ORDER BY id ASC";
            $start = $pdo->prepare($sql);
            $start->execute();

            $produtos = $start->fetchAll(PDO::FETCH_ASSOC);

            if (count($produtos) > 0) {
                echo json_encode([
                    "erro" => false,
                    "total" => count($produtos),
                    "produtos" => $produtos
                ]);
            } else {
                echo json_encode([
                    "erro" => false,
                    "total" => "Nenhum produto encontrado",
                    "produtos" => []
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