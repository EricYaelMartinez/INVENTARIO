<?php
include_once ('conexion.php'); 
header('Content-Type: application/json');

$codigo = $_GET['codigo'] ?? '';

if (empty($codigo)) {
    echo json_encode(['error' => 'Código de barra o nombre no proporcionado.']);
    exit();
}

// Consulta para buscar por CodigoBarra o Nombre
$sql = "SELECT ProductoID, Nombre, PrecioVenta, Stock, Imagen, CodigoBarra 
        FROM productos 
        WHERE CodigoBarra = ? OR Nombre LIKE ? LIMIT 1";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$codigo, "%$codigo%"]);
    $producto = $stmt->fetch();

    if ($producto) {
        echo json_encode($producto);
    } else {
        echo json_encode(['error' => 'Producto no encontrado.']);
    }

} catch (PDOException $e) {
    echo json_encode(['error' => 'Error de base de datos: ' . $e->getMessage()]);
}
?>