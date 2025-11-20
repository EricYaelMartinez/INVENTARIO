<?php
include_once ('conexion.php'); // Asegúrate de que $pdo está disponible

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $action = $_POST['action'] ?? '';
    
    // --- MANEJO DE CATEGORÍA ---
    if ($action === 'insert_categoria') {
        $nombre = trim($_POST['nombreC']);
        
        $sql = "INSERT INTO categorias (Nombre) VALUES (?)";
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$nombre]);
            $mensaje = "Categoría '{$nombre}' registrada con éxito.";
        } catch (PDOException $e) {
            $mensaje = "Error: " . $e->getMessage();
        }
        
        header("Location: formulario_productos.php?msg=" . urlencode($mensaje));
        exit();
    
    } 
    
    // --- MANEJO DE PRODUCTO ---
    else if ($action === 'insert_producto') {
        //$categoria_id = (int)$_POST['CategoriaID'];
        //echo "Valor de CategoriaID que se intenta insertar: [". $categoria_id . "]";
        //die();
        
        // 1. Recoger y validar datos
        $nombre = $_POST['nombreP'];
        // Asumiendo que usarás un campo para el código de barras (no lo definiste antes)
        $codigo_barra = $_POST['codigoB'] ?? null; 
        $descripcion = $_POST['desP'];
        $precio_venta = (float)$_POST['precioP'];
        $stock = (int)$_POST['stockP'];
        $stock_minimo = (int)$_POST['stockM'];
        $categoria_id = (int)$_POST['CategoriaID'];
        $imagen_url = null;
        
        // 2. Manejo de la Imagen
        if (isset($_FILES['img_p']) && $_FILES['img_p']['error'] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            $file_tmp_name = $_FILES['img_p']['tmp_name'];
            $file_ext = pathinfo($_FILES['img_p']['name'], PATHINFO_EXTENSION);
            $file_name = uniqid() . '.' . $file_ext;
            $upload_path = $upload_dir . $file_name;

            if (move_uploaded_file($file_tmp_name, $upload_path)) {
                $imagen_url = $upload_path;
            }
        }
        
        // 3. Sentencia SQL de Inserción (Usando sentencias preparadas)
        $sql = "INSERT INTO productos (Nombre, CodigoBarra, Descripcion, PrecioVenta, Stock, StockMinimo, Imagen, CategoriaID) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        try {
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $nombre, 
                $codigo_barra, 
                $descripcion, 
                $precio_venta, 
                $stock, 
                $stock_minimo, 
                $imagen_url, 
                $categoria_id
            ]);
            
            $mensaje = "Producto '{$nombre}' registrado con éxito.";
        } catch (PDOException $e) {
            $mensaje = "Error al registrar el producto: " . $e->getMessage();
        }
        
        header("Location: formulario_productos.php?msg=" . urlencode($mensaje));
        exit();
    }
    // --- LÓGICA PARA ELIMINAR PRODUCTO ---
else if ($action === 'delete') {
    
    // El ID del producto viene como parámetro GET en la URL (ej. ?action=delete&id=5)
    // Usamos el ID enviado por el formulario oculto en el listado
    $producto_id = $_POST['ProductoID'] ?? null;
    
    if (!$producto_id) {
        $mensaje = "Error: ID de producto no proporcionado para la eliminación.";
        header("Location: listado_productos.php?error=" . urlencode($mensaje));
        exit();
    }
    
    $sql = "DELETE FROM productos WHERE ProductoID = ?";
    
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$producto_id]);
        
        $mensaje = "Producto eliminado con éxito.";
    } catch (PDOException $e) {
        // En caso de error de BD, muestra el error
        $mensaje = "Error al eliminar el producto: " . $e->getMessage();
    }
    
    header("Location: listado_productos.php?msg=" . urlencode($mensaje));
    exit();
}
elseif ($action === 'update_producto') {
    $producto_id= $_POST['ProductoID'];
    $nombre = $_POST['nombreP'];
    $codigo_barra = $_POST['codigoB'] ?? null;
    $descripcion = $_POST['desP'];
    $precio_venta = $_POST['precioP'];
    $stock = (int)$_POST['stockP'];
    $stock_minimo = (int)$_POST['stockM'];
    $categoria_id = (int)$_POST['CategoriaID'];
    $imagen_url = null;

    $sql = "UPDATE productos SET
                Nombre = ?,
                CodigoBarra = ?,
                Descripcion = ?,
                PrecioVenta = ?,
                Stock = ?,
                StockMinimo = ?,
                CategoriaID = ?
            WHERE  ProductoID = ?";

    try{
        $smmt = $pdo->prepare($sql);
        $smmt->execute([
            $nombre,
            $codigo_barra,
            $descripcion,
            $precio_venta,
            $stock,
            $stock_minimo,
            $categoria_id,
            $producto_id
        ]);

        $mensaje = "Producto '{$nombre}' actualizado con exito.";
    }catch (PDOException $e){
        $mensaje = "Error al actualizar el producto: " . $e->getMessage();
    }

    header("Location: listado_productos.php?msg=" . urldecode($mensaje));
}
} else {
    // Si se accede directamente sin POST
    header("Location: procesar_registro.php");
    exit();
}
?>