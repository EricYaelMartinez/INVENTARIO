<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Proveedores</title>
</head>
<body>
    <?php 
    // Captura el mensaje de la redirección
    $msg = $_GET['msg'] ?? '';
    if ($msg) {
        $color = strpos($msg, 'Error') !== false ? 'red' : 'green';
        echo "<p style='color:{$color}; padding: 10px; border: 1px solid {$color};'>". htmlspecialchars($msg) ."</p>";
    }
    ?>

    <nav class="nav1">
        <li class="menu1"><a href="dashboard.php"><b>Inicio</b></a></li>
        <li class="menu1"><a href="listado_productos.php"><b>Lista de Productos</b></a></li>
        <li class="menu1"><a href="dashboard.php"><b>Inicia Sesion</b></a></li>
    </nav>

    <div class="contenido">
       
        <form action="registro_proveedores.php" method="post" class="form">

            <h2 class="form-title">Registro de Proveedores de Mercancia</h2>
            <div class="form-container">

                    <div class="input-box">
                        <span class="details">Nombre de Proveedor</span>
                        <input type="text" placeholder="Ingresa el nombre del proveedor" name="nombrePro" required>
                    </div>
                    
                    <div class="input-box">
                        <span class="details">Contacto</span>
                        <input type="text" placeholder="Ingresa el nombre del preventista" name="contacto" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Numero de Telefono</span>
                        <input type="text" placeholder="Telefono para contactar al preventista" name="tel" required>
                    </div>
                

                <input type="submit" class="form-submit" value="Resgistrar">

            </div>

        </form>
    </div>
</body>
</html>