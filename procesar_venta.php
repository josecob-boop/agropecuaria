<?php
// procesar_venta.php - Registra la venta y descuenta el stock
session_start();
require 'db_connect.php'; 

// 1. CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Inicializar variables de sesión para mensajes
$_SESSION['success_message'] = null;
$_SESSION['error_message'] = null;

// Comprobar que los datos del formulario se enviaron
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    $_SESSION['error_message'] = "Método de envío no permitido.";
    header("Location: ventas_pos.php");
    exit;
}

// 2. OBTENER Y DECODIFICAR DATOS
$id_usuario = $_SESSION['user_id'];
$id_cliente = filter_input(INPUT_POST, 'id_cliente', FILTER_VALIDATE_INT) ?? null;
$total_venta = filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);
$cart_data_json = $_POST['cart_data'] ?? '[]'; // Datos del carrito en JSON

$cart_items = json_decode($cart_data_json, true);

if (empty($cart_items) || $total_venta === false || $total_venta <= 0) {
    $_SESSION['error_message'] = "El carrito de ventas está vacío o el total es inválido.";
    header("Location: ventas_pos.php");
    exit;
}

// ===============================================
// 3. INICIO DE LA TRANSACCIÓN SQL (ACID)
// ===============================================
try {
    // 3.1. Iniciar la transacción: Asegura que si algo falla, TODO se revierte (ROLLBACK)
    $pdo->beginTransaction(); 

    // 4. REGISTRAR ENCABEZADO DE LA VENTA (Tabla ventas)
    $sql_venta = "INSERT INTO ventas (id_cliente, id_usuario, total, estado_pago) 
                  VALUES (:id_cliente, :id_usuario, :total, 'pagado')";
    $stmt_venta = $pdo->prepare($sql_venta);
    $stmt_venta->execute([
        ':id_cliente' => $id_cliente,
        ':id_usuario' => $id_usuario,
        ':total' => $total_venta
    ]);

    // Obtener el ID de la venta recién insertada
    $id_venta = $pdo->lastInsertId();

    // 5. PROCESAR DETALLE DE VENTA Y ACTUALIZAR STOCK
    $sql_detalle = "INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario) 
                    VALUES (:id_venta, :id_producto, :cantidad, :precio_unitario)";
    $sql_stock = "UPDATE productos SET stock = stock - :cantidad WHERE id = :id_producto AND stock >= :cantidad";

    foreach ($cart_items as $item) {
        $id_producto = $item['id'];
        $cantidad = $item['cantidad'];
        $precio_unitario = $item['precio']; // Precio al momento de la venta

        // A. Descontar Stock
        $stmt_stock = $pdo->prepare($sql_stock);
        $stmt_stock->execute([
            ':cantidad' => $cantidad,
            ':id_producto' => $id_producto
        ]);
        
        // Verificar si el stock se descontó correctamente (afectó 1 fila)
        if ($stmt_stock->rowCount() === 0) {
             // Si rowCount es 0, significa que stock < cantidad, y debemos fallar
             throw new Exception("Stock insuficiente para el producto ID: " . $id_producto);
        }

        // B. Registrar Detalle
        $stmt_detalle = $pdo->prepare($sql_detalle);
        $stmt_detalle->execute([
            ':id_venta' => $id_venta,
            ':id_producto' => $id_producto,
            ':cantidad' => $cantidad,
            ':precio_unitario' => $precio_unitario
        ]);
    }

    // 6. FINALIZAR TRANSACCIÓN: Si todo salió bien, confirma los cambios
    $pdo->commit();

    $_SESSION['success_message'] = "Venta registrada exitosamente (ID: {$id_venta}). El inventario ha sido actualizado.";

} catch (Exception $e) {
    // 7. MANEJO DE ERROR: Si algo falló (stock, SQL, etc.), revertir todo
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    
    $_SESSION['error_message'] = "Error al registrar la venta: " . $e->getMessage();
}

// 8. REDIRECCIÓN FINAL
header("Location: ventas_pos.php");
exit;
?>
