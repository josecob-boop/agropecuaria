<?php
// ventas_data.php - Carga de productos y lógica inicial para el POS
session_start();
require 'db_connect.php'; 

// Control de acceso
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 1. OBTENER LISTA DE PRODUCTOS (SOLO CON STOCK > 0)
try {
    $stmt_productos = $pdo->query("SELECT id, nombre, precio_venta, stock FROM productos WHERE stock > 0 ORDER BY nombre ASC");
    $productos_disponibles = $stmt_productos->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // En un entorno de producción, esto debería ser un log, no un mensaje al usuario.
    $error_productos = "Error al cargar productos: " . $e->getMessage();
    $productos_disponibles = [];
}

// 2. OBTENER LISTA DE CLIENTES (Para facturación)
try {
    $stmt_clientes = $pdo->query("SELECT id, nombre, apellido FROM clientes ORDER BY nombre ASC");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_clientes = "Error al cargar clientes.";
    $clientes = [];
}

?>
