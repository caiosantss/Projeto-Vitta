<?php
$servidor = "localhost";
$usuario = "root";
$senha = "";
$banco = "gerenciamento_consultas";

$mysqli = new mysqli($servidor, $usuario, $senha, $banco);

if (!$mysqli) {
    die("Erro na conexão: " . mysqli_connect_error());
}
?>