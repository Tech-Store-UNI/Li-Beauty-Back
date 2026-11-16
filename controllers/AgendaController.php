<?php
require_once __DIR__ . '/../config/Conexao.php';

class AgendaController
{
    public function ListarAgenda()
    {
        try {
            global $pdo;

            if ($pdo === null) {
                echo json_encode(["erro" => true, "mensagem" => "Erro interno de conexão com o banco."]);
                return;
            }

            $sql = "SELECT id, idUsr, Nome, Email, DtAgend, DtAgendFim, Status, Telefone, id_produto_servico FROM agendamento ORDER BY id ASC";
            $start = $pdo->prepare($sql);
            $start->execute();

            $agenda = $start->fetchAll(PDO::FETCH_ASSOC);

            if (count($agenda) > 0) {
                echo json_encode([
                    "erro" => false,
                    "total" => count($agenda),
                    "agenda" => $agenda
                ]);
                exit;
            } else {
                echo json_encode([
                    "erro" => false,
                    "total" => "Nenhum agendamento encontrado",
                    "agenda" => []
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
