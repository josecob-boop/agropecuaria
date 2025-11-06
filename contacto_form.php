<?php
// contacto_form.php - Formulario para Crear y Editar Clientes/Proveedores
session_start();
require 'db_connect.php'; 

// 1. CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// 2. OBTENER TIPO DE CONTACTO (Obligatorio)
$type = $_GET['type'] ?? null;
if (!in_array($type, ['cliente', 'proveedor'])) {
    header("Location: clientes_proveedores.php");
    exit;
}

// Determinar el nombre de la tabla y título
$is_cliente = ($type === 'cliente');
$table = $is_cliente ? 'clientes' : 'proveedores';
$title = $is_cliente ? 'Cliente' : 'Proveedor';

// Inicializar variables del formulario
$contacto_actual = [
    'id' => '',
    'nombre' => '',
    'apellido' => '', 
    'telefono' => '',
    'email' => '',
    'direccion' => '', 
    'contacto' => ''   
];
$form_action = 'create';

// 3. LÓGICA DE EDICIÓN (Cargar datos si se recibe un ID)
if (isset($_GET['id'])) {
    $id_contacto = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
    
    if ($id_contacto) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE id = :id");
            $stmt->bindParam(':id', $id_contacto);
            $stmt->execute();
            $contacto = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($contacto) {
                $contacto_actual = array_merge($contacto_actual, $contacto);
                $form_action = 'update';
            } else {
                header("Location: clientes_proveedores.php");
                exit;
            }
        } catch (PDOException $e) {
            $error_message = "Error al cargar datos para edición.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de <?php echo $title; ?></title>
    <link rel="stylesheet" href="CSS/styleisa.css">
    <link rel="icon" type="image/png" href="URL_DEL_FAVICON">
</head>
<body>
    <main class="dashboard-content">
        <section id="contacto-form-view">
            <h1><?php echo ($form_action == 'update') ? '✏️ Editar ' . $title : '➕ Nuevo ' . $title; ?></h1>
            
            <?php if (isset($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>

            <form method="POST" action="procesar_contacto.php" class="form-card">
                
                <input type="hidden" name="action" value="<?php echo $form_action; ?>">
                <input type="hidden" name="type" value="<?php echo $type; ?>">
                <input type="hidden" name="id" value="<?php echo $contacto_actual['id']; ?>">

                <label for="nombre">Nombre (o Razón Social):</label>
                <input type="text" id="nombre" name="nombre" required 
                       value="<?php echo htmlspecialchars($contacto_actual['nombre']); ?>">

                <?php if ($is_cliente): ?>
                    <label for="apellido">Apellido:</label>
                    <input type="text" id="apellido" name="apellido" 
                           value="<?php echo htmlspecialchars($contacto_actual['apellido']); ?>">
                    
                    <label for="direccion">Dirección:</label>
                    <input type="text" id="direccion" name="direccion" 
                           value="<?php echo htmlspecialchars($contacto_actual['direccion']); ?>">
                <?php endif; ?>
                
                <?php if (!$is_cliente): ?>
                    <label for="contacto">Contacto Principal:</label>
                    <input type="text" id="contacto" name="contacto" 
                           value="<?php echo htmlspecialchars($contacto_actual['contacto']); ?>">
                <?php endif; ?>

                <label for="email">Email:</label>
                <input type="email" id="email" name="email" 
                       value="<?php echo htmlspecialchars($contacto_actual['email']); ?>">

                <label for="telefono">Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" 
                       value="<?php echo htmlspecialchars($contacto_actual['telefono']); ?>">
                
                <button type="submit" class="btn-primary-lg">Guardar <?php echo $title; ?></button>
                <a href="clientes_proveedores.php" class="btn-secondary">Cancelar</a>
            </form>
        </section>
    </main>
    </body>
</html>
