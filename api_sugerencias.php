

<?php
include_once ('conexion.php'); 
header('Content-Type: application/json');

$term = $_GET['term'] ?? '';

if (empty($term)) {
    echo json_encode([]);
    exit();
}

// Consulta para buscar productos por nombre o código de barras (parcialmente)
$sql = "
    SELECT ProductoID, Nombre, PrecioVenta, CodigoBarra, Imagen, Stock 
    FROM Productos 
    WHERE Nombre LIKE ? OR CodigoBarra LIKE ? 
    LIMIT 10
";

try {
    $stmt = $pdo->prepare($sql);
    
    $searchTerm = "%" . $term . "%";
    
    // Utilizamos el mismo parámetro de búsqueda para ambos campos
    $stmt->execute([$searchTerm, $searchTerm]);
    $resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($resultados);

} catch (PDOException $e) {
    echo json_encode([]);
}
?>