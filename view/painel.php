<?php
session_start();
$usuario = $_SESSION['usuario'] ?? 'Visitante';

// Evita acesso sem login
if (!isset($_SESSION['id'])) {
    header("Location: loginpage.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Usuário</title>
    <link rel="stylesheet" href="../style.css"> <!-- Caminho do CSS principal -->
</head>
<body>
    <header>
        <nav>
            <ul>
                <li><a href="main.html">Início</a></li>
                <li><a href="escolhas.php">Agendar</a></li>
                <li><a href="#" onclick="alert('Função em desenvolvimento')">Consultas</a></li>
                <li><a href="logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <div class="mid">
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario); ?>!</h1>
        <div class="botoes" style="text-align: center; margin-top: 40px;">
            <a href="escolhas.php" class="button">Agendar Consulta</a>
            <a href="#" class="button" onclick="alert('Função em desenvolvimento')">Ver Consultas</a>
            <a href="logout.php" class="button">Sair</a>
        </div>
    </div>
</body>
</html>
