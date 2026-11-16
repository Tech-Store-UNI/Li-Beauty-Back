<?php
require_once __DIR__ . '/../config/Conexao.php';

class ContatoController
{
    public function enviarMensagem()
    {
        $dados = json_decode(file_get_contents("php://input"));

        if (empty($dados->nome) || empty($dados->email) || empty($dados->mensagem)) {
            echo json_encode(["erro" => true, "mensagem" => "Preencha todos os campos do formulário"]);
            return;
        }

        try {
            global $pdo;

            $sql = "INSERT INTO mensagemcontato (Nome, Email, Mensagem, Telefone) VALUES (:nome, :email, :mensagem, :telefone)";
            $start = $pdo->prepare($sql);

            $start->bindParam(':nome', $dados->nome);
            $start->bindParam(':email', $dados->email);
            $start->bindParam(':mensagem', $dados->mensagem);
            $start->bindParam(':telefone', $dados->telefone);

            $start->execute();

            echo json_encode(["erro" => false, "mensagem" => "Mensagem salva com sucesso"]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(["erro" => true, "mensagem" => $e->getMessage()]);
            exit;
        }
    }
}
