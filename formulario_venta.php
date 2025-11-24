<?php
include_once ('conexion.php'); 
session_start();

// Control de Sesión (asegura que el usuario esté logueado)
if (!isset($_SESSION['usuario_id'])) {
    header("Location: dashboard.php"); 
    exit();
}

// Variables para el Formulario
$usuario_id = $_SESSION['usuario_id'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Punto de Venta </title>
    <link rel="stylesheet" href="../TIENDA_INV/css/formulario_venta.css">
    </head>
<body>

    <div class="header">
        <div class="header-content">
            <h1>PUNTO DE VENTAS</h1>
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
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
            <a href="../TIENDA_INV/formulario_corte.php">Corte de Caja</a>
            <a href="../TIENDA_INV/listado_ventas.php">Lista de Ventas</a>
    </div>
    <h1>Realizar Venta</h1>
    <p class="emp">Vendedor: <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></p>

    <div id="tpv-container" style="display:flex;">
        
        <div id="scanner-area" style="width: 30%;">
            <h3>Escanear Producto</h3>
            <form id="formBusqueda" onsubmit="buscarProducto(event)">
                <input type="text" id="codigoBarra" name="codigoBarra" placeholder="Escanear Código o teclear Nombre" autofocus required>
                <button type="submit" style="display:none;">Agregar</button>
                <div id="resultadoBusqueda"></div>
            </form>
            <div id="previewProducto"></div>
        </div>

        <div id="carrito-area" style="width: 70%; padding-left: 20px;">
            <h2>Carrito de Compras</h2>
            <table border="1" id="tablaCarrito" style="width: 100%;">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Imagen</th>
                        <th>Precio Unitario</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    </tbody>
            </table>
            
            <hr>
            
            <div id="resumenPago">
                <p>Subtotal Venta: **$<span id="subtotalVenta">0.00</span>**</p>
                <h3>TOTAL: **$<span id="totalPagar">0.00</span>**</h3>
                
                <form action="procesar_ventas.php" method="POST" onsubmit="return validarVenta()">
                    <input type="hidden" name="usuarioID" value="<?php echo $usuario_id; ?>">
                    <input type="hidden" name="totalVenta" id="inputTotalVenta">
                    <input type="hidden" name="detallesJSON" id="inputDetallesJSON"> <label for="pagoCliente">Pago del Cliente:</label>
                    <input type="number" id="pagoCliente" name="pagoCliente" step="0.01" min="0" required oninput="calcularCambio()">
                    
                    <p>Cambio a Devolver: **$<span id="cambioDevolver">0.00</span>**</p>
                    <input type="hidden" name="cambio" id="inputCambio">
                    
                    <button type="submit">Finalizar Venta</button>
                </form>
            </div>
        </div>
    </div>
    
    <script src="../TIENDA_INV/js/ventas_script.js"></script> 
</body>
</html>