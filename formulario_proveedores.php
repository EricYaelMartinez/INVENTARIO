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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../TIENDA_INV/css/formulario_proveedores.css">
    <title>Registro de Proveedores</title>
</head>
<body>
    <?php 
    // Captura el mensaje de la redirección
    $msg = $_GET['msg'] ?? '';
    if ($msg) {
        $color = strpos($msg, 'Error') !== false ? 'red' : 'green';
        echo "<p style='color:{$color}; padding: 10px; border: 1px solid {$color};'>". htmlspecialchars($msg) ."</p>";
    }
    ?>

    <div class="header">
        <div class="header-content">
            <h1>NUEVO PROVEEDOR</h1>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>

    <div class="menu">
            <a href="../TIENDA_INV/dashboard.php">Inicio</a>
            <a href="../TIENDA_INV/formulario_productos.php">Registro de Productos</a>
            <a href="../TIENDA_INV/listado_productos.php">Lista de Productos</a>
            <a href="../TIENDA_INV/formulario_entrada.php">Nueva Entrada</a>
            <a href="../TIENDA_INV/listado_entradas.php">Entradas de Mercancia</a>
            <a href="../TIENDA_INV/formulario_venta.php">Nueva Venta</a>
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
            <a href="../TIENDA_INV/formulario_corte.php">Corte de Caja</a>
    </div>
    <div class="contenido">
       
        <form action="registro_proveedores.php" method="post" class="form">

            <h2 class="form-title">Registro de Proveedores de Mercancia</h2>
            <div class="form-container">

                    <div class="input-box">
                        <span class="details">Nombre de Proveedor</span>
                        <input type="text" placeholder="Ingresa el nombre del proveedor" name="nombrePro" required>
                    </div>
                    
                    <div class="input-box">
                        <span class="details">Contacto</span>
                        <input type="text" placeholder="Ingresa el nombre del preventista" name="contacto" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Numero de Telefono</span>
                        <input type="text" placeholder="Telefono para contactar al preventista" name="tel" required>
                    </div>
                

                <input type="submit" class="form-submit" value="Resgistrar">

            </div>

        </form>
    </div>
</body>
</html>