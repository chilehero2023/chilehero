<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos de chartsoficiales
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_chartsoficiales";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtención del ID de la canción desde la solicitud
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
} else {
    echo json_encode(['error' => 'ID de canción no proporcionado']);
    exit();
}

// Consulta a la base de datos para obtener los detalles de la canción
$sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3, Album, Genero, Ano, Dificultad_Guitarra, Dificultad_Bajo, charter_id
        FROM chartsoficiales
        WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $songDetails = $result->fetch_assoc();

    // Obtener el nombre del charter
    $charter_id = $songDetails['charter_id'];
    if ($charter_id) {
        $sqlCharter = "SELECT nombre AS charter_nombre FROM chileher_usuariosregistrados.Charters WHERE id = ?";
        $stmtCharter = $conn->prepare($sqlCharter);
        $stmtCharter->bind_param("i", $charter_id);
        $stmtCharter->execute();
        $resultCharter = $stmtCharter->get_result();
        if ($resultCharter->num_rows > 0) {
            $charter = $resultCharter->fetch_assoc();
            $songDetails['charter_nombre'] = $charter['charter_nombre'];
        } else {
            $songDetails['charter_nombre'] = 'No disponible';
        }
    } else {
        $songDetails['charter_nombre'] = 'No disponible';
    }

    echo json_encode($songDetails);
} else {
    echo json_encode(['error' => 'Canción no encontrada']);
}

$stmt->close();
$conn->close();
?>
