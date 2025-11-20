<?php 
// Asegúrate de que tu archivo conexion.php define la variable $pdo
include_once ('conexion.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 1. Recoger y Sanitizar los datos
    // Aunque PDO maneja la seguridad, es bueno sanitizar la entrada
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $pass_sin_cifrar = $_POST['pass'];
    
    // 2. Cifrar la Contraseña (¡CRUCIAL para la seguridad!)
    $pass_cifrada = password_hash($pass_sin_cifrar, PASSWORD_DEFAULT);

    // 3. Definir la consulta SQL con marcadores de posición (?)
    $sql = "INSERT INTO usuario (Nombre, Correo, Pass) 
            VALUES (?, ?, ?)";

    try {
        // 4. Preparar la sentencia
        $stmt = $pdo->prepare($sql);
        
        // 5. Ejecutar la sentencia, pasando los valores como un array
        // PDO reemplaza (?) con los valores de forma segura (previene Inyección SQL)
        $stmt->execute([$nombre, $correo, $pass_cifrada]);

        // 6. Si la ejecución es exitosa
        header("Location: Log_usuario.php");
        exit(); // Terminar el script después de la redirección

    } catch (PDOException $e) {
        // 7. Manejo de errores de PDO
        // Se puede registrar el error y mostrar un mensaje genérico al usuario
        echo "ERROR AL REGISTRAR. Por favor, inténtelo de nuevo más tarde.";
        // Opcional: Para depuración, puedes mostrar el error real
        // echo "Detalle del error: " . $e->getMessage();
    }
}
?>