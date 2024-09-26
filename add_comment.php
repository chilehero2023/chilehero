<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT id FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $user_id = $row["id"];
} else {
    echo "No se encontró el usuario.";
    exit();
}

$stmt->close();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $comment = $_POST['comment'];
    $parent_id = $_POST['parent_id'];

    $stmt = $conn->prepare("INSERT INTO comments (user_id, comment, parent_id) VALUES (?, ?, ?)");
    $stmt->bind_param("isi", $user_id, $comment, $parent_id);
    $stmt->execute();

    $stmt->close();
    $conn->close();

    header("Location: comentarios.php");
}
?>
