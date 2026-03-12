<?php
session_start(); //para poder controlar los errores que no se cuele.

require 'constantes.php'; // importamos las variables de entorno.
require 'fuctions.php';

$error=0;  
$mensajes = [
    1 => 'Usuario o contrasena no validos.',   // mensaje generico, no revelar cual falla
    2 => 'Usuario o contrasena incorrectos.',
    3 => 'Error interno. Intentalo mas tarde.',
];

//recogemos las entradas del formulario si la peticion es post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //inicializamos las variables
$USERNAME = htmlspecialchars($_POST['USERNAME'], ENT_QUOTES, 'UTF-8'); //Vericamos que el USERNAME es alfanumerico
$PASSWORD = $_POST['PASSWORD']; // No aplicamos htmlspecialchars a la contrasena para no alterar los caracteres especiales. 

        // 3. Validar formato USERNAME (solo alfanumerico, 3-30 chars)
        if (!esNombreUsuarioValido($USERNAME)) {
            $error = 1;
        }
        // 4. Validar complejidad de contrasena
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
               $res = $conexionDB->prepare($query); //la preparamos para escapar caracteres extranos
               $res->execute($values);

               $row = $res->fetch(PDO::FETCH_ASSOC); // nos traemos la row del usuario que buscamos

               if ($row !== false) { //si no viene vacia, lo que significaria que no existe
    
                    // --- COMPROBACION Y MIGRACION MD5 A BCRYPT ---
                    if ($row['password'] === md5($PASSWORD)) {
                        $nuevoHash = password_hash($PASSWORD, PASSWORD_DEFAULT);
                        $updateQuery = 'UPDATE users SET password = :NUEVOPASS WHERE id = :ID';
                        $updateRes = $conexionDB->prepare($updateQuery);
                        $updateRes->execute([':NUEVOPASS' => $nuevoHash, ':ID' => $row['id']]);
                        
                        // Actualizamos la variable $row para que password_verify funcione abajo
                        $row['password'] = $nuevoHash;
                    }
                    // ---------------------------------------------

                    if (password_verify($PASSWORD, $row["password"])) { //verificamos con password verify que la contrasena del formulario coincide con la de la base de datos

                        if (session_status() === PHP_SESSION_NONE) { //si coincide,y no tenia sesion anteriormente, le abro sesion
                            session_start();
                        }
                         // en la sesion metemos datos como id, username, y role    
                        $_SESSION['user_id'] = $row['id'];  
                        $_SESSION['username'] = $row['username']; 
                        $_SESSION['role'] = $row['role']; 

                      
                        header("Location: index.php"); // redirigimos al home      
                        exit;
                    } else {
                        $error = 2;
                    }
                }else{
                    $error = 2;
                }

           } catch (Exception $exc) {
               $error = 3; // si no funcionase la conexion a la base de datos saltamos systemerror.
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
            <input type="PASSWORD" name="PASSWORD" id="PASSWORD" required placeholder=" **************** "> 
            <input type="submit">

            <?php if ($error !== 0): ?>
            <p class="error">
                <?php echo htmlspecialchars($mensajes[$error] ?? 'Error inesperado.', ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <?php endif; ?>

            <?php if (isset($_SESSION['alerta'])): ?>
            <div style="background-color: #fee2e2; color: #dc2626; padding: 1rem; border: 1px solid #fecaca; margin-bottom: 1rem; border-radius: 4px; text-align: center;">
                <?= htmlspecialchars($_SESSION['alerta'], ENT_QUOTES, 'UTF-8'); ?>
            </div>
            <?php unset($_SESSION['alerta']); ?>
        <?php endif; ?>

        </form>
    </body>
</html>
