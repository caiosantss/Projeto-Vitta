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

        $email = $mysqli->real_escape_string($_POST ['email']);
        $senha = $mysqli->real_escape_string($_POST ['senha']);

        $sql_code = "SELECT * FROM usuarios WHERE email = '$email' AND senha = '$senha'";
        $sql_result = $mysqli->query($sql_code);

        if ($sql_result->num_rows == 1) {
            #Login bem sucedido
                $usuario = $sql_result->fetch_assoc();
                $_SESSION['email'] = $email;
                $_SESSION['id'] = $usuario['id'];
                $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

                #Separando redirecionamento de medicos e usuarios
                if ($usuario['tipo_usuario'] == 1) {
                    header("Location: ../main.html");
                }else {
                    header("Location: painel.php");
                }        
        }else {
            echo "Usuario ou Senha incorretos";
        }
};

$mysqli->close();

?>