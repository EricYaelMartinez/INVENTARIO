<?php
include_once ('conexion.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recoger datos del ENCABEZADO
    $fecha = $_POST['fecha'];
    $proveedorID = $_POST['proveedorID'];
    $totalCosto = $_POST['totalCosto'];
    
    // 2. Recoger arrays de DETALLES (se manejan como arrays en el formulario)
    $productos_ids = $_POST['productoID'];
    $cantidades = $_POST['cantidad'];
    $costos_unitarios = $_POST['costoUnitario'];

    if (empty($productos_ids)) {
        header("Location: formulario_entrada.php?error=" . urlencode("Debe ingresar al menos un producto."));
        exit();
    }
    
    try {
        // --- INICIAR TRANSACCIÓN ---
        // Esto asegura que si una consulta falla, TODAS se revierten.
        $pdo->beginTransaction(); 

        // 3. Insertar ENCABEZADO en EntradasMercancia
        $sql_encabezado = "INSERT INTO entradasmercancia (Fecha, ProveedorID, TotalCosto) 
                           VALUES (?, ?, ?)";
        $stmt_encabezado = $pdo->prepare($sql_encabezado);
        $stmt_encabezado->execute([$fecha, $proveedorID, $totalCosto]);
        
        // Obtener el ID de la entrada recién insertada
        $entradaID = $pdo->lastInsertId();

        // 4. Iterar y procesar cada línea de DETALLE
        foreach ($productos_ids as $key => $productoID) {
            $cantidad = (int)$cantidades[$key];
            $costo_unitario = (float)$costos_unitarios[$key];
            
            if ($cantidad <= 0) continue; // Ignorar si la cantidad es cero o negativa

            // 4a. Insertar DETALLE en DetalleEntrada
            $sql_detalle = "INSERT INTO detalleentrada (EntradaID, ProductoID, Cantidad, CostoUnitario) 
                            VALUES (?, ?, ?, ?)";
            $stmt_detalle = $pdo->prepare($sql_detalle);
            $stmt_detalle->execute([$entradaID, $productoID, $cantidad, $costo_unitario]);

            // 4b. Actualizar STOCK en la tabla Productos
            $sql_stock = "UPDATE productos 
                          SET Stock = Stock + ? 
                          WHERE ProductoID = ?";
            $stmt_stock = $pdo->prepare($sql_stock);
            $stmt_stock->execute([$cantidad, $productoID]);
        }
        
        // --- CONFIRMAR TRANSACCIÓN ---
        $pdo->commit(); 
        
        $mensaje = "Entrada de mercancía #{$entradaID} registrada y stock actualizado con éxito.";

    } catch (PDOException $e) {
        // --- REVERTIR TRANSACCIÓN ---
        $pdo->rollBack(); 
        $mensaje = "ERROR en la transacción. Stock NO fue actualizado. Detalle: " . $e->getMessage();
    }
    
    header("Location: formulario_entrada.php?msg=" . urlencode($mensaje));
    exit();

} else {
    header("Location: formulario_entrada.php");
    exit();
}
?>