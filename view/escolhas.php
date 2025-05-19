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
  <link rel="stylesheet" href="assets/css/escolha.css">
   <title>Agendamento Clínico</title>
</head>
<body>
    <header>
        <div class="logo">
            <h2><a href="../main.html">VITTA</a></h2>
        </div>
        <nav>
            <ul>
                <li><a href="sobrenos.html">Sobre nós</a></li>
                <li><a href="#especialidades">Especialidades</a></li>
                <li><a href="#contato">Contato</a></li>
            </ul>
        </nav>
    </header>   

  <h1>Agendamento Clínico</h1>
  <div class="container">
    <div class="card">
      <h2>Consultas presenciais</h2>
      <ul class="button-list">
        <li><a href="#">Oftalmologista</a></li>
        <li><a href="#">Clínico Geral</a></li>
        <li><a href="#">Dermatologista</a></li>
        <li><a href="#">Psiquiatra</a></li>
        <li><a href="#">Ginecologista</a></li>
      </ul>
    </div>

    <div class="card">
      <h2>Teleconsulta</h2>
      <ul class="button-list">
        <li><a href="#">Psicólogo</a></li>
        <li><a href="#">Nutricionista</a></li>
        <li><a href="#">Dermatologista</a></li>
        <li><a href="#">Endocrinologista</a></li>
        <li><a href="#">Clínico Geral</a></li>
      </ul>
    </div>

    <div class="card">
      <h2>Exames de imagem</h2>
      <ul class="button-list">
        <li><a href="#">Radiografia Tórax</a></li>
        <li><a href="#">Ecografia Aparelho Urinário</a></li>
        <li><a href="#">Ecografia Transvaginal</a></li>
        <li><a href="#">Raio X Joelho</a></li>
        <li><a href="#">Tomografia de Tórax</a></li>
      </ul>
    </div>

    <div class="card">
      <h2>Exames laboratoriais</h2>
      <ul class="button-list">
        <li><a href="#">Hemograma</a></li>
        <li><a href="#">Creatinina</a></li>
        <li><a href="#">TSH</a></li>
        <li><a href="#">Lipidograma</a></li>
        <li><a href="#">Vitamina D</a></li>
      </ul>
    </div>
  </div>

</body>
</html>
