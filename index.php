<?php

// Desactivar la visualización de errores al usuario
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);

// Registrar errores en un archivo privado para tu revisión
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/var/www/html/php-error.log');

require 'constantes.php';

$error=0;  
$mensajes = [
    1 => 'Usuario o contraseña no válidos.',   // mensaje genérico, no revelar cuál falla
    2 => 'Usuario o contraseña incorrectos.',
    3 => 'Error interno. Inténtalo más tarde.',
];

function validarPass(string $PASSWORD): bool {
    return strlen($PASSWORD) >= 8
        && strlen($PASSWORD) <= 20         
        && preg_match('/[A-Z]/', $PASSWORD)
        && preg_match('/[a-z]/', $PASSWORD)
        && preg_match('/[0-9]/', $PASSWORD)
        && preg_match('/[\W_]/', $PASSWORD);
}

//recogemos las entradas del formulario si la peticion es post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //inicializamos las variables
$USERNAME = htmlspecialchars($_POST['USERNAME'], ENT_QUOTES, 'UTF-8'); //Vericamos que el USERNAME es alfanumerico
$PASSWORD = $_POST['PASSWORD']; // No aplicamos htmlspecialchars a la contraseña para no alterar los caracteres especiales. 

        // 3. Validar formato USERNAME (solo alfanumérico, 3-30 chars)
        if (!ctype_alnum($USERNAME) || strlen($USERNAME) < 3 || strlen($USERNAME) > 30) {
            $error = 1;
        }
        // 4. Validar complejidad de contraseña
        elseif (!validarPass($PASSWORD)) {
            $error = 0;
        }
        
        
        if($error == 0){
           $dsn = "mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DB . ";charset=utf8mb4";
           
           try {
               $conexionDB = new PDO($dsn, USER, PASSW);
               $query = 'SELECT * FROM users WHERE username = :USERNAME';
               $values = [':USERNAME' => $USERNAME];
               $res = $conexionDB->prepare($query);
               $res->execute($values);
               
               $row = $res->fetch(PDO::FETCH_ASSOC);
              
               if ($row !== false) {
    
    if (password_verify($PASSWORD, $row["password"])) {
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
             
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['username'] = $row['username']; 
        $_SESSION['role'] = $row['role']; 
        
      
        header("Location: home.php");
        exit(); 
        
    } else {
        $error = 2;
    }
}
                       
           } catch (Exception $exc) {
               $error = 3;
           }                             
}}

?>



<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title></title>
        <link rel="stylesheet" href="style.css">
    </head>
    <body>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="USERNAME" id="USERNAME" required autocomplete="off" placeholder="Tu usuario">
            <input type="PASSWORD" name="PASSWORD" id="PASSWORD" required placeholder="••••••••"> 
            <input type="submit">

            <?php if ($error !== 0): ?>
            <p class="error">
                <?php echo htmlspecialchars($mensajes[$error] ?? 'Error inesperado.', ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <?php endif; ?>

        </form>
    </body>
</html>
