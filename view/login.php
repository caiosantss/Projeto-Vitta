<?php
session_start();
include('../model/conexao.php'); // Conexão define $mysqli

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email'] ?? '');
    $senha = $_POST['senha'] ?? '';

    if (empty($email)) {
        echo "Preencha seu email.";
        exit;
    }

    if (empty($senha)) {
        echo "Preencha sua senha.";
        exit;
    }

    // Consulta com prepared statement
    $stmt = $mysqli->prepare("SELECT id, email, senha, tipo_usuario FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['email'] = $usuario['email'];
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] == 1) {
                header("Location: tabelamedicoinfo.html");
            } else {
                header("Location: painel.php");
            }
            exit();
        }
    }

    echo "Usuário ou senha incorretos.";
}

$mysqli->close();
?>
