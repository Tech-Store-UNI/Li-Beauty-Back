<?php

use Firebase\JWT\JWT;

require_once __DIR__ . '/../config/Conexao.php';

class LoginController {
  
// Lógica para cadastrar
public function Cadastro() {
    global $pdo; // Acessa a variavel para a conexão com o banco

    //  Recebe os dados do formulário
    $json = file_get_contents('php://input');
    $dados = json_decode($json);

    // Verifica se os campos chegam vazios
    if(empty($dados->nome) || empty($dados->email)  || empty($dados->senha)) {
        echo json_encode(["erro" => true, "mensagem" => "Preencha todos os campos!"]);
        return; 
    }
    try {
        // Criptografa a senha e salva no Banco
        $senhaSegura = password_hash($dados->senha, PASSWORD_DEFAULT);
        $sql = "INSERT INTO Usuarios (Nome, Email, Senha) VALUES (:nome, :email, :senha)";
        $comando = $pdo->prepare($sql);

        // Substitui os valores dos parametros
        $comando->bindValue(':nome', $dados->nome);
        $comando->bindValue(':email', $dados->email);
        $comando->bindValue(':senha', $senhaSegura);

        $comando->execute();

        // Avisa o React que deu tudo certo
        echo json_encode(["erro" => false, "mensagem" => "Usuário cadastrado com sucesso!"]);
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro ao cadastrar usuário: ". $e->getMessage()]);
    }
}


// Lógica para Login
public function Login() {
    global  $pdo;

    $json = file_get_contents('php://input');
    $dados = json_decode($json);

    if(empty($dados->usuario) || empty($dados->senha)) {
        return;
    }
    try {
        // Busca o usuário pelo email ou nome
        $sql = "SELECT * FROM Usuarios WHERE Email = :email OR Nome = :nome";
        $comando = $pdo->prepare($sql);
        $comando->bindValue(':email', $dados->usuario);
        $comando->bindValue(':nome', $dados->usuario);
        $comando->execute();

        // Pega o resultado e deixa o array limpo
        $usuario = $comando->fetch(PDO::FETCH_ASSOC);

        // Verifica se o usuário e senha existem
        if($usuario && password_verify($dados->senha, $usuario['Senha'])) {
           
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
                    "Perfil" => $perfil
                ]
            ]);
        } else {
            // Caso email, usuário ou senha não existir, será retornado um erro padrão
            echo json_encode(["erro" => true, "mensagem" => "Usuário ou senha inválidos!"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["erro" => true, "mensagem" => "Erro no banco: " . $e->getMessage()]);
    }
    }
}
?>