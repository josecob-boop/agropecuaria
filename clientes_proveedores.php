<?php
// clientes_proveedores.php - Módulo de Gestión de Contactos
session_start();
require 'db_connect.php'; 

// Control de acceso
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Lógica para obtener la lista de Clientes
try {
    $stmt_clientes = $pdo->query("SELECT id, nombre, apellido, telefono, email FROM clientes ORDER BY nombre ASC");
    $clientes = $stmt_clientes->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_clientes = "Error al cargar clientes: " . $e->getMessage();
    $clientes = [];
}

// Lógica para obtener la lista de Proveedores
try {
    $stmt_prov = $pdo->query("SELECT id, nombre, contacto, telefono, email FROM proveedores ORDER BY nombre ASC");
    $proveedores = $stmt_prov->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error_proveedores = "Error al cargar proveedores: " . $e->getMessage();
    $proveedores = [];
}

// Mensajes de éxito/error de operaciones anteriores
$message = $_SESSION['success_message'] ?? $_SESSION['error_message'] ?? '';
unset($_SESSION['success_message'], $_SESSION['error_message']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <title>Gestión de Clientes y Proveedores</title>
</head>
<body>
    <main class="dashboard-content">
        <h1>👥 Gestión de Contactos</h1>
        
        <?php if (!empty($message)): ?>
            <p class="<?php echo isset($_SESSION['success_message']) ? 'success-message' : 'error-message'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </p>
        <?php endif; ?>

        <section id="proveedores-view">
            <h2>Proveedores</h2>
            <a href="contacto_form.php?type=proveedor" class="btn-primary">➕ Nuevo Proveedor</a>

            <?php if (!empty($proveedores)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Contacto Principal</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($proveedores as $p): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($p['contacto']); ?></td>
                        <td><?php echo htmlspecialchars($p['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($p['email']); ?></td>
                        <td>
                            <a href="contacto_form.php?type=proveedor&id=<?php echo $p['id']; ?>" class="btn-secondary">✏️ Editar</a>
                            <form method="POST" action="procesar_contacto.php" style="display:inline;" onsubmit="return confirm('¿Eliminar a <?php echo htmlspecialchars($p['nombre']); ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="proveedor">
                                <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                <button type="submit" class="btn-danger">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No hay proveedores registrados. <?php echo $error_proveedores ?? ''; ?></p>
            <?php endif; ?>
        </section>
        
        <hr> 
        
        <section id="clientes-view">
            <h2>Clientes</h2>
            <a href="contacto_form.php?type=cliente" class="btn-primary">➕ Nuevo Cliente</a>

            <?php if (!empty($clientes)): ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Nombre Completo</th>
                        <th>Teléfono</th>
                        <th>Email</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($clientes as $c): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($c['nombre'] . ' ' . $c['apellido']); ?></td>
                        <td><?php echo htmlspecialchars($c['telefono']); ?></td>
                        <td><?php echo htmlspecialchars($c['email']); ?></td>
                        <td>
                            <a href="contacto_form.php?type=cliente&id=<?php echo $c['id']; ?>" class="btn-secondary">✏️ Editar</a>
                            <form method="POST" action="procesar_contacto.php" style="display:inline;" onsubmit="return confirm('¿Eliminar a <?php echo htmlspecialchars($c['nombre']); ?>?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="type" value="cliente">
                                <input type="hidden" name="id" value="<?php echo $c['id']; ?>">
                                <button type="submit" class="btn-danger">🗑️ Eliminar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <?php else: ?>
                <p>No hay clientes registrados. <?php echo $error_clientes ?? ''; ?></p>
            <?php endif; ?>
        </section>
    </main>
    </body>
</html>
