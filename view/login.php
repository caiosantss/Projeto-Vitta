<?php
session_start();
include('../model/conexao.php');

## Verificação da existencia de dados email e senha
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if (empty($_POST['email'])){
        echo "Preencha seu email";
        exit;

    } else if(empty($_POST['senha'])) {  
        echo "Preencha sua senha";
        exit;
    }

    #evitar sqlinjection -> limpa a string
    $email = $mysqli->real_escape_string($_POST['email']);
    $senha = $_POST['senha']; // Não aplicar real_escape_string em senha para usar password_verify corretamente

    $sql_code = "SELECT * FROM usuarios WHERE email = '$email'";
    $sql_result = $mysqli->query($sql_code);

    if ($sql_result->num_rows == 1) {
        $usuario = $sql_result->fetch_assoc();

        if (password_verify($senha, $usuario['senha'])) {
            $_SESSION['email'] = $email;
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            if ($usuario['tipo_usuario'] == 1) {
                header("Location: ##");
            } else {
                header("Location: painelCliente.php");
            }
        } else {
            echo "Usuario ou Senha incorretos";
        }
    } else {
        echo "Usuario ou Senha incorretos";
    }
};

$mysqli->close();
?>