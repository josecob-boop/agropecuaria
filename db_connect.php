<?php
// ------------------------------------
$host = 'localhost';      // El servidor de la base de datos (típicamente 'localhost' en XAMPP)
$dbName = 'db_agropecuaria'; // El nombre de la base de datos que creaste
$user = 'root';           // El usuario de la base de datos (típicamente 'root' en XAMPP)
$pass = '';               // La contraseña (típicamente vacía en XAMPP/WAMP por defecto)
$charset = 'utf8mb4';     // Codificación de caracteres

// Definimos el DSN (Data Source Name)
$dsn = "mysql:host=$host;dbname=$dbName;charset=$charset";

// 2. OPCIONES DE PDO (Manejo de Errores y Fetch por defecto)
// ---------------------------------------------------------
$options = [
    // Lanza excepciones en caso de error para un mejor manejo
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    // Deshabilita la emulación de prepared statements (mejor rendimiento y seguridad)
    PDO::ATTR_EMULATE_PREPARES   => false,
    // El modo de retorno de los resultados es por array asociativo por defecto
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

// 3. BLOQUE DE CONEXIÓN
// ---------------------
try {
     // Intenta crear la conexión usando las credenciales y opciones
     $pdo = new PDO($dsn, $user, $pass, $options);
     
     // Si la conexión es exitosa, la variable $pdo contiene el objeto de conexión
     // y estará disponible para tus scripts PHP.

} catch (\PDOException $e) {
     // Si la conexión falla, se captura la excepción y se muestra un mensaje (solo en desarrollo)
     // ⚠️ En producción, NUNCA debes mostrar el error exacto de la base de datos.
     throw new \PDOException($e->getMessage(), (int)$e->getCode());
     
     // Para un manejo más simple, solo puedes mostrar:
     // die("ERROR: No se pudo conectar a la base de datos.");
}

// El script que incluya este archivo ya tendrá acceso al objeto PDO en la variable $pdo
// para ejecutar consultas como: $stmt = $pdo->prepare("SELECT * FROM usuarios");
?>
