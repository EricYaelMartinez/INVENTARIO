<?php
// 1. Iniciar la sesión
session_start();

// 2. Control de Acceso: Verificar si el usuario está logueado
if (!isset($_SESSION['usuario_id'])) {
    
    // Si no hay sesión, redirigir al login
    header("Location: Log_usuario.php"); 
    exit(); // Detener la ejecución del script
}

// Si la sesión existe, el código continúa
$nombre_usuario = $_SESSION['usuario_nombre'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema de Inventario y Ventas</title>
    <link rel="stylesheet" href="../TIENDA_INV/css/dashboard.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>¡Hola <?php echo htmlspecialchars($nombre_usuario); ?>! Bienvenida a Miscelanea GOYITOO</h1>
            <p>Dashboard de Inventario y Análisis de Rendimiento.</p>
        </div>
        <div class="logo-container">
            <img src="../TIENDA_INV/img/GOYITO.png" alt="Logo del Sistema" onerror="this.style.display='none'"> 
        </div>
    </div>
    
    <div class="container">
        
        <h2>Acceso Rápido</h2>
        <div class="menu">
            <a href="../TIENDA_INV/formulario_productos.php">Gestión de Productos</a>
            <a href="../TIENDA_INV/formulario_venta.php">Registro de Ventas</a>
            <a href="formulario_entrada.php">Entradas de Mercancía</a>
            <a href="../TIENDA_INV/formulario_corte.php">Reportes y Corte de Caja</a>
            <a href="../TIENDA_INV/formulario_proveedores.php">Proveedores</a>
        </div>

        <h2>Análisis de Rendimiento</h2>
        <div class="charts-grid">
            
            <div class="chart-card">
                <h2>Ventas por Día (Últimos 7 días)</h2>
                <canvas id="ventasSemanales"></canvas>
            </div>

            <div>
                <div class="chart-card" style="margin-bottom: 30px;">
                    <h2>Total Vendido Hoy</h2>
                    <canvas id="ventasDelDia"></canvas> 
                </div>

                <div class="chart-card">
                    <h2>Productos Más Vendidos (Top 5)</h2>
                    <canvas id="productosMasVendidos"></canvas>
                </div>
            </div>
        </div>

        <a href="logout.php" class="logout">Cerrar Sesión</a>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            fetch('api_graficos.php')
                .then(response => response.json())
                .then(data => {
                    renderVentasSemanales(data.semanales);
                    renderVentasDelDia(data.hoy.total_hoy);
                    renderProductosMasVendidos(data.top_productos);
                })
                .catch(error => console.error('Error al cargar datos de gráficos:', error));
        });

        // Gráfico 1: Ventas Semanales (Líneas/Barras)
        function renderVentasSemanales(datos) {
            const dias = datos.map(d => d.dia);
            const totales = datos.map(d => parseFloat(d.total_vendido || 0));
            new Chart(document.getElementById('ventasSemanales'), {
                type: 'bar',
                data: {
                    labels: dias,
                    datasets: [{
                        label: 'Ventas Diarias ($)',
                        data: totales,
                        backgroundColor: 'rgba(226, 172, 63, 0.7)', /* Color Dorado */
                        borderColor: 'rgba(226, 172, 63, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });
        }

        // Gráfico 2: Ventas del Día (Texto)
        function renderVentasDelDia(totalHoy) {
            const ctx = document.getElementById('ventasDelDia').getContext('2d');
            ctx.font = '36px Arial';
            ctx.fillStyle = '#7BA58D'; // Color Verde
            ctx.textAlign = 'center';
            // Ajuste la posición para que se vea bien en el canvas
            ctx.fillText(`$${parseFloat(totalHoy || 0).toFixed(2)}`, 150, 80); 
            ctx.font = '16px Arial';
            ctx.fillStyle = '#924F1B'; // Color Marrón
            ctx.fillText('Ventas Netas Hoy', 150, 110);
        }

        // Gráfico 3: Productos más Vendidos (Pie/Doughnut)
        function renderProductosMasVendidos(datos) {
            const nombres = datos.map(d => d.producto);
            const cantidades = datos.map(d => parseInt(d.cantidad_vendida));
            new Chart(document.getElementById('productosMasVendidos'), {
                type: 'doughnut',
                data: {
                    labels: nombres,
                    datasets: [{
                        data: cantidades,
                        // Paleta de colores más acorde a tu tema
                        backgroundColor: [
                            '#E2AC3F', /* Dorado */
                            '#7BA58D', /* Verde */
                            '#924F1B', /* Marrón */
                            '#2A0308', /* Oscuro */
                            '#cccccc' 
                        ]
                    }]
                },
                options: {
                    responsive: true,
                    plugins: { legend: { position: 'right' } }
                }
            });
        }
    </script>
</body>
</html>