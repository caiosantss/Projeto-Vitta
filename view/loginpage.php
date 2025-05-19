<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: painelCliente.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Login</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/loginpage.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/img/vitta-logo.png" alt="VITTA Logo">
        </div>
        <h2>Bem-vindo</h2>
        <p class="subtitle">Entre com seus dados para acessar</p>
        
        <form action="login.php" method="POST">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="E-mail" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" id="senha" placeholder="Senha" required>
            </div>
            
            <button type="submit">Entrar</button>
            
            <div class="link">
                <p>Não tem uma conta? <a href="cadastroPage.php">Cadastre-se</a></p>
            </div>
        </form>
    </div>
</body>
</html>