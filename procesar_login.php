<?php
// 1. Iniciar la sesión
session_start();

// 2. Incluir el archivo de conexión a la base de datos (db_connect.php)
require 'db_connect.php'; 

// Comprobar que los datos se enviaron por el método POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // 3. Obtener y limpiar los datos del formulario
    $email = $_POST['username'] ?? ''; 
    $password = $_POST['password'] ?? ''; 

    // Validación simple (debes mejorarla con JS en el front-end también)
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Por favor, ingresa tu email y contraseña.";
        header("Location: login.html");
        exit;
    }

    try {
        // 4. Preparar la consulta SQL para obtener el usuario por email
        // Usamos prepared statements para prevenir inyección SQL
        $stmt = $pdo->prepare("SELECT id, nombre, password, rol FROM usuarios WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        // 5. Obtener los datos del usuario
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // 6. Verificar si el usuario existe y si la contraseña es correcta
        if ($usuario && password_verify($password, $usuario['password'])) {
            
            // 7. Login exitoso: Registrar variables de sesión
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['user_name'] = $usuario['nombre'];
            $_SESSION['user_rol'] = $usuario['rol']; // Importante para control de acceso

            // Redirigir al dashboard
            header("Location: dashboard.html");
            exit;

        } else {
            // Login fallido
            $_SESSION['error'] = "Email o contraseña incorrectos.";
            header("Location: login.html");
            exit;
        }

    } catch (PDOException $e) {
        // Manejo de errores de base de datos (solo para desarrollo)
        $_SESSION['error'] = "Error del sistema: " . $e->getMessage();
        header("Location: login.html");
        exit;
    }
} else {
    // Si se intenta acceder directamente sin POST
    header("Location: login.html");
    exit;
}
?>
