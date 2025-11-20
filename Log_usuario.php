<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesion</title>
</head>
<body>
    <form action="Log_proceso.php" method="post" class="form">

        <h2 class="form-title">Inicio de Sesion</h2>
        <div class="form-container">
            <div class="form-group">
                <input type="gmail" class="form-input" name="correo" required>
                <label for="correo" class="form-label">Correo Electronico</label>
                <span class="form-line"></span>

                <input type="password" class="form-input" name="pass" required>
                <label for="pass" class="form-label">Contraseña</label>
                <span class="form-line"></span>
            </div>

            <input type="submit" class="form-submit" value="Ingresar">

        </div>

    </form>
</body>
</html>