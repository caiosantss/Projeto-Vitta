<?php
session_start();
if (isset($_SESSION['id'])) {
    header("Location: painelCliente.php");
    exit();
}

// Recupera erros da sessão
$erros = $_SESSION['cadastro_erros'] ?? [];
unset($_SESSION['cadastro_erros']);

// Recupera valores preenchidos (para não perder os dados do usuário)
$valores = $_SESSION['cadastro_valores'] ?? [];
unset($_SESSION['cadastro_valores']);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Cadastro</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/cadastrostyle.css">
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <img src="assets/img/vitta-logo.png" alt="VITTA Logo">
        </div>
        <h2>Crie sua conta</h2>
        <p class="subtitle">Preencha seus dados para se cadastrar</p>
        
        <?php if (!empty($erros)): ?>
            <div class="error-message">
                <?php foreach ($erros as $erro): ?>
                    <p><i class="fas fa-exclamation-circle"></i> <?= $erro ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
        
        <form action="cadastro.php" method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nome" placeholder="Nome completo" value="<?= htmlspecialchars($valores['nome'] ?? '') ?>" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="E-mail" value="<?= htmlspecialchars($valores['email'] ?? '') ?>" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" id="cpf" name="cpf" placeholder="CPF" maxlength="14" value="<?= htmlspecialchars($valores['cpf'] ?? '') ?>" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="senha" placeholder="Senha" required>
            </div>
            
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirma-senha" placeholder="Confirme a senha" required>
            </div>
            
            <button type="submit">Cadastrar</button>
            
            <div class="link">
                <p>Já tem uma conta? <a href="loginpage.php">Entrar</a></p>
            </div>
        </form>
    </div>
    <script src="assets/js/validacaocadastro.js"></script>
</body>
</html>