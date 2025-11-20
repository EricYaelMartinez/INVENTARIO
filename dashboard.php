<?php
// 1. Iniciar la sesión
session_start();

// 2. Control de Acceso: Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    
    // Si no hay sesión, redirigir al login
    header("Location: Log_usuario.php"); 
    exit(); // Detener la ejecución del script
}

// Si la sesión existe, el código continúa
$nombre_usuario = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Inventario y Ventas</title>
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        h1 { color: #333; }
        .menu a { margin-right: 15px; text-decoration: none; color: #007bff; }
        .menu a:hover { text-decoration: underline; }
        .logout { display: block; margin-top: 30px; }
    </style>
</head>
<body>
    
    <h1>¡Bienvenido/a, <?php echo htmlspecialchars($nombre_usuario); ?>!</h1>
    <p>Has iniciado sesión correctamente y tienes acceso al sistema.</p>
    
    ---
    
    <h2>Menú Principal</h2>
    <div class="menu">
        <a href="../TIENDA_INV/formulario_productos.php">Gestión de Productos</a>
        <a href="#">Registro de Ventas</a>
        <a href="formulario_entrada.php">Entradas de Mercancía</a>
        <a href="#">Reportes y Corte de Caja</a>
        <a href="../TIENDA_INV/formulario_proveedores.php">Proveedores</a>
    </div>

    ---

    <a href="logout.php" class="logout">Cerrar Sesión</a>

</body>
</html>