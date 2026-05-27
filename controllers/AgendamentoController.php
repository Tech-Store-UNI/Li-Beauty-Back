<?php

require_once __DIR__ . '/../config/Conexao.php';

class AgendamentoController {
    public function salvarAgendamento() {
        $dados = json_decode(file_get_contents('php://input'));

        if(empty($dados->idUsuario) || empty($dados->dataInicio) || empty($dados->dataFim)) {
            echo json_encode(["erro" => true, "mensagem" => "Preencha todos os campos!"]);
            return;
        }

        try {
            global $pdo;

            if ($pdo === null) {
                echo json_encode(["erro" => true, "mensagem" => "Erro de conexão com o banco de dados."]);
                return;
            }

            $sql = "CALL sp_GerenciarAgendamento_v2(:p_idUsuario, :p_dataInicio, :p_dataFim, :p_telefone, :p_idAgendamentoExistente, :p_idProdutoServico)";
            $start = $pdo->prepare($sql);

            $idUsuario = $dados->idUsuario;
            $dataInicio = $dados->dataInicio;
            $dataFim = $dados->dataFim;
            $telefone = !empty($dados->telefone) ? $dados->telefone : '';
            $idExistente = !empty($dados->idAgendamentoExistente) ? $dados->idAgendamentoExistente : null;
            $idProdutoServico = !empty($dados->idProdutoServico) ? $dados->idProdutoServico : null;
         
            $start->bindValue(':p_idUsuario', $idUsuario, PDO::PARAM_INT);
            $start->bindValue(':p_dataInicio', $dataInicio);
            $start->bindValue(':p_dataFim', $dataFim);
            $start->bindValue(':p_telefone', $telefone);

            if ($idExistente) {
               $start->bindParam(':p_idAgendamentoExistente', $idExistente, PDO::PARAM_INT);
            } else {
                $start->bindValue(':p_idAgendamentoExistente', null, PDO::PARAM_NULL);
             }

             if ($idProdutoServico) {
                $start->bindParam(':p_idProdutoServico', $idProdutoServico, PDO::PARAM_INT);
            } else {
                $start->bindValue(':p_idProdutoServico', null, PDO::PARAM_NULL);
            }
             $start->execute();
             $respostaBanco = $start->fetch(PDO::FETCH_ASSOC);

             if ($respostaBanco && $respostaBanco['Resultado'] === 'Sucesso') {
                echo json_encode(["erro" => false, "mensagem" => $respostaBanco['Mensagem']]);
             } else {
                $msgErro = isset($respostaBanco['Mensagem']) ? $respostaBanco['Mensagem'] : 'Não foi possivel realizar o agendamento.';
                echo json_encode(["erro" => true, "mensagem" => $msgErro]);
             }
            } catch (PDOException $e) {
                echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
            }
        }
    }
?>