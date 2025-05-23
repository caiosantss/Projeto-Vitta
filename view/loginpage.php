<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: painelCliente.php");
    exit();
}

// Verificar se existe mensagem de erro na sessão
$erro = '';
if (isset($_SESSION['erro_login'])) {
    $erro = $_SESSION['erro_login'];
    unset($_SESSION['erro_login']); // Remove a mensagem após exibir
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
        <p class="subtitle">Entre com seus dados para acessar.</p>
        
        <?php if (!empty($erro)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-triangle"></i>
                <?php echo htmlspecialchars($erro); ?>
            </div>
        <?php endif; ?>
        
        <form action="login.php" method="POST" <?php echo !empty($erro) ? 'class="form-shake"' : ''; ?>>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" id="email" placeholder="E-mail" required 
                       <?php echo !empty($erro) ? 'class="error-input"' : ''; ?>>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" id="senha" placeholder="Senha" required
                       <?php echo !empty($erro) ? 'class="error-input"' : ''; ?>>
            </div>
            
            <button type="submit">Entrar</button>
            
            <div class="link">
                <p>Não tem uma conta? <a href="cadastroPage.php">Cadastre-se</a></p>
            </div>
        </form>
    </div>

    <script src="assets/js/loginpage.js"></script>
</body>
</html>