<?php
session_start();

$acceso_permitido = false;

if (!isset($_SESSION['user_id'])) { //verifico que tenga sesion
    $_SESSION['alerta'] = "Debes iniciar sesión para acceder.";
    header("Location: login.php");
} elseif ($_SESSION['role'] !== 'ROLE_ADMIN') { //que en dicha sesion sea rol admin
    $_SESSION['alerta'] = "Acceso no autorizado.";
    header("Location: index.php");
} else {
    $acceso_permitido = true;
    $nombre_usuario = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
}
// solo si el acceso es permitido renderizamos el HTML
if ($acceso_permitido):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container" style="max-width: 800px; text-align: center;">
        <h1>Panel de Control Administrativo</h1>
        
        <div style="margin: 2rem 0; padding: 1.5rem; background-color: #eff6ff; border-radius: 8px;">
            <p>Bienvenido, Administrador: <strong><?= $nombre_usuario ?></strong></p>
        </div>
    </div>
</body>
</html>
<?php endif; ?>