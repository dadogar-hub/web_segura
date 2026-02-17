<?php

require constantes.php;




//recogemos las entradas del formulario si la peticion es post
if($_SERVER['REQUEST_METHOD'] == 'POST'){
    //inicializamos las variables
$USERNAME = htmlspecialchars($_POST['username'], ENT_QUOTES, 'UTF-8');
$PASSSWORD = htmlspecialchars($_POST['password'], ENT_QUOTES, 'UTF-8');
$error=0;   
    
    function validarPass($password) {
        // longitud
        if (strlen($password) < 8 || strlen($password) > 20) {
            return 1;
        }

        // mayuscula
        if (!preg_match('/[A-Z]/', $password)) {
            return 1;
        }

        // minuscula
        if (!preg_match('/[a-z]/', $password)) {
            return 1;
        }

        // numero
        if (!preg_match('/[0-9]/', $password)) {
            return 1;
        }

        // caracter especial
        if (!preg_match('/[\W_]/', $password)) {
            return 1;
        }

        return 0;    
    }
    
    if (!ctype_alnum($username)){
        $error = 2;
        
    }
        
        
    
        if($error === 0){
           $dsn = "mysql:host=HOST;port=PORT;dbname=DB;";
           
           try {
               $conexionDB = new PDO($dsn, USER, PASSW);
               $query = 'SELECT * FROM users WHERE email = :email';
               $values = [':email' => $correo];
               $res = $pdo->prepare($query);
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
            <input type="text" name="correo" id="correo">
            <input type="password" name="password" id="password"> 
            <input type="submit">
            <p>
            <?php 
    if ($error !== 0) {
        $mensajes = [

        ];

        // Mostramos el mensaje o uno genérico si el código no existe
        echo $mensajes[$error] ?? "Ha ocurrido un error inesperado.";
    }
    ?>
            </p>
        </form>
    </body>
</html>
