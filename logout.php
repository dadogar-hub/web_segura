<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // retomamos la sesión para poder destruirla
}

$_SESSION = array();//vaciamos el array de sesión en memoria

if (ini_get("session.use_cookies")) {//destruimos la cookie de sesión en el navegador
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000, // expiración en el pasado para forzar el borrado
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

session_destroy();//destruimos los datos de la sesión en el servidor

header("Location: login.php"); //redirect al login
?>