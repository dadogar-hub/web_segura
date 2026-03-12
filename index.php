<?php

session_start(); //inciamos la sesion


if (!isset($_SESSION['user_id'])) {//vemos si la sesion trae datos
    $_SESSION['alerta'] = "Inicia Sesion para Comenzar";
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
        <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'ROLE_ADMIN'): ?>
                <a href="admin.php" class="btn-admin" style="background-color: #1d4ed8; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
                    PANEL ADMIN
                </a>
            <?php endif; ?>
        <form action="logout.php" method="post">
            <input type="submit" value="Cerrar Sesión" style="background-color: #dc2626;">
        </form>
        <?php if (isset($_SESSION['alerta'])): ?>
            <div style="background-color: #fee2e2; color: #dc2626; padding: 1rem; border: 1px solid #fecaca; margin-bottom: 1rem; border-radius: 4px; text-align: center;">
                <?= htmlspecialchars($_SESSION['alerta'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php unset($_SESSION['alerta']); ?>
    <?php endif; ?>
    </div>


</body>
</html>