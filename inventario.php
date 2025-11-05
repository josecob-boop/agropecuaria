<?php
// inventario.php - Vista de la tabla de inventario
session_start();

// ⚠️ 1. CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 2. INCLUIR LÓGICA DE DATOS
include 'productos.php'; // Este script ya contiene la lógica de seguridad por rol y carga $productos

// Opcional: Mostrar mensajes de éxito/error de operaciones previas (CRUD)
$message = $_SESSION['success_message'] ?? $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

// Asumimos que $productos.php ya definió $productos
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inventario - Agropecuaria</title>
    <link rel="stylesheet" href="styleisa.css">
    <link rel="icon" type="image/png" href="https://drive.google.com/thumbnail?id=1Oc3K0QRVUrfh7K_Rlxqo4Y9zv5aV7g1D&sz=w800">
</head>

<body>
    <main class="dashboard-content">
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo isset($_SESSION['success_message']) ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <section id="inventario-view">
            <h1>Inventario de Productos 🌾</h1>
            
            <a href="producto_form.php" class="btn-primary">➕ Nuevo Producto</a>

            <?php if (!empty($productos)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Stock</th>
                        <th>Precio Venta</th>
                        <th>Proveedor</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos as $producto): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($producto['id']); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                        <td class="<?php echo ($producto['stock'] < 10) ? 'stock-low' : ''; ?>">
                            <?php echo htmlspecialchars($producto['stock']); ?>
                        </td>
                        <td>$<?php echo number_format($producto['precio_venta'], 2); ?></td>
                        <td><?php echo htmlspecialchars($producto['nombre_proveedor'] ?? 'N/A'); ?></td>
                        <td>
                            <a href="producto_form.php?id=<?php echo $producto['id']; ?>" class="btn-secondary">✏️ Editar</a>
                            
                            <form method="POST" action="procesar_producto.php" style="display:inline;" onsubmit="return confirm('¿Seguro que desea eliminar este producto? Esta acción no se puede deshacer.');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                <button type="submit" class="btn-danger">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No hay productos registrados en el inventario.</p>
                <?php if (isset($error_message)): ?>
                    <p class="error"><?php echo $error_message; ?></p>
                <?php endif; ?>
            <?php endif; ?>
        </section>
    </main>

    <script src="js/main.js"></script>
</body>
</html>
