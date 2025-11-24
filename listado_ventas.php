<?php
include_once ('conexion.php'); 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: Log_usuario.php"); 
    exit();
}

$ventas = [];
$error_sql = null;

// --- Manejo del Filtro de Fechas ---
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01'); // Por defecto: el día 1 del mes actual
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');        // Por defecto: la fecha de hoy

// 1. Consulta SQL: Obtener ventas con el nombre del usuario
$sql = "
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
        DATE(V.Fecha) BETWEEN ? AND ?
    ORDER BY 
        V.Fecha DESC;
";

try {
    $stmt = $pdo->prepare($sql);
    // 2. Ejecutar la consulta con el rango de fechas
    $stmt->execute([$fecha_inicio, $fecha_fin]); 
    $ventas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error_sql = "Error al obtener las ventas: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="../TIENDA_INV/css/listado_ventas.css">
    <title>Historial y Reporte de Ventas</title>
<!--<style>
        /* Estilos básicos para claridad y usabilidad */
        body { font-family: 'Verdana', sans-serif; padding: 20px; }
        .filter-form { margin-bottom: 25px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #ccc; }
        .filter-form label, .filter-form input, .filter-form button { margin-right: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { padding: 10px; border: 1px solid #ddd; text-align: left; font-size: 0.9em; }
        th { background-color: #007bff; color: white; }
        tr:nth-child(even) { background-color: #f4f4f4; }
        
        /* Área de Totalización Flotante */
        .total-flotante {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 300px;
            padding: 15px;
            background: #fff3cd; /* Color dorado suave para el total */
            border-top: 3px solid orange;
            box-shadow: 0 -4px 10px rgba(0,0,0,0.1);
            border-top-left-radius: 10px;
        }
        .total-flotante h4 { margin-top: 0; }
        .total-flotante #totalSeleccionado { font-size: 1.8em; font-weight: bold; color: #dc3545; }
    </style>-->
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>HISTORIAL DE VENTAS</h1>
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
            <a href="../TIENDA_INV/formulario_proveedores.php">Nuevo Proveedor</a>
            <a href="../TIENDA_INV/formulario_venta.php">Nueva Venta</a>
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
            <a href="../TIENDA_INV/formulario_corte.php">Corte de Caja</a>
</div>

    <h1>Historial de Ventas</h1>

    <form class="filter-form" action="listado_ventas.php" method="GET">
        <h4>Filtrar Ventas</h4>
        <label for="fecha_inicio">Desde:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo htmlspecialchars($fecha_inicio); ?>" required>
        
        <label for="fecha_fin">Hasta:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo htmlspecialchars($fecha_fin); ?>" required>
        
        <button type="submit">Filtrar</button>
        <a href="listado_ventas.php">Mostrar Hoy</a>
    </form>

    <?php if (isset($error_sql)): ?>
        <p style="color:red;">Error de Base de Datos: <?php echo htmlspecialchars($error_sql); ?></p>
    <?php elseif (empty($ventas)): ?>
        <p>No se encontraron ventas en el rango de fechas seleccionado.</p>
    <?php else: ?>

        <h2>Ventas Encontradas (<?php echo count($ventas); ?>)</h2>

        <table>
            <thead>
                <tr>
                    <th>Seleccionar</th>
                    <th>ID Venta</th>
                    <th>Fecha y Hora</th>
                    <th>Vendedor</th>
                    <th>Total Venta</th>
                    <th>Detalles</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($ventas as $venta): ?>
                    <tr class="venta-row">
                        <td>
                            <input type="checkbox" 
                                   class="venta-checkbox" 
                                   data-total="<?php echo $venta['Total']; ?>">
                        </td>
                        <td><?php echo $venta['VentasID']; ?></td>
                        <td><?php echo date('d/m/Y H:i', strtotime($venta['Fecha'])); ?></td>
                        <td><?php echo htmlspecialchars($venta['Vendedor']); ?></td>
                        <td>$<?php echo number_format($venta['Total'], 2); ?></td>
                        <td>
                            <a href="detalle_venta.php?id=<?php echo $venta['VentasID']; ?>">Ver Detalles</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="total-flotante">
            <h4>Total de Venta Seleccionado</h4>
            <p>Ventas seleccionadas: <span id="contadorVentas">0</span></p>
            <div id="totalSeleccionado">$0.00</div>
        </div>

    <?php endif; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const checkboxes = document.querySelectorAll('.venta-checkbox');
            const totalDisplay = document.getElementById('totalSeleccionado');
            const contadorDisplay = document.getElementById('contadorVentas');

            function calcularTotalSeleccionado() {
                let total = 0;
                let contador = 0;
                
                checkboxes.forEach(checkbox => {
                    if (checkbox.checked) {
                        // Obtiene el total de la venta del atributo data-total
                        const totalVenta = parseFloat(checkbox.dataset.total);
                        total += totalVenta;
                        contador++;
                    }
                });

                totalDisplay.textContent = `$${total.toFixed(2)}`;
                contadorDisplay.textContent = contador;
            }

            // Asignar el evento 'change' a todos los checkboxes
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', calcularTotalSeleccionado);
            });
        });
    </script>
</body>
</html>