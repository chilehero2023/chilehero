<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Datos de la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_encuesta";

// Conectar a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Validar datos recibidos
if (!isset($_POST['opcion']) || !isset($_POST['id_encuesta']) || empty($_POST['opcion']) || empty($_POST['id_encuesta'])) {
    echo "Datos incompletos.";
    $conn->close();
    exit();
}

// Verificar si el usuario ya ha votado en la encuesta
$sql = "SELECT COUNT(*) as votos FROM votos WHERE usuario = ? AND encuesta_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $usuario, $_POST['id_encuesta']);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

if ($row['votos'] > 0) {
    echo "Ya has votado en esta encuesta.";
    $stmt->close();
    $conn->close();
    exit();
}

// Insertar el voto en la base de datos
$sql = "INSERT INTO votos (usuario, encuesta_id, opcion_id) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sii", $usuario, $_POST['id_encuesta'], $_POST['opcion']);

if ($stmt->execute()) {
    echo "Voto registrado exitosamente.";
} else {
    echo "Error al registrar el voto: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
