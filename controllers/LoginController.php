<?php

use Firebase\JWT\JWT;

require_once __DIR__ . '/../config/Conexao.php';

class LoginController
{

    // Lógica para cadastrar
    public function Cadastro()
    {
        global $pdo; // Acessa a variavel para a conexão com o banco

        //  Recebe os dados do formulário
        $json = file_get_contents('php://input');
        $dados = json_decode($json);

        // Verifica se os campos chegam vazios
        if (empty($dados->nome) || empty($dados->email)  || empty($dados->senha)) {
            echo json_encode(["erro" => true, "mensagem" => "Preencha todos os campos!"]);
            return;
        }
        try {
            // Criptografa a senha e salva no Banco
            $senhaSegura = password_hash($dados->senha, PASSWORD_DEFAULT);
            $sql = "INSERT INTO usuarios (Nome, Email, Senha, Perfil) VALUES (:nome, :email, :senha, :perfil)";
            $comando = $pdo->prepare($sql);

            // Substitui os valores dos parametros
            $comando->bindValue(':nome', $dados->nome);
            $comando->bindValue(':email', $dados->email);
            $comando->bindValue(':senha', $senhaSegura);
            $comando->bindValue(':perfil', 'cliente');

            $comando->execute();

            // Avisa o React que deu tudo certo
            echo json_encode(["erro" => false, "mensagem" => "Usuário cadastrado com sucesso!"]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro ao cadastrar usuário: " . $e->getMessage()]);
            exit;
        }
    }


    // Lógica para Login
    public function Login()
    {
        global  $pdo;

        $json = file_get_contents('php://input');
        $dados = json_decode($json);

        if (empty($dados->usuario) || empty($dados->senha)) {
            return;
        }
        try {
            // Busca o usuário pelo email ou nome
            $sql = "SELECT * FROM usuarios WHERE Email = :email OR Nome = :nome";
            $comando = $pdo->prepare($sql);
            $comando->bindValue(':email', $dados->usuario);
            $comando->bindValue(':nome', $dados->usuario);
            $comando->execute();

            // Pega o resultado e deixa o array limpo
            $usuario = $comando->fetch(PDO::FETCH_ASSOC);

            // Verifica se o usuário e senha existem
            if ($usuario && password_verify($dados->senha, $usuario['Senha'])) {

                // Lógica para o usuário inativo, caso o campo ativo seja 0, o usuário não pode acessar o sistema
                $sql = "SELECT ativo FROM usuarios WHERE id = :id";
                $stmt = $pdo->prepare($sql);
                $stmt->bindValue(':id', $usuario['id'], PDO::PARAM_INT);
                $stmt->execute();
                $usuarioAtivo = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($usuarioAtivo && $usuarioAtivo['ativo'] == 0) {
                    http_response_code(403);
                    echo json_encode([
                        "erro" => true,
                        "bloqueado" => true,
                        "mensagem" => "Usuário inátivo, por favor, finalizar seu agendamento através do WhatsApp, obrigada!"
                    ]);
                    return;
                }

                $perfil = "cliente";
                $tempoValidade = 60 * 60 * 24 * 30;

                if (strtolower($usuario['Nome']) === "admin") {
                    $perfil = "admin";
                    $tempoValidade = 60 * 60;
                }

                $secretKey = $_ENV['JWT_SECRET'];
                $playload = [
                    "iat" => time(),
                    "exp" => time() + $tempoValidade,
                    "data" => [
                        "id" => $usuario["id"],
                        "perfil" => $perfil
                    ]
                ];
                $token = JWT::encode($playload, $secretKey, 'HS256');

                echo json_encode([
                    "usuario" => [
                        "id" => $usuario["id"],
                        "Nome" => $usuario["Nome"],
                        "Email" => $usuario["Email"],
                        "Perfil" => $perfil,
                        "Token" => $token
                    ]
                ]);
                exit;
            } else {
                // Caso email, usuário ou senha não existir, será retornado um erro padrão
                http_response_code(401);
                echo json_encode(["erro" => true, "mensagem" => "Usuário ou senha inválidos!"]);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode(["erro" => true, "mensagem" => "Erro no banco: " . $e->getMessage()]);
            exit;
        }
    }

    public function loginGoogle()
    {
        global $pdo;

        $id_token = $_POST['id_token'] ?? null;

        if (!$id_token) {
            die(json_encode(["sucesso" => false, "mensagem" => "Token do Google não informado"]));
        }

        $url_google = 'https://oauth2.googleapis.com/tokeninfo?id_token=' . $id_token;
        $resposta_google = file_get_contents($url_google);
        $playload = json_decode($resposta_google, true);

        if (isset($playload['error'])) {
            die(json_encode(["sucesso" => false, "mensagem" =>  "Token inválido ou expirado"]));
        }

        $email = $playload['email'];
        $nome = $playload['name'];
        $foto_google = $playload['picture'];

        try { // Login google (teste)
            $sql = "SELECT id, avatar_url FROM usuarios WHERE Email = :email";
            $start = $pdo->prepare($sql);
            $start->bindParam(':email', $email);
            $start->execute();
            $usuario = $start->fetch(PDO::FETCH_ASSOC);

            if ($usuario) {
                echo json_encode([
                    "sucesso" => true,
                    "mensagem" => "Login realizado com sucesso",
                    "id_usuario" => $usuario['id']
                ]);
                exit;
            } else {   // cadastro google (teste)
                $senhaVazia = '';

                $sql = "INSERT INTO usuarios (Nome, Email, Senha, avatar_url) VALUES (:nome, :email, :senha, :avatar)";
                $start = $pdo->prepare($sql);
                $start->bindParam(':nome', $nome);
                $start->bindParam(':email', $email);
                $start->bindParam(':senha', $senhaVazia);
                $start->bindParam(':avatar', $foto_google);
                $start->execute();

                echo json_encode([
                    "sucesso" => true,
                    "mensagem" => "Usuário cadastrado e logado com sucesso",
                    "id_usuario" => $pdo->lastInsertId()
                ]);
                exit;
            }
        } catch (PDOException $e) {
            echo json_encode(["sucesso" => false, "mensagem" => "Erro no banco: " . $e->getMessage()]);
            exit;
        }
    }
}
