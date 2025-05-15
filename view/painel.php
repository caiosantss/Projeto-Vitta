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
    <link rel="stylesheet" href="assets/css/painel.css">
</head>
<body>
    <div class="container">
        <h1>Olá, <?php echo htmlspecialchars($usuario); ?>!</h1>
        
        <div class="botoes">
            <a href="escolhas.php" class="botao">Agendar Consulta</a>
            <a href="#" class="botao desativado" onclick="alert('Essa função ainda não está disponível.')">Ver Consultas</a>
            <<a href="logout.php" class="botao sair">Sair</a>

        </div>
    </div>
</body>
</html>
