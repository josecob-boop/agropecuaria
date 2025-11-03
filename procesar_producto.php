<?php
// procesar_producto.php
session_start();
require 'db_connect.php'; 

// Control de acceso: Asegurar que solo usuarios logueados puedan procesar
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 1. OBTENER LA ACCIÓN SOLICITADA
$action = $_POST['action'] ?? $_GET['action'] ?? '';

// 2. PROCESAR ACCIÓN
try {
    if ($action === 'create' || $action === 'update') {
        
        // Sanear y obtener datos comunes (Create y Update)
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $descripcion = filter_input(INPUT_POST, 'descripcion', FILTER_SANITIZE_STRING);
        $precio_venta = filter_input(INPUT_POST, 'precio_venta', FILTER_VALIDATE_FLOAT);
        $stock = filter_input(INPUT_POST, 'stock', FILTER_VALIDATE_INT);
        $id_proveedor = filter_input(INPUT_POST, 'id_proveedor', FILTER_VALIDATE_INT);

        // Validación de datos
        if (!$nombre || $precio_venta === false || $stock === false) {
            throw new Exception("Error en la validación de los campos.");
        }

        if ($action === 'create') {
            // A. LÓGICA DE CREAR (INSERT)
            $sql = "INSERT INTO productos (nombre, descripcion, precio_venta, stock, id_proveedor) 
                    VALUES (:nombre, :descripcion, :precio, :stock, :proveedor)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio_venta,
                ':stock' => $stock,
                ':proveedor' => $id_proveedor
            ]);
            $message = "Producto creado exitosamente.";

        } elseif ($action === 'update') {
            // B. LÓGICA DE ACTUALIZAR (UPDATE)
            $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
            if (!$id) { throw new Exception("ID de producto no válido para actualizar."); }

            $sql = "UPDATE productos SET nombre = :nombre, descripcion = :descripcion, 
                    precio_venta = :precio, stock = :stock, id_proveedor = :proveedor 
                    WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':precio' => $precio_venta,
                ':stock' => $stock,
                ':proveedor' => $id_proveedor,
                ':id' => $id
            ]);
            $message = "Producto actualizado exitosamente.";
        }

    } elseif ($action === 'delete') {
        // C. LÓGICA DE ELIMINAR (DELETE)
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        if (!$id) { throw new Exception("ID de producto no válido para eliminar."); }

        $sql = "DELETE FROM productos WHERE id = :id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([':id' => $id]);
        $message = "Producto eliminado exitosamente.";
    } 

    // 3. REDIRECCIÓN FINAL (ÉXITO)
    $_SESSION['success_message'] = $message;
    header("Location: inventario.html");
    exit;

} catch (Exception $e) {
    // 4. MANEJO DE ERROR
    $_SESSION['error_message'] = "Operación fallida: " . $e->getMessage();
    header("Location: inventario.html");
    exit;
}

// Si se accede directamente, redirigir
header("Location: inventario.html");
exit;
?>
