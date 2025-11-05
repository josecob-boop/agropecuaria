<?php
// procesar_contacto.php - Maneja INSERT, UPDATE y DELETE para clientes y proveedores
session_start();
require 'db_connect.php'; // Incluye tu conexión PDO

// 1. CONTROL DE ACCESO
if (!isset($_SESSION['user_id'])) {
    header("Location: login.html");
    exit;
}

// Inicializar variables de entrada
$action = filter_input(INPUT_POST, 'action', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_GET, 'action', FILTER_SANITIZE_STRING);
$type = filter_input(INPUT_POST, 'type', FILTER_SANITIZE_STRING) ?? filter_input(INPUT_GET, 'type', FILTER_SANITIZE_STRING);
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// 2. VALIDACIÓN BÁSICA DEL TIPO
if (!in_array($type, ['cliente', 'proveedor'])) {
    $_SESSION['error_message'] = "Tipo de contacto no válido.";
    header("Location: clientes_proveedores.php");
    exit;
}

// Asignar tabla y mensaje base
$table = ($type === 'cliente') ? 'clientes' : 'proveedores';
$title = ($type === 'cliente') ? 'Cliente' : 'Proveedor';

try {
    switch ($action) {
        // ======================================
        // A. ACCIÓN ELIMINAR (DELETE)
        // ======================================
        case 'delete':
            if (!$id) {
                throw new Exception("ID de {$title} no especificado para eliminar.");
            }
            
            // Eliminar usando Prepared Statement
            $sql = "DELETE FROM {$table} WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([':id' => $id]);
            
            $_SESSION['success_message'] = "{$title} eliminado exitosamente.";
            break;

        // ======================================
        // B. ACCIÓN CREAR/ACTUALIZAR (INSERT/UPDATE)
        // ======================================
        case 'create':
        case 'update':
            // 1. Obtener y sanear datos comunes
            $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
            $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);

            if (empty($nombre)) {
                throw new Exception("El nombre es obligatorio.");
            }

            // 2. Manejar datos específicos del tipo
            if ($type === 'cliente') {
                $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
                $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
                
                if ($action === 'create') {
                    $sql = "INSERT INTO clientes (nombre, apellido, telefono, email, direccion) 
                            VALUES (:nombre, :apellido, :telefono, :email, :direccion)";
                    $params = [
                        ':nombre' => $nombre, ':apellido' => $apellido, 
                        ':telefono' => $telefono, ':email' => $email, 
                        ':direccion' => $direccion
                    ];
                } else { // update
                    if (!$id) { throw new Exception("ID de Cliente no válido."); }
                    $sql = "UPDATE clientes SET nombre = :nombre, apellido = :apellido, 
                            telefono = :telefono, email = :email, direccion = :direccion 
                            WHERE id = :id";
                    $params = [
                        ':nombre' => $nombre, ':apellido' => $apellido, 
                        ':telefono' => $telefono, ':email' => $email, 
                        ':direccion' => $direccion, ':id' => $id
                    ];
                }
            } else { // proveedor
                $contacto = filter_input(INPUT_POST, 'contacto', FILTER_SANITIZE_STRING);
                
                if ($action === 'create') {
                    $sql = "INSERT INTO proveedores (nombre, contacto, telefono, email) 
                            VALUES (:nombre, :contacto, :telefono, :email)";
                    $params = [
                        ':nombre' => $nombre, ':contacto' => $contacto, 
                        ':telefono' => $telefono, ':email' => $email
                    ];
                } else { // update
                    if (!$id) { throw new Exception("ID de Proveedor no válido."); }
                    $sql = "UPDATE proveedores SET nombre = :nombre, contacto = :contacto, 
                            telefono = :telefono, email = :email 
                            WHERE id = :id";
                    $params = [
                        ':nombre' => $nombre, ':contacto' => $contacto, 
                        ':telefono' => $telefono, ':email' => $email, ':id' => $id
                    ];
                }
            }

            $stmt = $pdo->prepare($sql);
            $stmt->execute($params);

            $_SESSION['success_message'] = "{$title} " . ($action === 'create' ? 'creado' : 'actualizado') . " exitosamente.";
            break;

        default:
            throw new Exception("Acción no válida.");
    }

} catch (Exception $e) {
    // 4. MANEJO DE ERROR
    $_SESSION['error_message'] = "Operación fallida: " . $e->getMessage();
}

// 5. REDIRECCIÓN FINAL a la página de listado
header("Location: clientes_proveedores.php");
exit;
?>
