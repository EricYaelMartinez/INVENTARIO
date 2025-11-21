<?php
include_once ('conexion.php'); 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // 1. Recoger datos del formulario
    $usuarioID = $_POST['usuarioID'] ?? null;
    $fechaCorte = $_POST['fechaCorte'] ?? date('Y-m-d');
    $montoInicial = $_POST['montoInicial'] ?? 0.00;
    
    // Totales calculados por PHP/JS
    $totalVentasCalculado = $_POST['totalVentasCalculado'] ?? 0.00;
    $totalVentasEfectivo = $_POST['totalVentasEfectivo'] ?? 0.00; // Asumimos todas las ventas son efectivo
    $totalCajaEsperado = $_POST['totalCajaEsperado'] ?? 0.00;
    
    // Valores ingresados manualmente
    $totalCajaFisico = $_POST['totalCajaFisico'] ?? 0.00;
    $diferencia = $_POST['diferencia'] ?? 0.00;

    // 2. Sentencia SQL de Inserción
    $sql = "INSERT INTO CorteCaja (
                Fecha, MontoInicialCaja, TotalVentasEfectivo, TotalVentasCalculado, 
                TotalCajaEsperado, TotalCajaFísico, Diferencia, UsuarioID
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            
    try {
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $fechaCorte, 
            $montoInicial, 
            $totalVentasEfectivo, 
            $totalVentasCalculado, 
            $totalCajaEsperado, 
            $totalCajaFisico, 
            $diferencia, 
            $usuarioID
        ]);
        
        $corteID = $pdo->lastInsertId();
        $mensaje = "Corte de Caja #{$corteID} registrado con éxito. Diferencia: $" . number_format($diferencia, 2);

    } catch (PDOException $e) {
        $mensaje = "ERROR al registrar el corte: " . $e->getMessage();
    }
    
    // 3. Redirigir
    header("Location: formulario_corte.php?msg=" . urlencode($mensaje));
    exit();

} else {
    header("Location: formulario_corte.php");
    exit();
}
?>