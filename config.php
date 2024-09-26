<?php
// Configuración de la base de datos
$host1 = 'sql200.ezyro.com';  // Cambia por el host de tu servidor
$dbname1 = 'ezyro_37346176_usuariosregistrados';        // Cambia por el nombre de tu base de datos
$username1 = 'ezyro_37346176';        // Cambia por tu usuario de la base de datos
$password1 = '102e4c07e';     // Cambia por tu contraseña de la base de datos

try {
    $pdo1 = new PDO("mysql:host=$host1;dbname=$dbname1", $username1, $password1);
    $pdo1->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Error de conexión: ' . $e->getMessage();
}
?>
