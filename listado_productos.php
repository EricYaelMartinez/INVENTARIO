<?php
// Incluye el archivo de conexión (asumiendo que define $pdo)
include_once ('conexion.php'); 

// 1. Obtener todos los productos y sus categorías
$productos_por_categoria = [];
$sql = "
    SELECT 
        p.ProductoID, 
        p.Nombre AS ProductoNombre, 
        p.Descripcion, 
        p.PrecioVenta, 
        p.Stock, 
        p.Imagen,
        c.Nombre AS CategoriaNombre,
        c.CategoriaID
    FROM 
        Productos p
    JOIN 
        Categorias c ON p.CategoriaID = c.CategoriaID
    ORDER BY 
        c.Nombre ASC, p.Nombre ASC;
";

try {
    $stmt = $pdo->query($sql);
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
    <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        .producto-card { border: 1px solid #ccc; padding: 15px; margin-bottom: 10px; display: flex; align-items: center; }
        .producto-card img { width: 100px; height: 100px; object-fit: cover; margin-right: 20px; border-radius: 5px; }
        .producto-info { flex-grow: 1; }
        h3 { border-bottom: 2px solid #007bff; padding-bottom: 5px; color: #007bff; margin-top: 25px; }
        .stock-bajo { color: red; font-weight: bold; }
    </style>
</head>
<body>

    <h1>Inventario General de Productos</h1>

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
                        <strong><?php echo htmlspecialchars($producto['ProductoNombre']); ?> (ID: <?php echo $producto['ProductoID']; ?>)</strong>
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