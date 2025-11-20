<?php 

include_once ('conexion.php');

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    $nombre_proveedor = trim($_POST['nombrePro']);
    $contacto = trim($_POST['contacto']);
    $telefono = trim($_POST['tel']);

    $sql = "INSERT INTO proveedores (NombreProveedor, Contacto, Telefono) 
    VALUES (?, ?, ?)";

    try{
        $stmt = $pdo->prepare($sql);

        $stmt->execute([
            $nombre_proveedor,
            $contacto,
            $telefono
        ]);
        $mensaje = "Proveedor '{$nombre_proveedor}' registrado con exito.";
    }catch(PDOException $e){
        $mensaje = "Error al registrar el proveedor: " . $e->getMessage();
    }
    header("Location: formulario_proveedores.php?msg=" . urlencode($mensaje));
    exit();
} else {
    header("Location: formulario_proveedores.php");
    exit();
}

?>