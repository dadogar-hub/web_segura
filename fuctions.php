<?php

function validarPass(string $PASSWORD): bool {
    return strlen($PASSWORD) >= 8
        && strlen($PASSWORD) <= 20         
        && preg_match('/[A-Z]/', $PASSWORD) //Validamos que cumpla con un estadar complejo
        && preg_match('/[a-z]/', $PASSWORD)
        && preg_match('/[0-9]/', $PASSWORD)
        && preg_match('/[\W_]/', $PASSWORD);
}

function esNombreUsuarioValido($username) {
    // 1. Solo alfanuméricos
    if (!ctype_alnum($username)) {
        return false;
    }

    // 2. Longitud entre 3 y 30 caracteres
    $longitud = strlen($username);
    if ($longitud < 3 || $longitud > 30) {
        return false;
    }

    return true;
}

?>