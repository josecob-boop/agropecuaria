<?php
// products.php
session_start();
require 'db_connect.php'; 

// ⚠️ CONTROL DE ACCESO BÁSICO: 
// Asegúrate de que solo usuarios logueados y con rol 'administrador' o 'vendedor' puedan ver el inventario.
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 🟢 VERIFICAR ROL: Asegura que solo el rol 'administrador' o 'vendedor' tenga acceso
$allowed_roles = ['administrador', 'vendedor'];
if (!isset($_SESSION['user_rol']) || !in_array($_SESSION['user_rol'], $allowed_roles)) {
    // Si no tiene el rol correcto, redirigir a una página de error o al dashboard
    header("Location: dashboard.php"); // Redirigir a una página accesible
    exit;
}
// ----------------------------------------------------

// 1. OBTENER DATOS DE LA BASE DE DATOS
try {
    // Consulta para obtener todos los productos y el nombre del proveedor (JOIN simple)
    $sql = "SELECT 
                p.id, 
                p.nombre, 
                p.descripcion, 
                p.precio_venta, 
                p.stock,
                pr.nombre AS nombre_proveedor
            FROM productos p
            LEFT JOIN proveedores pr ON p.id_proveedor = pr.id
            ORDER BY p.nombre ASC";
            
    $stmt = $pdo->query($sql);
    $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    // Manejo de error en la consulta
    $error_message = "Error al cargar productos: " . $e->getMessage();
    // En un sistema real, esto debería ser registrado, no mostrado al usuario.
    $productos = []; // Aseguramos que $productos es un array vacío en caso de fallo
}

?>
