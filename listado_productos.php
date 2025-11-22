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
// Incluye el archivo de conexión (asumiendo que define $pdo)
include_once ('conexion.php'); 

// 1. Obtener todos los productos y sus categorías
$productos_por_categoria = [];
$termino_busqueda = $_GET['busqueda'] ?? '';
$where_clause = '';
$execute_params = [];

if (!empty($termino_busqueda)){
    $where_clause = "WHERE p.Nombre LIKE ? OR p.CodigoBarra LIKE ?";
    $execute_params = ["%$termino_busqueda%", "%$termino_busqueda%"];
}

$sql = "
    SELECT 
        p.ProductoID, 
        p.Nombre AS ProductoNombre, 
        p.Descripcion, 
        p.PrecioVenta, 
        p.Stock, 
        p.Imagen,
        p.CodigoBarra,
        c.Nombre AS CategoriaNombre,
        c.CategoriaID
    FROM 
        Productos p
    JOIN 
        Categorias c ON p.CategoriaID = c.CategoriaID
    {$where_clause}
    ORDER BY 
        c.Nombre ASC, p.Nombre ASC;
";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($execute_params);
    $productos = $stmt->fetchAll();
    
    // 2. Agrupar los productos por nombre de categoría para visualización
    foreach ($productos as $producto) {
        $nombre_cat = $producto['CategoriaNombre'];
        // Si la categoría no existe en el array, la creamos
        if (!isset($productos_por_categoria[$nombre_cat])) {
            $productos_por_categoria[$nombre_cat] = [];
        }
        // Agregamos el producto a su respectiva categoría
        $productos_por_categoria[$nombre_cat][] = $producto;
    }

} catch (PDOException $e) {
    $error_sql = "Error al obtener los datos: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Inventario de Productos</title>
    <link rel="stylesheet" href="../TIENDA_INV/css/listado_productos.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>INVENTARIO GENERAL DE PRODUCTOS</h1>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>

    <div class="menu">
            <a href="../TIENDA_INV/formulario_productos.php">Gestión de Productos</a>
            <a href="formulario_entrada.php">Entradas de Mercancía</a>
            <a href="../TIENDA_INV/formulario_corte.php">Reportes y Corte de Caja</a>
            <a href="../TIENDA_INV/formulario_proveedores.php">Proveedores</a>
        </div>

    <nav>
        <p><a href="formulario_productos.php">VOLVER AL REGISTRO DE PRODUCTOS</a></p>
    </nav>

    <form action="listado_productos.php" method="get" style="margin-bottom: 20px;">
        <input type="text" name="busqueda" 
           placeholder="Buscar por Nombre o Código de Barras" 
           value="<?php echo htmlspecialchars($termino_busqueda); ?>" 
           style="padding: 8px; width: 300px;">
    
    <button type="submit" style="padding: 8px 15px;">Buscar</button>
    
    <?php if (!empty($termino_busqueda)): ?>
        <a href="listado_productos.php" style="margin-left: 10px; text-decoration: none;">Limpiar Búsqueda</a>
        <p style="margin-top: 10px; font-weight: bold;">
            Resultados para: "<?php echo htmlspecialchars($termino_busqueda); ?>"
        </p>
    <?php endif; ?>
    </form>
<hr>
    <?php if (isset($error_sql)): ?>
        <p style="color:red;"><?php echo $error_sql; ?></p>
    <?php elseif (empty($productos)): ?>
        <p>No hay productos registrados en el inventario.</p>
    <?php else: ?>

        <?php foreach ($productos_por_categoria as $categoria_nombre => $lista_productos): ?>
            
            <h3><?php echo htmlspecialchars($categoria_nombre); ?> (<?php echo count($lista_productos); ?> productos)</h3>
            
            <?php foreach ($lista_productos as $producto): ?>
                <div class="producto-card">
                    <?php if (!empty($producto['Imagen']) && file_exists($producto['Imagen'])): ?>
                        <img src="<?php echo htmlspecialchars($producto['Imagen']); ?>" alt="<?php echo htmlspecialchars($producto['ProductoNombre']); ?>">
                    <?php else: ?>
                        <img src="placeholder.jpg" alt="No Image"> <?php endif; ?>

                    <div class="producto-info">
                        <strong><?php echo htmlspecialchars($producto['ProductoNombre']); ?></strong>
                        <p>Descripción: <?php echo htmlspecialchars($producto['Descripcion']); ?></p>
                        <p>Precio: $<?php echo number_format($producto['PrecioVenta'], 2); ?></p>
                        
                        <p>Stock Disponible: 
                            <span class="<?php echo ($producto['Stock'] <= 5) ? 'stock-bajo' : ''; ?>">
                                <?php echo htmlspecialchars($producto['Stock']); ?> unidades
                            </span>
                        </p>
                        
                        <a href="formulario_productos.php?id=<?php echo $producto['ProductoID']; ?>">Modificar</a> |
                        <form action="../TIENDA_INV/procesar_registro.php" method="post">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="ProductoID" value="<?php echo $producto['ProductoID'];?>">
                            <button type="submit" style="color:red; background:none; border:none; cursor:pointer; padding:0;">Eliminar</button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>

        <?php endforeach; ?>

    <?php endif; ?>
    
    <hr>
    <p><a href="formulario_productos.php">Volver al Registro de Productos</a></p>

</body>
</html>