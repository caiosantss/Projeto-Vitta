<?php
session_start();
session_unset(); 
session_destroy(); // destrói a sessão

header("Location: loginpage.php");
exit();
?>
