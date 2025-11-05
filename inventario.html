<?php 
    include 'productos.php'; // Incluimos el script que carga los datos de $productos
?>

<main class="dashboard-content">
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
                        
                        <form method="POST" action="procesar_producto.php" style="display:inline;" onsubmit="return confirm('¿Seguro que desea eliminar este producto?');">
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
