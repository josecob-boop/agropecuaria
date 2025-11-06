<?php
// producto_form.php - Formulario para Crear y Editar productos
session_start();
require 'db_connect.php'; 

// 1. CONTROL DE ACCESO (Asegura que el usuario esté logueado)
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Inicializar variables
$producto_actual = [
    'id' => '',
    'nombre' => '',
    'descripcion' => '',
    'precio_venta' => '',
    'stock' => '0',
    'id_proveedor' => ''
];
$proveedores = [];
$form_action = 'create'; // Acción por defecto

// 2. OBTENER PROVEEDORES para el desplegable (Select)
try {
    $stmt_prov = $pdo->query("SELECT id, nombre FROM proveedores ORDER BY nombre ASC");
    $proveedores = $stmt_prov->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_message = "Error al cargar proveedores.";
}


// 3. LÓGICA DE EDICIÓN (Cargar datos si se recibe un ID)
if (isset($_GET['id'])) {
    $id_producto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id_producto) {
        try {
            // Consulta para obtener los datos del producto a editar
            $stmt_prod = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
            $stmt_prod->bindParam(':id', $id_producto);
            $stmt_prod->execute();
            $producto = $stmt_prod->fetch(PDO::FETCH_ASSOC);

            if ($producto) {
                $producto_actual = $producto;
                $form_action = 'update';
            } else {
                // Si el ID no existe
                header("Location: inventario.php"); // 🟢 AJUSTE DE RUTA: inventario.php
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Error al cargar producto para edición.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Producto</title>
    <link rel="stylesheet" href="styleisa.css"> 
    <link rel="icon" type="image/png" href="URL_DEL_FAVICON">
</head>
<body>
    <main class="dashboard-content">
        <section id="product-form-view">
            <h1><?php echo ($form_action == 'update') ? '✏️ Editar Producto: ' . htmlspecialchars($producto_actual['nombre']) : '➕ Nuevo Producto'; ?></h1>
            
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="procesar_producto.php" class="form-card">
                
                <input type="hidden" name="action" value="<?php echo $form_action; ?>">
                <input type="hidden" name="id" value="<?php echo $producto_actual['id']; ?>">

                <label for="nombre">Nombre del Producto:</label>
                <input type="text" id="nombre" name="nombre" placeholder="Semillas de Maíz" required 
                       value="<?php echo htmlspecialchars($producto_actual['nombre']); ?>">

                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion" placeholder="Detalles, uso, características..." required><?php echo htmlspecialchars($producto_actual['descripcion']); ?></textarea>

                <label for="precio_venta">Precio de Venta ($):</label>
                <input type="number" id="precio_venta" name="precio_venta" step="0.01" min="0" required 
                       value="<?php echo htmlspecialchars($producto_actual['precio_venta']); ?>">

                <label for="stock">Stock Actual:</label>
                <input type="number" id="stock" name="stock" min="0" required 
                       value="<?php echo htmlspecialchars($producto_actual['stock']); ?>">

                <label for="id_proveedor">Proveedor:</label>
                <select id="id_proveedor" name="id_proveedor">
                    <option value="">Selecciona un Proveedor</option>
                    <?php foreach ($proveedores as $proveedor): ?>
                        <option value="<?php echo $proveedor['id']; ?>"
                                <?php echo ($producto_actual['id_proveedor'] == $proveedor['id']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($proveedor['nombre']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                
                <button type="submit" class="btn-primary-lg">Guardar Producto</button>
                <a href="inventario.php" class="btn-secondary">Cancelar</a> </form>
        </section>
    </main>

    </body>
</html>
