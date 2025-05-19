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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Painel do Usuário</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/painelCliente.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a>
        </div>
        <nav>
            <ul>
                <li><a href="../main.html">Início</a></li>
                <li><a href="especialidades.html">Especialidades</a></li>
                <li><a href="como-funciona.html">Como funciona</a></li>
                <li><a href="contato.html">Contato</a></li>
            </ul>
        </nav>
        <a href="logout.php" class="button">Sair</a>
    </header>

    <div class="mid">
        <h1>Bem-vindo, <?php echo htmlspecialchars($usuario); ?>!</h1>
        <div class="botoes">
            <a href="escolhas.php" class="botao">
                <i class="fas fa-calendar-plus"></i> Agendar Consulta
            </a>
            <a href="#" class="botao" onclick="alert('Função em desenvolvimento')">
                <i class="fas fa-clipboard-list"></i> Ver Minhas Consultas
            </a>
            <a href="perfil.php" class="botao">
                <i class="fas fa-user-circle"></i> Meu Perfil
            </a>
            <a href="logout.php" class="botao sair">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>

    <script>
        // Script para destacar o item de menu atual
        document.addEventListener('DOMContentLoaded', function() {
            const currentLocation = location.href;
            const menuItems = document.querySelectorAll('nav ul li a');
            
            menuItems.forEach(item => {
                if(item.href === currentLocation) {
                    item.classList.add('active');
                }
            });
        });
    </script>
</body>
</html>