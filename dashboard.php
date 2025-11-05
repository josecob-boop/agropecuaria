<?php
// dashboard.php - Panel de navegación principal y control de acceso
session_start();

// ⚠️ LÓGICA DE CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    // Si no hay sesión, redirige al login
    header("Location: login.html");
    exit;
}

// Opcional: Si necesitas la conexión PDO para mostrar estadísticas rápidas aquí.
// require 'db_connect.php'; 

// La variable $_SESSION['user_name'] contiene el nombre del usuario logueado.
$user_name = $_SESSION['user_name'] ?? 'Usuario'; 
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agropecuaria - Dashboard</title>
    <link rel="stylesheet" href="styleisa.css">
    <link rel="icon" type="image/png" href="https://drive.google.com/thumbnail?id=1Oc3K0QRVUrfh7K_Rlxqo4Y9zv5aV7g1D&sz=w800">
</head>

<body>
<main class="dashboard-content">
    <h1>¡Bienvenido/a, <?php echo htmlspecialchars($user_name); ?>!</h1>
    <p>Selecciona una opción para comenzar a trabajar:</p>

    <div class="modulo-grid">
        <a href="inventario.php" class="modulo-card">
            <h3>📦 Gestión de Inventario</h3>
            <p>Control de stock, alertas bajas y productos.</p>
        </a>
        
        <a href="ventas_pos.php" class="modulo-card">
            <h3>💰 Punto de Venta (POS)</h3>
            <p>Registrar ventas y emitir facturas al instante.</p>
        </a>
        
        <a href="clientes_proveedores.php" class="modulo-card">
            <h3>👥 Clientes y Proveedores</h3>
            <p>Administración de contactos y pedidos.</p>
        </a>
        
        <a href="reportes.php" class="modulo-card">
            <h3>📈 Reportes y Analíticas</h3>
            <p>Consultar ventas, rentabilidad y stock.</p>
        </a>
    </div>
</main>
    <footer>
        <p>© 2025 Agropecuaria. Todos los derechos reservados. | <a href="contacto.html">Contacto</a></p>
    </footer>
    <script src="js/main.js"></script>
</body>
</html>
