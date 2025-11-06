<?php
// agregar_usuario_temp.php
require 'db_connect.php'; // Incluye la conexión a tu base de datos

// 1. Define el usuario y la contraseña en texto plano
$nombre = 'Nuevo Administrador';
$email = 'admin@agro.com';
$password_plano = 'adminpass'; // Cambia esto por una contraseña real
$rol = 'administrador'; 

// 2. Generar el Hash de la Contraseña (CLAVE DE SEGURIDAD)
$password_hash = password_hash($password_plano, PASSWORD_DEFAULT);

try {
    // 3. Sentencia preparada para insertar el usuario seguro
    $sql = "INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre' => $nombre,
        ':email' => $email,
        ':password' => $password_hash,
        ':rol' => $rol
    ]);

    echo "✅ Usuario '{$nombre}' agregado exitosamente con el rol: {$rol}";
    
} catch (\PDOException $e) {
    echo "❌ Error al agregar usuario: " . $e->getMessage();
}
?>
