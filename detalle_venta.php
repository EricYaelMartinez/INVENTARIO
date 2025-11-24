<?php
include_once ('conexion.php'); 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Log_usuario.php"); 
    exit();
}

// 1. Obtener el ID de la venta de la URL
$ventaID = $_GET['id'] ?? null;

if (!$ventaID || !is_numeric($ventaID)) {
    die("Error: ID de venta no proporcionado o inválido.");
}

$venta_header = null;
$venta_detalles = [];
$error = null;

try {
    // --- 2. CONSULTA DEL ENCABEZADO DE LA VENTA ---
    $sql_header = "
        SELECT 
            V.VentasID, 
            V.Fecha, 
            V.Total,
            V.PagoCliente,
            V.Cambio,
            U.Nombre AS Vendedor
        FROM 
            Ventas V
        JOIN
            usuario U ON V.UsuarioID = U.UsuarioID
        WHERE 
            V.VentasID = ?;
    ";
    $stmt_header = $pdo->prepare($sql_header);
    $stmt_header->execute([$ventaID]); 
    $venta_header = $stmt_header->fetch(PDO::FETCH_ASSOC);

    if (!$venta_header) {
        die("Venta no encontrada.");
    }

    // --- 3. CONSULTA DE LOS DETALLES DE LA VENTA (Productos) ---
    $sql_detalles = "
        SELECT 
            DV.Cantidad, 
            DV.PrecioVendido, 
            DV.Subtotal,
            P.Nombre AS NombreProducto,
            P.Imagen -- Asegúrate de usar el campo de imagen correcto (Imagen o ImagenURL)
        FROM 
            DetalleVenta DV
        JOIN
            Productos P ON DV.ProductoID = P.ProductoID
        WHERE 
            DV.VentasID = ?;
    ";
    $stmt_detalles = $pdo->prepare($sql_detalles);
    $stmt_detalles->execute([$ventaID]); 
    $venta_detalles = $stmt_detalles->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Error al obtener los detalles de la venta: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../TIENDA_INV/css/detalle_venta.css">
    <title>Detalle de Venta #<?php echo htmlspecialchars($ventaID); ?></title>

</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>DETALLES DE VENTA</h1>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>
    
    <nav class="menu">
            <a href="../TIENDA_INV/dashboard.php">Inicio</a>
            <a href="../TIENDA_INV/formulario_productos.php">Registro de Productos</a>
            <a href="../TIENDA_INV/listado_productos.php">Lista de Productos</a>
            <a href="../TIENDA_INV/formulario_entrada.php">Nueva Entrada</a>
            <a href="../TIENDA_INV/listado_entradas.php">Entradas de Mercancia</a>
            <a href="../TIENDA_INV/formulario_proveedores.php">Nuevo Proveedor</a>
            <a href="../TIENDA_INV/formulario_venta.php">Nueva Venta</a>
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
            <a href="../TIENDA_INV/formulario_corte.php">Corte de Caja</a>
        
    </nav>  

    


    <a href="listado_ventas.php">← Volver al Listado de Ventas</a>

    <h1>Detalle de Venta #<?php echo htmlspecialchars($ventaID); ?></h1>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error); ?></p>
    <?php else: ?>

        <h2>Información General</h2>
        <div class="header-info">
            <div><strong>Fecha:</strong> <?php echo date('d/M/Y H:i:s', strtotime($venta_header['Fecha'])); ?></div>
            <div><strong>Vendedor:</strong> <?php echo htmlspecialchars($venta_header['Vendedor']); ?></div>
            <div><strong>Total de Venta:</strong> $<?php echo number_format($venta_header['Total'], 2); ?></div>
            <div><strong>Pago Cliente:</strong> $<?php echo number_format($venta_header['PagoCliente'], 2); ?></div>
            <div><strong>Cambio Devuelto:</strong> $<?php echo number_format($venta_header['Cambio'], 2); ?></div>
        </div>

        <h2>Productos Vendidos</h2>
        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio Unitario</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($venta_detalles as $detalle): ?>
                    <tr>
                        <td>
                            <?php echo htmlspecialchars($detalle['NombreProducto']); ?>
                        </td>
                        <td><?php echo $detalle['Cantidad']; ?></td>
                        <td>$<?php echo number_format($detalle['PrecioVendido'], 2); ?></td>
                        <td>$<?php echo number_format($detalle['Subtotal'], 2); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

    <?php endif; ?>
</body>
</html>