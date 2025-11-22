<?php
include_once ('conexion.php'); 
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php"); 
    exit();
}

$usuario_id = $_SESSION['usuario_id'];
$ventas_del_dia = [];
//$fecha_corte = date('Y-m-d');
//$total_ventas_calculado = 0.00;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['fecha_seleccionada'])) {
    $fecha_corte = $_POST['fecha_seleccionada'];
} else {
    // Si no se ha seleccionado nada, usa la fecha de hoy
    $fecha_corte = date('Y-m-d'); 
}

$total_ventas_calculado = 0.00;

// 1. Consulta para obtener el total de ventas del día actual
$sql_ventas = "
    SELECT 
        SUM(Total) AS TotalVentas, 
        COUNT(VentasID) AS NumeroVentas
    FROM 
        ventas
    WHERE 
        DATE(Fecha) = ?
";

try {
    $stmt = $pdo->prepare($sql_ventas);
    // Usamos la fecha actual para el filtro
    $stmt->execute([$fecha_corte]); 
    $resumen_ventas = $stmt->fetch();
    //var_dump($resumen_ventas); 
//die(); // Detiene el script
    
    $total_ventas_calculado = $resumen_ventas['TotalVentas'] ?? 0.00;
    $num_ventas = $resumen_ventas['NumeroVentas'] ?? 0;

} catch (PDOException $e) {
    $error_sql = "Error al obtener resumen de ventas: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Corte de Caja Diario</title>
    <link rel="stylesheet" href="../TIENDA_INV/css/formulario_corte.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>CORTE DE CAJA DIARIO</h1>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>

    <div class="menu">
        <a href="../TIENDA_INV/dashboard.php">Inicio</a>
            <a href="../TIENDA_INV/formulario_productos.php">Gestión de Productos</a>
            <a href="formulario_entrada.php">Entradas de Mercancía</a>
            <a href="../TIENDA_INV/listado_productos.php">Ventas</a>
            <a href="../TIENDA_INV/formulario_proveedores.php">Proveedores</a>
        </div>

    <form method="POST" action="formulario_corte.php" style="margin-bottom: 20px;">
        <label for="fecha_seleccionada">Seleccionar Fecha del Corte:</label>
        <input type="date" id="fecha_seleccionada" name="fecha_seleccionada" 
               value="<?php echo htmlspecialchars($fecha_corte); ?>" required>
        <button type="submit">Cargar Ventas</button>
    </form>
    
    <h2>Cifras para el día: <?php echo htmlspecialchars($fecha_corte); ?></h2>
    <p>Usuario responsable: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>

    <?php if (isset($error_sql)): ?>
        <p style="color:red;"><?php echo htmlspecialchars($error_sql); ?></p>
    <?php endif; ?>

    <fieldset>
        <legend>Cifras del Sistema (Ventas Registradas)</legend>
        <p>Número de Ventas Hoy: **<?php echo $num_ventas; ?>**</p>
        <p>Total de Ventas Calculado (Sistema): **$<?php echo number_format($total_ventas_calculado, 2); ?>**</p>
    </fieldset>

    <hr>
    
    <h2>Realizar Cierre</h2>
    <form action="procesar_corte.php" method="POST">
        
        <input type="hidden" name="usuarioID" value="<?php echo $usuario_id; ?>">
        <input type="hidden" name="fechaCorte" value="<?php echo $fecha_corte; ?>">
        <input type="hidden" name="totalVentasCalculado" value="<?php echo $total_ventas_calculado; ?>">
        
        <label for="montoInicial">Monto Inicial de Caja (Fondo Fijo):</label>
        <input type="number" name="montoInicial" id="montoInicial" step="0.01" min="0" value="50.00" required oninput="calcularEsperado()"><br><br>

        <input type="hidden" name="totalVentasEfectivo" id="totalVentasEfectivo" value="<?php echo $total_ventas_calculado; ?>">
        
        <p>Total Esperado en Caja (Inicial + Ventas): **$<span id="totalEsperadoDisplay">0.00</span>**</p>
        <input type="hidden" name="totalCajaEsperado" id="totalCajaEsperado">
        
        <label for="totalCajaFisico">Monto Contado en Caja (Efectivo Físico):</label>
        <input type="number" name="totalCajaFisico" id="totalCajaFisico" step="0.01" min="0" required oninput="calcularDiferencia()"><br><br>

        <p>Diferencia (Sobrante/Faltante): **$<span id="diferenciaDisplay">0.00</span>**</p>
        <input type="hidden" name="diferencia" id="inputDiferencia">

        <button type="submit">Registrar Corte de Caja</button>
    </form>
    
    <script>
        const totalVentas = parseFloat("<?php echo $total_ventas_calculado; ?>");
        
        function calcularEsperado() {
            const inicial = parseFloat(document.getElementById('montoInicial').value) || 0;
            const esperado = inicial + totalVentas;
            
            document.getElementById('totalEsperadoDisplay').textContent = esperado.toFixed(2);
            document.getElementById('totalCajaEsperado').value = esperado.toFixed(2);
            calcularDiferencia(); // Recalcular diferencia
        }

        function calcularDiferencia() {
            const esperado = parseFloat(document.getElementById('totalCajaEsperado').value) || 0;
            const fisico = parseFloat(document.getElementById('totalCajaFisico').value) || 0;
            
            const diferencia = fisico - esperado;
            
            document.getElementById('diferenciaDisplay').textContent = diferencia.toFixed(2);
            document.getElementById('inputDiferencia').value = diferencia.toFixed(2);

            display.style.color = (diferencia < 0) ? '#dc3545' : ((diferencia > 0) ? '#007bff' : 'var(--primary-dark)');
        }

        document.addEventListener('DOMContentLoaded', calcularEsperado); // Inicia el cálculo al cargar
    </script>
</body>
</html>