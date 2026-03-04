<?php

session_start(); //inciamos la sesion


if (!isset($_SESSION['user_id'])) {//vemos si la sesion trae datos
    header("Location: login.php"); //si no los trae lo redirigimos al login.
    exit(); 
}
$nombre_usuario = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); //La pagina es una simple pagina en la que vemos que usaurio somos y podemos cerrar sesion.
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Área Privada</title>
    <link rel="stylesheet" href="style.css"> </head>
<body>

    <div class="login-container" style="max-width: 600px; text-align: center;">
        <h1>Bienvenido al Panel de Control</h1>
        
        <div style="margin: 2rem 0; padding: 1rem; background-color: #f3f4f6; border-radius: 4px;">
            <p>Has iniciado sesión con éxito como: <strong><?= $nombre_usuario ?></strong></p>
        </div>

        <form action="logout.php" method="post">
            <input type="submit" value="Cerrar Sesión" style="background-color: #dc2626;">
        </form>
    </div>

</body>
</html>