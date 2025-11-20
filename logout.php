<?php
// 1. Iniciar la sesión
session_start();

// 2. Destruir todas las variables de sesión
$_SESSION = array();

// 3. Destruir la sesión completamente
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

session_destroy();

// 4. Redirigir al usuario a la página de inicio de sesión
header("Location: Log_usuario.php");
exit();
?>