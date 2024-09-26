<?php
session_start();

// Verificar si el usuario está autenticado y tiene rol de Administrador
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";

// Conectar a la base de datos de usuarios para verificar el rol
$conn = new mysqli($servername, $username, $password, "chileher_usuariosregistrados");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['rol'] !== 'Administrador') {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();

// Conectar a la base de datos de encuestas para eliminar el anuncio
$conn = new mysqli($servername, $username, $password, "chileher_encuesta");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Verificar si el anuncio existe
    $sql = "SELECT * FROM anuncios WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        // Eliminar el anuncio
        $sql = "DELETE FROM anuncios WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        
        if ($stmt->affected_rows > 0) {
            echo "Anuncio eliminado exitosamente.";
        } else {
            echo "Error al eliminar el anuncio. Filas afectadas: " . $stmt->affected_rows;
        }
    } else {
        echo "Anuncio no encontrado.";
    }

    $stmt->close();
} else {
    echo "ID de anuncio no recibido.";
}

$conn->close();
?>
