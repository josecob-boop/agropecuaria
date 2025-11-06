<?php
// ventas_pos.php - Interfaz del Punto de Venta (POS)
session_start();

// 1. Lógica de Control de Acceso
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 2. Incluir la lógica que carga los datos de productos y clientes
include 'ventas_data.php';

// Mostrar mensajes de éxito/error de la transacción anterior
$message = $_SESSION['success_message'] ?? $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Punto de Venta (POS) - Agropecuaria</title>
    <link rel="stylesheet" href="CSS/styleisa.css">
    <link rel="icon" type="image/png" href="https://drive.google.com/thumbnail?id=1Oc3K0QRVUrfh7K_Rlxqo4Y9zv5aV7g1D&sz=w800">
</head>
<body>
    <main class="dashboard-content">
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo isset($_SESSION['success_message']) ? 'alert-success' : 'alert-danger'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>
        
        <h1>💰 Punto de Venta (POS)</h1>

        <div class="pos-layout">
            
            <section id="productos-selection">
                <h2>Catálogo Rápido</h2>
                <input type="text" id="search-product" placeholder="Buscar producto por nombre..." onkeyup="filterProducts()">
                
                <div id="product-list" class="product-cards-grid">
                    <?php foreach ($productos_disponibles as $p): ?>
                        <div class="product-card-pos" 
                             data-id="<?php echo $p['id']; ?>" 
                             data-nombre="<?php echo htmlspecialchars($p['nombre']); ?>"
                             data-precio="<?php echo $p['precio_venta']; ?>"
                             data-stock="<?php echo $p['stock']; ?>"
                             onclick="addItemToCart(<?php echo $p['id']; ?>, '<?php echo htmlspecialchars($p['nombre']); ?>', <?php echo $p['precio_venta']; ?>, <?php echo $p['stock']; ?>)">
                            
                            <h4><?php echo htmlspecialchars($p['nombre']); ?></h4>
                            <p>$<?php echo number_format($p['precio_venta'], 2); ?></p>
                            <span class="stock-info">Stock: <?php echo $p['stock']; ?></span>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($productos_disponibles)): ?>
                        <p class="error-message">Inventario vacío o error al cargar.</p>
                    <?php endif; ?>
                </div>
            </section>

            <section id="cart-summary">
                <h2>Resumen de la Venta</h2>
                
                <table id="cart-table" class="data-table">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Cant.</th>
                            <th>Subtotal</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody id="cart-body">
                        <tr><td colspan="4" style="text-align:center;">Agrega productos al carrito.</td></tr>
                    </tbody>
                </table>
                
                <div id="totals">
                    <p>Subtotal: <span id="subtotal">$0.00</span></p>
                    <p>Impuestos (ej. 13%): <span id="tax">$0.00</span></p>
                    <h3>TOTAL: <span id="grand-total">$0.00</span></h3>
                </div>

                <hr>

                <form id="checkout-form" method="POST" action="procesar_venta.php">
                    <input type="hidden" name="cart_data" id="cart-data">
                    <input type="hidden" name="total_amount" id="total-amount">
                    
                    <label for="id_cliente">Cliente (Opcional):</label>
                    <select name="id_cliente" id="id_cliente">
                        <option value="">Consumidor Final</option>
                        <?php foreach ($clientes as $c): ?>
                            <option value="<?php echo $c['id']; ?>">
                                <?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>

                    <button type="submit" class="btn-acceder" id="finalize-sale" disabled>Finalizar Venta</button>
                </form>

            </section>
        </div>
    </main>
    
    <script src="js/main.js"></script>
    </body>
</html>
