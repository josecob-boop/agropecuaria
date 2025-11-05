<?php
// reportes.php - Generación de reportes de ventas, stock y rentabilidad
session_start();
require 'db_connect.php'; 

// 1. CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Inicializar variables
$reporte_actual = $_GET['r'] ?? 'stock'; // Reporte por defecto: stock
$datos_reporte = [];

// 2. LÓGICA DE GENERACIÓN DE REPORTES
try {
    switch ($reporte_actual) {
        
        // ======================================
        // A. REPORTE DE STOCK ACTUAL (Más sencillo)
        // ======================================
        case 'stock':
            $sql_stock = "SELECT 
                            nombre, 
                            stock, 
                            precio_venta, 
                            (stock * precio_venta) AS valor_stock 
                          FROM productos 
                          ORDER BY stock ASC";
            $stmt_stock = $pdo->query($sql_stock);
            $datos_reporte = $stmt_stock->fetchAll(PDO::FETCH_ASSOC);
            $titulo_reporte = "Stock Actual y Valoración";
            break;

        // ======================================
        // B. REPORTE DE PRODUCTOS MÁS VENDIDOS (Top 10)
        // ======================================
        case 'vendidos':
            $sql_vendidos = "SELECT 
                                p.nombre, 
                                SUM(dv.cantidad) AS total_vendido,
                                SUM(dv.cantidad * dv.precio_unitario) AS ingresos_totales
                             FROM detalle_venta dv
                             JOIN productos p ON dv.id_producto = p.id
                             GROUP BY p.id, p.nombre
                             ORDER BY total_vendido DESC
                             LIMIT 10";
            $stmt_vendidos = $pdo->query($sql_vendidos);
            $datos_reporte = $stmt_vendidos->fetchAll(PDO::FETCH_ASSOC);
            $titulo_reporte = "Top 10 Productos Más Vendidos";
            break;
            
        // ======================================
        // C. REPORTE DE RESUMEN DE VENTAS POR PERÍODO (Total de Ventas)
        // ======================================
        case 'ventas_resumen':
            // Definir periodo (ej. los últimos 30 días)
            $fecha_inicio = date('Y-m-d', strtotime('-30 days'));
            $fecha_fin = date('Y-m-d');
            
            $sql_ventas = "SELECT 
                            DATE(fecha_venta) AS dia,
                            COUNT(id) AS num_ventas,
                            SUM(total) AS total_ingresado
                           FROM ventas
                           WHERE fecha_venta BETWEEN :inicio AND :fin
                           GROUP BY dia
                           ORDER BY dia DESC";
            
            $stmt_ventas = $pdo->prepare($sql_ventas);
            $stmt_ventas->execute([':inicio' => $fecha_inicio, ':fin' => $fecha_fin]);
            $datos_reporte = $stmt_ventas->fetchAll(PDO::FETCH_ASSOC);
            $titulo_reporte = "Resumen de Ventas (Últimos 30 Días)";
            break;
    }

} catch (PDOException $e) {
    $error_message = "Error al generar el reporte: " . $e->getMessage();
    $datos_reporte = [];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Módulo de Reportes</title>
</head>
<body>
    <main class="dashboard-content">
        <h1>📈 Módulo de Reportes</h1>
        
        <nav class="report-menu">
            <a href="reportes.php?r=stock" class="btn-report <?php echo ($reporte_actual === 'stock') ? 'active' : ''; ?>">Stock Actual</a>
            <a href="reportes.php?r=vendidos" class="btn-report <?php echo ($reporte_actual === 'vendidos') ? 'active' : ''; ?>">Más Vendidos</a>
            <a href="reportes.php?r=ventas_resumen" class="btn-report <?php echo ($reporte_actual === 'ventas_resumen') ? 'active' : ''; ?>">Ventas por Día</a>
        </nav>
        
        <section id="reporte-datos">
            <h2><?php echo $titulo_reporte ?? 'Seleccione un Reporte'; ?></h2>
            
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <?php if (!empty($datos_reporte)): ?>
                
                <table class="data-table report-table">
                    <thead>
                        <tr>
                        <?php 
                            // Generar cabeceras basadas en las claves del primer registro
                            $keys = array_keys($datos_reporte[0]);
                            foreach ($keys as $key) {
                                // Simple formateo de nombres de columna
                                echo "<th>" . htmlspecialchars(ucwords(str_replace('_', ' ', $key))) . "</th>";
                            }
                        ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($datos_reporte as $fila): ?>
                        <tr>
                            <?php foreach ($fila as $valor): ?>
                                <td><?php echo htmlspecialchars($valor); ?></td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
            <?php else: ?>
                <p>No se encontraron datos para este reporte en el período seleccionado.</p>
            <?php endif; ?>
        </section>
    </main>
    </body>
</html>
