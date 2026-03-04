<?php

require 'constantes.php'; // importamos las variables de entorno.
require 'fuctions.php';

$error=0;  
$mensajes = [
    1 => 'Usuario o contraseña no válidos.',   // mensaje genérico, no revelar cuál falla
    2 => 'Usuario o contraseña incorrectos.',
    3 => 'Error interno. Inténtalo más tarde.',
];

//recogemos las entradas del formulario si la peticion es post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //inicializamos las variables
$USERNAME = htmlspecialchars($_POST['USERNAME'], ENT_QUOTES, 'UTF-8'); //Vericamos que el USERNAME es alfanumerico
$PASSWORD = $_POST['PASSWORD']; // No aplicamos htmlspecialchars a la contraseña para no alterar los caracteres especiales. 

        // 3. Validar formato USERNAME (solo alfanumérico, 3-30 chars)
        if (!esNombreUsuarioValido($USERNAME)) {
            $error = 1;
        }
        // 4. Validar complejidad de contraseña
        elseif (!validarPass($PASSWORD)) {
            $error = 0;
        }
        
        //Si el no da error una validacion anterior conecto con base de datos
        if($error == 0){
           $dsn = "mysql:host=" . HOST . ";port=" . PORT . ";dbname=" . DB . ";charset=utf8mb4";
           
           try { // Utilizamos un tricach por si acaso fallase controlar el error
               $conexionDB = new PDO($dsn, USER, PASSW); // usando los datos del fichero constantes.php nos conectamos con PDO a la base de datos
               $query = 'SELECT * FROM users WHERE username = :USERNAME'; //Definimos que query queremos que haga sql
               $values = [':USERNAME' => $USERNAME];
               $res = $conexionDB->prepare($query); //la preparamos para escapar caracteres extraños
               $res->execute($values);
               
               $row = $res->fetch(PDO::FETCH_ASSOC); // nos traemos la row del usuario que buscamos
              
               if ($row !== false) { //si no viene vacia, lo que significaria que no existe
    
    if (password_verify($PASSWORD, $row["password"])) { //verificamos con password verifi que la constraseña del formulario coincide con la de la base de datos
        
        if (session_status() === PHP_SESSION_NONE) { //si coincide,y no tenia sesion anteriormente, le abro sesion
            session_start();
        }
         // en la sesion metemos datos como id, username, y role    
        $_SESSION['user_id'] = $row['id'];  
        $_SESSION['username'] = $row['username']; 
        $_SESSION['role'] = $row['role']; 
        
      
        header("Location: home.php"); // redirigimos al home     
    } else {
        $error = 2;
    }
}else{
    $error = 2;
}
                       
           } catch (Exception $exc) {
               $error = 3; // si no funcionase la conexion a la bae de datos saltamos systemerror.
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
