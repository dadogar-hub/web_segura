<?php
session_start(); // Iniciamos sesión para gestionar las alertas

require 'constantes.php'; // importamos las variables de entorno.
require 'fuctions.php';

$error = 0;
$registro_exitoso = false;
$mensajes = [
    1 => 'El nombre de usuario no es válido (3-30 caracteres alfanuméricos).',
    2 => 'Las contraseñas no coinciden.',
    3 => 'La contraseña no cumple los requisitos mínimos.',
    4 => 'El usuario o el correo ya están registrados.',
    5 => 'Error interno. Inténtalo más tarde.',
    6 => 'El formato del correo electrónico no es válido.'
];

// Recogemos las entradas del formulario si la petición es post
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Inicializamos y saneamos variables
    $USERNAME = htmlspecialchars($_POST['USERNAME'], ENT_QUOTES, 'UTF-8');
    $EMAIL = filter_var($_POST['EMAIL'], FILTER_SANITIZE_EMAIL);
    $PASSWORD = $_POST['PASSWORD'];
    $PASSWORD_CONFIRM = $_POST['PASSWORD_CONFIRM'];

    // 1. Validar formato USERNAME (usando tu función de functions.php)
    if (!esNombreUsuarioValido($USERNAME)) {
        $error = 1;
    } 
    // 2. Validar formato EMAIL
    elseif (!filter_var($EMAIL, FILTER_VALIDATE_EMAIL)) {
        $error = 6;
    }
    // 3. Validar coincidencia de contraseñas
    elseif ($PASSWORD != $PASSWORD_CONFIRM) {
        $error = 2;
    }
    // 4. Validar complejidad de contraseña (usando tu función)
    elseif (!validarPass($PASSWORD)) {
        $error = 3;
    }

    // Si no hay errores de validación, procedemos con la base de datos
    if ($error == 0) {
        $dsn = "mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DB . ";charset=utf8mb4";
        
        try {
            $conexionDB = new PDO($dsn, USER, PASSW);
            
            // Verificamos si el usuario o email ya existen para evitar duplicados
            $queryCheck = 'SELECT id FROM users WHERE username = :u OR email = :e';
            $stmtCheck = $conexionDB->prepare($queryCheck);
            $stmtCheck->execute([':u' => $USERNAME, ':e' => $EMAIL]);
            
            if ($stmtCheck->fetch()) {
                $error = 4; // Usuario o email duplicado
            } else {
                // Insertamos el nuevo usuario con ROLE_USER por defecto
                $queryInsert = 'INSERT INTO users (username, password, email, role) VALUES (:u, :p, :e, "ROLE_USER")';
                $hash = password_hash($PASSWORD, PASSWORD_BCRYPT, ['cost' => 12]); // hasheamos la clave
                
                $stmtInsert = $conexionDB->prepare($queryInsert);
                $stmtInsert->execute([
                    ':u' => $USERNAME,
                    ':p' => $hash,
                    ':e' => $EMAIL
                ]);
                
                // Si llegamos aquí, el registro es correcto. Preparamos la alerta para el login.
                $_SESSION['alerta'] = "Registro completado. Ya puedes iniciar sesión.";
                $registro_exitoso = true;
                header("Location: login.php");
            }
        } catch (Exception $exc) {
            $error = 5; // Fallo de conexión o query
        }
    }
}

// Solo mostramos el HTML si no hemos redirigido por éxito
if (!$registro_exitoso):
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
   <div class="login-container">
    <h1>Crea tu cuenta</h1>
    
    <form method="post" action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="USERNAME" placeholder="Ej. juanito123" required autocomplete="off" value="<?= $USERNAME ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Correo electrónico</label>
            <input type="email" name="EMAIL" placeholder="correo@ejemplo.com" required value="<?= $EMAIL ?? '' ?>">
        </div>

        <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="PASSWORD" placeholder="••••••••" required>
        </div>

        <div class="form-group">
            <label>Confirmar contraseña</label>
            <input type="password" name="PASSWORD_CONFIRM" placeholder="••••••••" required>
        </div>
        
        <input type="submit" value="Registrarse">

        <?php if (isset($error) && $error !== 0): ?>
            <div class="error-box">
                <?= htmlspecialchars($mensajes[$error], ENT_QUOTES, 'UTF-8'); ?>
            </div>
        <?php endif; ?>
    </form>
    
    <a href="login.php" class="footer-link">¿Ya tienes cuenta? Entra aquí</a>
</div>
</body>
</html>
<?php endif; ?>