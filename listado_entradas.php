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
<?php
include_once ('conexion.php'); 

$entradas = [];
$error_sql = null;

// Consulta SQL para obtener todas las entradas y sus detalles
$sql = "
    SELECT 
        E.EntradaID, 
        DATE(E.Fecha) AS FechaEntrada, 
        E.TotalCosto, 
        P.NombreProveedor,
        D.ProductoID,
        PD.Nombre AS ProductoNombre,
        D.Cantidad,
        D.CostoUnitario
    FROM 
        entradasmercancia E
    JOIN 
        proveedores P ON E.ProveedorID = P.ProveedorID
    JOIN
        detalleentrada D ON E.EntradaID = D.EntradaID
    JOIN
        productos PD ON D.ProductoID = PD.ProductoID
    ORDER BY 
        E.EntradaID DESC, D.ProductoID ASC;
";

try {
    $stmt = $pdo->query($sql);
    $resultados = $stmt->fetchAll();
    
    // Agrupar los detalles bajo su respectiva entrada
    foreach ($resultados as $row) {
        $id = $row['EntradaID'];
        if (!isset($entradas[$id])) {
            $entradas[$id] = [
                'ID' => $id,
                'Fecha' => $row['FechaEntrada'],
                'Proveedor' => $row['NombreProveedor'],
                'Total' => $row['TotalCosto'],
                'Detalles' => []
            ];
        }
        
        // Agregar el detalle del producto a la entrada
        $entradas[$id]['Detalles'][] = [
            'ID' => $row['ProductoID'],
            'Nombre' => $row['ProductoNombre'],
            'Cantidad' => $row['Cantidad'],
            'CostoUnitario' => $row['CostoUnitario'],
            // Clave única para el checkbox: EntradaID-ProductoID
            'ClaveUnica' => $row['EntradaID'] . '-' . $row['ProductoID'] 
        ];
    }
} catch (PDOException $e) {
    $error_sql = "Error al obtener datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Historial de Entradas de Mercancía</title>
    <link rel="stylesheet" href="../TIENDA_INV/css/listado_entradas.css">
</head>
<body>

    <div class="header">
        <div class="header-content">
            <h1>HISTORIAL DE ENTRADAS</h1>
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
            <a href="../TIENDA_INV/formulario_proveedores.php">Nuevo Proveedor</a>
            <a href="../TIENDA_INV/formulario_venta.php">Nueva Venta</a>
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
            <a href="../TIENDA_INV/formulario_corte.php">Corte de Caja</a>
    </div>

    <p><a href="formulario_entrada.php">Registrar Nueva Entrada</a></p>

    <?php if ($error_sql): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error_sql); ?></p>
    <?php elseif (empty($entradas)): ?>
        <p>No hay entradas de mercancía registradas.</p>
    <?php else: ?>
        
        <div class="subtotal-calc">
            <h4>Cálculo de Subtotal</h4>
            <p>Productos seleccionados: <span id="contadorDetalles">0</span></p>
            <p>Subtotal (Costo): **$ <span id="subtotalCosto">0.00</span>**</p>
        </div>

        <?php foreach ($entradas as $entrada): ?>
            <div class="entrada-box">
                <div class="entrada-header">
                    Entrada #<?php echo $entrada['ID']; ?> | Fecha: <?php echo $entrada['Fecha']; ?> | Proveedor: <?php echo htmlspecialchars($entrada['Proveedor']); ?> | Costo Total: $<?php echo number_format($entrada['Total'], 2); ?>
                </div>

                <h4>Detalles de la Entrada:</h4>
                <?php foreach ($entrada['Detalles'] as $detalle): ?>
                    <div class="detalle-row">
                        <input type="checkbox" 
                                class="detalle-checkbox"
                                data-costo-unitario="<?php echo $detalle['CostoUnitario']; ?>"
                                data-cantidad="<?php echo $detalle['Cantidad']; ?>"
                                id="chk-<?php echo $detalle['ClaveUnica']; ?>">
                        
                        <label for="chk-<?php echo $detalle['ClaveUnica']; ?>">
                            **<?php echo htmlspecialchars($detalle['Nombre']); ?>** (ID: <?php echo $detalle['ID']; ?>) | 
                            Cant: <?php echo $detalle['Cantidad']; ?> | 
                            Costo Unitario: $<?php echo number_format($detalle['CostoUnitario'], 2); ?> | 
                            Subtotal Línea: $<?php echo number_format($detalle['Cantidad'] * $detalle['CostoUnitario'], 2); ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>

    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.detalle-checkbox');
            const subtotalDisplay = document.getElementById('subtotalCosto');
            const contadorDisplay = document.getElementById('contadorDetalles');

            function calcularSubtotal() {
                let subtotal = 0;
                let contador = 0;
                
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        const costoUnitario = parseFloat(checkbox.dataset.costoUnitario);
                        const cantidad = parseInt(checkbox.dataset.cantidad);
                        subtotal += (costoUnitario * cantidad);
                        contador++;
                    }
                });

                subtotalDisplay.textContent = subtotal.toFixed(2);
                contadorDisplay.textContent = contador;
            }

            // Asignar el evento a todos los checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calcularSubtotal);
            });
        });
    </script>
</body>
</html>