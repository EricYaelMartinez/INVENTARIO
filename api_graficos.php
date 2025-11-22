<?php
include_once ('conexion.php'); 
header('Content-Type: application/json');

$pdo = $GLOBALS['pdo']; // Asegúrate de que $pdo esté disponible

$data = [];

// 1. VENTAS SEMANALES (Últimos 7 días)
$sql_semanales = "
    SELECT 
        DATE(Fecha) AS dia, 
        SUM(Total) AS total_vendido 
    FROM 
        ventas
    WHERE 
        Fecha >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
    GROUP BY 
        dia
    ORDER BY 
        dia ASC;
";
$data['semanales'] = $pdo->query($sql_semanales)->fetchAll();

// 2. VENTAS DEL DÍA
$sql_hoy = "
    SELECT 
        SUM(Total) AS total_hoy 
    FROM 
        ventas
    WHERE 
        DATE(Fecha) = CURDATE();
";
$data['hoy'] = $pdo->query($sql_hoy)->fetch();

// 3. PRODUCTOS MÁS VENDIDOS (Top 5 por cantidad)
$sql_top_productos = "
    SELECT 
        P.Nombre AS producto, 
        SUM(DV.Cantidad) AS cantidad_vendida 
    FROM 
        detalleventa DV
    JOIN 
        productos P ON DV.ProductoID = P.ProductoID
    GROUP BY 
        P.Nombre
    ORDER BY 
        cantidad_vendida DESC
    LIMIT 5;
";
$data['top_productos'] = $pdo->query($sql_top_productos)->fetchAll();

echo json_encode($data);
?>