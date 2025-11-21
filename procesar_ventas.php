<?php
include_once ('conexion.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recoger datos del POST
    $usuarioID = $_POST['usuarioID'] ?? null;
    $totalVenta = $_POST['totalVenta'] ?? 0.00;
    $pagoCliente = $_POST['pagoCliente'] ?? 0.00;
    $cambio = $_POST['cambio'] ?? 0.00;
    $detallesJSON = $_POST['detallesJSON'] ?? '{}';
    $detalles = json_decode($detallesJSON, true);

    if (empty($detalles) || !is_array($detalles)) {
        // Debería ser validado por JS, pero por seguridad, verificamos
        header("Location: formulario_ventas.php?error=" . urlencode("El carrito está vacío o los datos son inválidos."));
        exit();
    }
    
    try {
        // --- INICIAR TRANSACCIÓN CRÍTICA ---
        $pdo->beginTransaction(); 

        // 2. Insertar ENCABEZADO de la Venta
        $sql_venta = "INSERT INTO ventas (Fecha, Total, PagoCliente, Cambio, UsuarioID) 
                      VALUES (NOW(), ?, ?, ?, ?)";
        $stmt_venta = $pdo->prepare($sql_venta);
        $stmt_venta->execute([$totalVenta, $pagoCliente, $cambio, $usuarioID]);
        
        $ventasID = $pdo->lastInsertId();

        // 3. Iterar e insertar DETALLES y actualizar STOCK
        foreach ($detalles as $productoDetalle) {
            
            $productoID = $productoDetalle['id'];
            $cantidad = (int)$productoDetalle['cantidad'];
            $precioVendido = (float)$productoDetalle['precio'];
            
            // 3a. Calcular subtotal de la línea
            $subtotalLinea = $cantidad * $precioVendido;

            // 3b. Insertar DETALLE en DetalleVenta
            $sql_detalle = "INSERT INTO detalleventa (VentasID, ProductoID, Cantidad, PrecioVendido, Subtotal) 
                            VALUES (?, ?, ?, ?, ?)";
            $stmt_detalle = $pdo->prepare($sql_detalle);
            $stmt_detalle->execute([$ventasID, $productoID, $cantidad, $precioVendido, $subtotalLinea]);

            // 3c. Descontar STOCK en la tabla Productos (la verificación de stock es en JS)
            $sql_stock = "UPDATE productos 
                          SET Stock = Stock - ? 
                          WHERE ProductoID = ?";
            $stmt_stock = $pdo->prepare($sql_stock);
            $stmt_stock->execute([$cantidad, $productoID]);
        }
        
        // --- CONFIRMAR TRANSACCIÓN ---
        $pdo->commit(); 
        
        $mensaje = "Venta #{$ventasID} registrada y stock actualizado con éxito.";

    } catch (PDOException $e) {
        // --- REVERTIR TRANSACCIÓN ---
        $pdo->rollBack(); 
        $mensaje = "ERROR en la venta. Stock NO actualizado. Detalle: " . $e->getMessage();
    }
    
    header("Location: formulario_venta.php?msg=" . urlencode($mensaje));
    exit();

} else {
    header("Location: formulario_venta.php");
    exit();
}
?>