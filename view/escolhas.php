<?php
session_start();
if (!isset($_SESSION['id'])) {
  header("Location: loginpage.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>VITTA - Agendamento de Consultas</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../MainStyles.css">
    <link rel="stylesheet" href="assets/css/escolhas-style.css">
</head>
<body>
    <header>
        <div class="container header-container">
            <div class="logo">
               <a href="../main.html"><img src="assets/img/vitta-logo.png" alt="VITTA Logo"></a> 
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn">
                <i class="fas fa-bars"></i>
            </button>
            <nav>
                <ul id="navMenu">
                    <li><a href="sobrenos.html">Sobre nós</a></li>
                    <li><a href="../main.html#especialidades">Especialidades</a></li>
                    <li><a href="../main.html#como-funciona">Como funciona</a></li>
                    <li><a href="../main.html#contato">Contato</a></li>
                    <li><a href="logout.php" class="button">Sair</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="especialidades-container">
        <h1 class="especialidades-titulo">Mais procurados</h1>
        
        <div class="especialidades-grid">
            <div class="especialidade-card">
                <div class="especialidade-header">
                    <div class="especialidade-icon">
                        <i class="fas fa-hospital"></i>
                    </div>
                    <h2>Consultas presenciais</h2>
                </div>
                <ul class="especialidade-lista">
                    <li><a href="#">Oftalmologista</a></li>
                    <li><a href="#">Clínico Geral</a></li>
                    <li><a href="#">Dermatologista</a></li>
                    <li><a href="#">Psiquiatra</a></li>
                    <li><a href="#">Ginecologista</a></li>
                </ul>
            </div>
            
            <div class="especialidade-card">
                <div class="especialidade-header">
                    <div class="especialidade-icon">
                        <i class="fas fa-video"></i>
                    </div>
                    <h2>Teleconsulta</h2>
                </div>
                <ul class="especialidade-lista">
                    <li><a href="#">Psicólogo</a></li>
                    <li><a href="#">Nutricionista</a></li>
                    <li><a href="#">Dermatologista</a></li>
                    <li><a href="#">Endocrinologista</a></li>
                    <li><a href="#">Clínico Geral</a></li>
                </ul>
            </div>
        </div>
    </div>

    <script src="../view/mainjs.js"></script>
</body>
</html>
