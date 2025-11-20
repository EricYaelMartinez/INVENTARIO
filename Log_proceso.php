<?php
// Asegúrate de que tu archivo conexion.php define la variable $pdo
include_once ('conexion.php'); 

// Iniciar sesión para usar variables de sesión (como $_SESSION['usuario_id'])
session_start(); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Obtener los datos del formulario
    $correo_ingresado = trim($_POST['correo']);
    $pass_ingresada = $_POST['pass'];
    
    // 2. Definir la consulta: Buscar el usuario por correo
    // Solo seleccionamos el ID, el nombre, y el hash de la contraseña (Pass)
    $sql = "SELECT UsuarioID, Nombre, Pass FROM usuario WHERE Correo = ?";
    
    try {
        // Preparar la sentencia
        $stmt = $pdo->prepare($sql);
        
        // Ejecutar la sentencia de forma segura
        $stmt->execute([$correo_ingresado]);
        
        // Obtener la fila del usuario
        $usuario = $stmt->fetch();

        // 3. Verificar si el usuario existe
        if ($usuario) {
            
            // 4. Verificar la contraseña cifrada
            // password_verify compara el texto plano (pass_ingresada) con el hash (usuario['Pass'])
            if (password_verify($pass_ingresada, $usuario['Pass'])) {
                
                // --- INICIO DE SESIÓN EXITOSO ---
                
                // 5. Crear variables de sesión para mantener al usuario logueado
                $_SESSION['usuario_id'] = $usuario['UsuarioID'];
                $_SESSION['usuario_nombre'] = $usuario['Nombre'];
                
                // 6. Redirigir al panel de control o página principal del sistema
                header("Location: dashboard.php");
                exit();
                
            } else {
                // Contraseña incorrecta
                $error_mensaje = "contraseña incorrectos.";
            }
        } else {
            // Usuario no encontrado (correo no existe)
            $error_mensaje = "Usuario no encontrado";
        }

    } catch (PDOException $e) {
        // Error en la base de datos
        $error_mensaje = "Ocurrió un error en el servidor. Intente más tarde.";
        // Opcional: registrar $e->getMessage()
    }
    
    // Si llega aquí, hubo un error de autenticación.
    // Podrías redirigir al formulario de login con el mensaje de error:
    header("Location: Log_usuario.php?error=" . urlencode($error_mensaje));
    exit();
}
// Si se accede sin POST, redirigir al formulario de login.
header("Location: index.html");
exit();
?>