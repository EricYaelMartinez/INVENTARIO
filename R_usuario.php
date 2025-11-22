<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=p, initial-scale=1.0">
    <link rel="stylesheet" href="../TIENDA_INV/css/R_usuarios.css">
    <title>Registro de Usuarios</title>
</head>
<body>
    <div class="contenedor1">
        <div class="titulo">Registrate</div>
            <form action="Reg_usuario.php" method="post">
                <div class="detalles-usuario">
                    <div class="input-box">
                        <span class="details">Nombre Completo</span>
                        <input type="text" placeholder="Ingresa tu nombre y apellidos" name="nombre" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Correo Electronico</span>
                        <input type="email" placeholder="Ingresa un Correo" name="correo" required>
                    </div>

                    <div class="input-box">
                        <span class="details">Contraseña</span>
                        <input type="password" placeholder="Crea una contraseña" name="pass" required>
                    </div>
                </div>

                <div class="boton">
                    <button type="submit">Registrar</button>
                </div>
            </form>

    </div>
</body>
</html>