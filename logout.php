<?php
/**
 * logout.php
 * Script para cerrar la sesión de usuario de forma segura.
 */

// 1. Iniciar/Reanudar la sesión
session_start();

// 2. Eliminar todas las variables de la sesión
// Esto borra el user_id, user_rol, etc.
session_unset();

// 3. Destruir la sesión por completo
// Esto elimina el archivo de sesión del servidor
session_destroy();

// 4. Redirigir al usuario a la página de acceso público
// Utilizamos index.html como destino final.
header("Location: index.html"); 
exit; // Es crucial detener la ejecución del script después de la redirección
?>
