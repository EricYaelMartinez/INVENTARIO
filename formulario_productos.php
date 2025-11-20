<?php
include_once ('conexion.php'); // Incluye la conexión PDO ($pdo)

$producto_a_editar = null;
$es_edicion = false;

//verifica si hay un ID de producto para editar
if(isset($_GET['id']) && is_numeric($_GET['id'])){
    $pruducto_id = (int)$_GET['id'];
    $es_edicion = true;

    $sql_producto = "SELECT * FROM productos WHERE ProductoID = ?";
    try{
        $stmt_producto = $pdo->prepare($sql_producto);
        $stmt_producto->execute([$pruducto_id]);
        $producto_a_editar = $stmt_producto->fetch();

        if(!$producto_a_editar){
            header("Location: listado_productos.php?error=" . urlencode("Producto a editar no encontrado."));
            exit();
        }
    }catch (PDOException $e) {
        $error_db = "Error al cargar el producto:" . $e->getMessage();
    }
}

// 1. OBTENER LAS CATEGORÍAS para el SELECT
$categorias = [];
try {
    $stmt = $pdo->query("SELECT CategoriaID, Nombre FROM categorias ORDER BY Nombre ASC");
    $categorias = $stmt->fetchAll();
} catch (PDOException $e) {
    $error_categorias = "Error al cargar categorías: " . $e->getMessage();
}

// Mensajes de feedback (si hay parámetros en la URL)
$msg = $_GET['msg'] ?? '';
$error = $_GET['error'] ?? '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de productos</title>
</head>
<body>
    <nav class="nav1">
        <li class="menu1"><a href="dashboard.php"><b>Inicio</b></a></li>
        <li class="menu1"><a href="listado_productos.php"><b>Lista de Productos</b></a></li>
        <li class="menu1"><a href="dashboard.php"><b>Inicia Sesion</b></a></li>
    </nav>

    <nav class="contenido">
        <div class="titulo">Resgistro de Productos y Categorias</div>

        <form action="procesar_registro.php" method="POST">
            <h3>Registro de Categorias</h3>
            <input type="hidden" name="action" value="insert_categoria">
            <div class="input-box">
                <span class="details">Nombre de Categoria</span>
                <input type="text" placeholder="Ingresa el nombre de categoria " name="nombreC" required>
            </div>

            <div class="boton">
                <input type="submit" value="Registrar Categoria">
            </div>
        </form>

        
        
        <form action="procesar_registro.php" method="POST" enctype="multipart/form-data">
            <h3><?php echo $es_edicion ? 'MODIFICAR PRODUCTO' : 'REGISTRAR PRODUCTO'; ?></h3>
            <input type="hidden" name="action" value="<?php echo $es_edicion ? 'update_producto' : 'insert_producto'; ?>">
            <?php if ($es_edicion): ?>
                <input type="hidden" name="ProductoID" value="<?php echo htmlspecialchars($producto_a_editar['ProductoID']); ?>">
            <?php endif; ?>

            
        <div class="detalles-prod">

            <div class="input-box">
                <span class="details">Nombre de Producto</span>
                <input type="text" placeholder="Ingresa el nombre del producto" name="nombreP" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['Nombre']) : ''; ?>" required>
            </div>

            <div class="input-box">
                <span class="details">Codigo de Barras</span>
                <input type="text" placeholder="Escanea el codigo del producto" name="codigoB" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['CodigoBarra']) : ''; ?>" required>
            </div>

            <div class="input-box">
                <span class="details">Descripcion</span>
                <input type="text" placeholder="Tamaño o peso del producto(litros, kilos)" name="desP" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['Descripcion']) : ''; ?>" required>
            </div>

            <div class="input-box">
                <span class="details">Precio de Venta</span>
                <input type="text" placeholder="Precio del Producto" name="precioP" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['PrecioVenta']) : ''; ?>" required>
            </div>

            <div class="input-box">
                <span class="details">Unidades Disponibles</span>
                <input type="text" placeholder="Cantidad de producto disponible" name="stockP" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['Stock']) : ''; ?>" required>
            </div>

            <div class="input-box">
                <span class="details">Unidades minimas Disponibles</span>
                <input type="text" placeholder="Cantidad de producto minimo disponible" name="stockM" 
                value="<?php echo $es_edicion ? htmlspecialchars($producto_a_editar['StockMinimo']) : ''; ?>" required>
            </div>
            

            <div class="img_perfil">
                <label for="imp">Imagen del producto</label>
                <input type="file" name="img_p" id="imp" >
            </div>

            <div class="input-box">
                <span class="details">Categoria del producto</span>
                <select name="CategoriaID" id="" required>
                    <option value="" disabled <?php echo $es_edicion ? '' : 'selected'; ?>>Selecciona una Categoria</option>    
                    <?php if (isset($error_categorias)): ?>
                            <option value="" disabled style="color:red;"><?php echo $error_categorias; ?></option>
                        <?php else: ?>
                            <?php foreach ($categorias as $cat): ?>
                                <option value="<?php echo htmlspecialchars($cat['CategoriaID']); ?>"
                                <?php 
                                    if ($es_edicion && $producto_a_editar['CategoriaID'] == $cat ['CategoriaID']) {
                                        echo 'selected';
                                    } 
                                ?>>
                                    <?php echo htmlspecialchars($cat['Nombre']); ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </select>
            </div>
            

            </div>
            <div class="boton">
                <input type="submit" value="Registar Producto">
            </div>
        </form>

    </nav>
</body>
</html>