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

// 1. Obtener Proveedores (para el SELECT)
$proveedores = [];
try {
    $stmt = $pdo->query("SELECT ProveedorID, NombreProveedor FROM Proveedores ORDER BY NombreProveedor ASC");
    $proveedores = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_proveedores = "Error al cargar proveedores: " . $e->getMessage();
}

// 2. Obtener Productos (para el SELECT de cada línea de detalle)
$productos = [];
try {
    $stmt = $pdo->query("SELECT ProductoID, Nombre FROM Productos ORDER BY Nombre ASC");
    $productos = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_productos = "Error al cargar productos: " . $e->getMessage();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Entrada de Mercancía</title>
    <link rel="stylesheet" href="../TIENDA_INV/css/formulario_entrada.css">
</head>
<body>

<div class="header">
        <div class="header-content">
            <h1>ENTRADAS DE MERCANCIA</h1>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>

     <nav class="nav1">
        <li class="menu1"><a href="dashboard.php"><b>Inicio</b></a></li>
        <li class="menu1"><a href="listado_productos.php"><b>Lista de Productos</b></a></li>
        <li class="menu1"><a href="listado_entradas.php"><b>Detalles de Entradas</b></a></li>
        <li class="menu1"><a href="formulario_proveedores.php"><b>Registro de Proveedores</b></a></li>

    </nav>

    <h1>Registro de Nueva Entrada de Mercancía</h1>
    
    <form action="procesar_entrada.php" method="POST" id="formEntrada">

        <fieldset>
            <legend>Datos Generales de la Entrada</legend>
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo date('Y-m-d'); ?>" required><br><br>

            <label for="proveedorID">Proveedor:</label>
            <select id="proveedorID" name="proveedorID" required>
                <option value="" disabled selected>Selecciona un proveedor</option>
                <?php if (isset($error_proveedores)): ?>
                    <option value="" disabled><?php echo $error_proveedores; ?></option>
                <?php else: ?>
                    <?php foreach ($proveedores as $prov): ?>
                        <option value="<?php echo htmlspecialchars($prov['ProveedorID']); ?>">
                            <?php echo htmlspecialchars($prov['NombreProveedor']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </fieldset>
        
        <hr>

        <h2>Detalle de Productos Recibidos</h2>
        <table border="1" id="tablaDetalles">
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Cantidad Recibida</th>
                    <th>Monto Total p/Producto</th>
                    <th>Acción</th>
                </tr>
            </thead>
            <tbody>
                </tbody>
        </table>
        
        <button type="button" onclick="agregarFila()">+ Agregar Producto</button>
        <hr>

        <p>Total Calculado (Costo): <span id="totalCostoDisplay">0.00</span></p>
        <input type="hidden" name="totalCosto" id="totalCostoInput" value="0.00">
        
        <input type="submit" value="Registrar Entrada y Actualizar Stock">
    </form>

    <script>
        // Función para generar las opciones de producto
        function generarOpcionesProductos() {
            let opciones = '<option value="" disabled selected>Selecciona Producto</option>';
            // Usamos PHP para imprimir el array de productos como JSON
            const productos = <?php echo json_encode($productos); ?>;
            productos.forEach(p => {
                opciones += `<option value="${p.ProductoID}">${p.Nombre}</option>`;
            });
            return opciones;
        }

        function calcularTotal() {
            let total = 0;
            let totalGeneralEntrada = 0;
            const filas = document.querySelectorAll('#tablaDetalles tbody tr');
            filas.forEach(fila => {
                const cantidad = parseFloat(fila.querySelector('[name^="cantidad"]').value) || 0;
                //const costo = parseFloat(fila.querySelector('[name^="costoUnitario"]').value) || 0;
                //total += (cantidad * costo);
                const montoTotalProducto = parseFloat(fila.querySelector('[name^="montoTotal"]').value) || 0;
                console.log("Monto leído de la fila:", montoTotalProducto); // <<-- AÑADE ESTA LÍNEA
                totalGeneralEntrada += montoTotalProducto;
                const costoUnitario = (cantidad > 0) ? (montoTotalProducto / cantidad) : 0;
            });
            document.getElementById('totalCostoDisplay').textContent = totalGeneralEntrada.toFixed(2);
            document.getElementById('totalCostoInput').value = totalGeneralEntrada.toFixed(2);
        }

        let contadorFila = 0;

        function agregarFila() {
            const tabla = document.querySelector('#tablaDetalles tbody');
            const newRow = tabla.insertRow();
            
            newRow.innerHTML = `
                <td>
                    <select name="productoID[${contadorFila}]" required>
                        ${generarOpcionesProductos()}
                    </select>
                </td>
                <td><input type="number" name="cantidad[${contadorFila}]" min="1" value="1" required onchange="calcularTotal()"></td>
                <td><input type="number" name="montoTotal[${contadorFila}]" step="0.01" min="0" value="0.00" required onchange="calcularTotal()"></td>
                <td><button type="button" onclick="eliminarFila(this)">Eliminar</button></td>
            `;
            contadorFila++;
            calcularTotal(); // Recalcula el total al agregar una fila
        }

        function eliminarFila(btn) {
            const fila = btn.parentNode.parentNode;
            fila.parentNode.removeChild(fila);
            calcularTotal(); // Recalcula el total al eliminar una fila
        }
        
        // Agrega una fila por defecto al cargar la página
        document.addEventListener('DOMContentLoaded', agregarFila);
    </script>
</body>
</html>