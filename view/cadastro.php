<?php
session_start();
include('../model/conexao.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $cpf = trim($_POST['cpf'] ?? '');
    $senha = $_POST['senha'] ?? '';
    $confirma_senha = $_POST['confirma-senha'] ?? '';

    // Armazena valores para não perder o que o usuário digitou
    $_SESSION['cadastro_valores'] = [
        'nome' => $nome,
        'email' => $email,
        'cpf' => $cpf
    ];

    $erros = [];

    // Validações
    if (empty($nome) || strlen($nome) < 3) {
        $erros[] = "Nome inválido. Deve ter pelo menos 3 caracteres.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "E-mail inválido. Por favor, insira um e-mail válido.";
    }

    // Remove caracteres não numéricos do CPF
    $cpf_numeros = preg_replace('/[^0-9]/', '', $cpf);
    
    if (empty($cpf_numeros)) {
        $erros[] = "CPF inválido. O CPF deve conter apenas números.";
    } elseif (strlen($cpf_numeros) != 11) {
        $erros[] = "CPF inválido. Deve conter exatamente 11 dígitos.";
    }

    if (strlen($senha) < 8) {
        $erros[] = "A senha deve ter pelo menos 8 caracteres.";
    } elseif (!preg_match('/[A-Z]/', $senha) || !preg_match('/[0-9]/', $senha)) {
        $erros[] = "A senha deve conter pelo menos uma letra maiúscula e um número.";
    }

    if ($senha !== $confirma_senha) {
        $erros[] = "As senhas não coincidem. Por favor, digite a mesma senha nos dois campos.";
    }

    // Verifica se email já está cadastrado
    if (empty($erros)) {
        $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $erros[] = "Este e-mail já está cadastrado. Por favor, use outro e-mail ou faça login.";
        }
    }

    // Se houver erros, redireciona de volta ao formulário
    if (!empty($erros)) {
        $_SESSION['cadastro_erros'] = $erros;
        header("Location: cadastroPage.php");
        exit();
    }

    // Inserção no banco de dados (só executa se não houver erros)
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO usuarios (nome, email, cpf, senha) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nome, $email, $cpf_numeros, $senhaHash);

    if ($stmt->execute()) {
        $_SESSION['cadastro_sucesso'] = "Cadastro realizado com sucesso! Faça login para continuar.";
        header("Location: loginpage.php");
        exit();
    } else {
        $_SESSION['cadastro_erros'] = ["Erro ao cadastrar usuário. Por favor, tente novamente."];
        header("Location: cadastroPage.php");
        exit();
    }
}

$mysqli->close();
?>