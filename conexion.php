<?php
// Define las credenciales de conexión
$host = 'localhost'; // Tu host de BD
$db   = 'tienda_inventario'; // Nombre de tu base de datos
$user = 'yael'; // Tu usuario de base de datos
$pass = '12345678'; // Tu contraseña de base de datos
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Activa el manejo de errores
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,      // Devuelve los resultados como arrays asociativos
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un error y termina
    die("Error de conexión a la base de datos: " . $e->getMessage());
}
?>