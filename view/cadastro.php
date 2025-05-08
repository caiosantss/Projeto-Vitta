<?php
// Conexão com o banco de dados
session_start();
include('../model/conexao.php');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Conexão falhou: " . $e->getMessage());
}

// Verifica se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = filter_input(INPUT_POST, 'nome', FILTER_SANITIZE_STRING);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma-senha'];

    // Validações do lado do servidor
    $erros = [];
    
    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido. Deve ter pelo menos 3 caracteres.";
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }
    
    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    }
    
    if ($senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem.";
    }

    // Verifica se o email já existe
    $stmt = $conn->prepare("SELECT id FROM usuarios WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $erros[] = "Este e-mail já está cadastrado.";
    }

    // Se não houver erros, prossegue com o cadastro
    if (empty($erros)) {
        try {      
            // Insere os dados no banco
            $stmt = $conn->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (:nome, :email, :senha)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            $stmt->execute();
            
            // Redireciona para a página de login com mensagem de sucesso
            header("Location: loginpage.html?cadastro=sucesso");
            exit();
        } catch(PDOException $e) {
            $erros[] = "Erro ao cadastrar: " . $e->getMessage();
        }
    }
}

// Se houver erros, exibe-os
if (!empty($erros)) {
    foreach ($erros as $erro) {
        echo "<p style='color: red;'>$erro</p>";
    }
}

$mysqli->close();
?>