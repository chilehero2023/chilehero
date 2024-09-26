<?php
header('Content-Type: application/json');

// Configuración de la base de datos
$host = 'localhost';
$dbname = 'chileher_chartsoficiales';
$username = 'chileher_smuggling';
$password = 'aweonaoctm2024';

try {
    // Crear conexión a la base de datos
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Consultar la lista de canciones
    $stmt = $pdo->prepare("SELECT ID, Cancion, Artista, imagen_nombre FROM Canciones");
    $stmt->execute();

    // Obtener los resultados
    $songs = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Devolver los resultados en formato JSON
    echo json_encode($songs);
} catch (PDOException $e) {
    // Manejo de errores
    echo json_encode(['error' => 'Error al conectar con la base de datos: ' . $e->getMessage()]);
}
?>
