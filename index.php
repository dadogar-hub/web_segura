<?php
require 'constantes.php';

$error=0; 
$mensajes = [
    1 => 'Usuario o contraseña no válidos.',   // mensaje genérico, no revelar cuál falla
    2 => 'Usuario o contraseña incorrectos.',
    3 => 'Error interno. Inténtalo más tarde.',
];

function validarPass(string $password): bool {
    return strlen($password) >= 8
        && strlen($password) <= 20         
        && preg_match('/[A-Z]/', $password)
        && preg_match('/[a-z]/', $password)
        && preg_match('/[0-9]/', $password)
        && preg_match('/[\W_]/', $password);
}

//recogemos las entradas del formulario si la peticion es post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //inicializamos las variables
$USERNAME = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8'); //Vericamos que el username es alfanumerico
$PASSWORD = $_POST['password']; // No aplicamos htmlspecialchars a la contraseña para no alterar los caracteres especiales. 

        // 3. Validar formato username (solo alfanumérico, 3-30 chars)
        if (!ctype_alnum($username) || strlen($username) < 3 || strlen($username) > 30) {
            $error = 1;
        }
        // 4. Validar complejidad de contraseña
        elseif (!validarPass($password)) {
            $error = 1;
        }
        
        
        if($error === 0){
           $dsn = "mysql:host=HOST;port=PORT;dbname=DB;";
           
           try {
               $conexionDB = new PDO($dsn, USER, PASSW);
               $query = 'SELECT * FROM users WHERE username = :username';
               $values = [':username' => $USERNAME];
               $res = $conexionDB->prepare($query);
               $res->execute($values);
               
               $row = $res->fetch(PDO::FETCH_ASSOC);
              
               if($row !== false){
                   if(password_verify($password, $row["password"])){
                       if (session_status() === PHP_SESSION_NONE) session_start();
                        $_SESSION['user_id'] = $row['id'];
                        $_SESSION['username'] = $row['username'];
                   }else{
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
    </head>
    <body>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
            <input type="text" name="username" id="username">
            <input type="password" name="password" id="password"> 
            <input type="submit">

            <?php if ($error !== 0): ?>
            <p class="error">
                <?php echo htmlspecialchars($mensajes[$error] ?? 'Error inesperado.', ENT_QUOTES, 'UTF-8'); ?>
            </p>
            <?php endif; ?>

        </form>
    </body>
</html>
