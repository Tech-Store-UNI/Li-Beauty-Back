<?php
require_once __DIR__ . '/../config/Conexao.php';

class UsuarioController
{
    public function ListarUsuarios()
    {
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
            echo json_encode(["erro" => true, "mensagem" => "Erro SQL: " . $e->getMessage()]);
        } catch (Exception $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro PHP: " . $e->getMessage()]);
        }
    }

    public function atualizarPerfil()
    {
        global $pdo;

        $id = $_POST['id'] ?? null;
        $nome = $_POST['nome'] ?? '';
        $email = $_POST['email'] ?? '';
        $foto = $_FILES['foto'] ?? null;

        if (!$id) {
            die(json_encode(["sucesso" => false, "mensagem" => "ID do usuário não informado"]));
        }

        $caminho_banco  = null;

        if ($foto && $foto['error'] === UPLOAD_ERR_OK) {
            $extensao = pathinfo($foto['name'], PATHINFO_EXTENSION);
            $nome_unico = uniqid('user_') . '.' . $extensao;
            $caminho_fisico = __DIR__ . '/../uploads/avatars/' . $nome_unico;

            if (move_uploaded_file($foto['tmp_name'], $caminho_fisico)) {
                $caminho_banco = '/uploads/avatars/' . $nome_unico;
            }
        }

        try {
            if ($caminho_banco) {
                $sql = "UPDATE usuarios SET Nome = :nome, Email = :email, avatar_url = :avatar  WHERE id = :id";
                $start = $pdo->prepare($sql);
                $start->bindParam(':avatar', $caminho_banco);
            } else {
                $sql = "UPDATE usuarios SET Nome = :nome, Email = :email WHERE id = :id";
                $start = $pdo->prepare($sql);
            }

            $start->bindParam(':nome', $nome);
            $start->bindParam(':email', $email);
            $start->bindParam(':id', $id);
            $start->execute();

            echo json_encode([
                "sucesso" => true,
                "mensagem" => "Perfil atualizado com successo",
                "nova_foto" => $caminho_banco
            ]);
        } catch (PDOException  $e) {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro  no banco: " . $e->getMessage()]);
        }
    }
}
