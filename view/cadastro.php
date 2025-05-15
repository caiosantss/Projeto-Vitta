<?php
session_start();
include('../model/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma-senha'] ?? '';

    $erros = [];

    // Validações
    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido. Deve ter pelo menos 3 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido.";
    }

    if (empty($cpf) || !preg_match('/^\d{11}$/', $cpf)) {
        $erros[] = "CPF inválido. Deve conter exatamente 11 números (sem pontos ou traços).";
    }

    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    }

    if ($senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem.";
    }

    // Verifica se email já está cadastrado
    if (empty($erros)) {
        $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erros[] = "Este e-mail já está cadastrado.";
        }
    }

    // Inserção no banco de dados
    if (empty($erros)) {
        $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

        $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, cpf, senha) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $nome, $email, $cpf, $senhaHash);

        if ($stmt->execute()) {
            header("Location: loginpage.html?cadastro=sucesso");
            exit();
        } else {
            $erros[] = "Erro ao cadastrar usuário.";
        }
    }

    // Exibição de erros
    if (!empty($erros)) {
        foreach ($erros as $erro) {
            echo "<p style='color: red;'>$erro</p>";
        }
    }
}

$mysqli->close();
?>
