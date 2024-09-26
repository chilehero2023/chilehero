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

$sql = "SELECT rol, id FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$usuario_id = null;
$accesoAdministrador = false;
$accesoUsuario = false;
$accesoBaneado = false;
$accesoCharter = false;
$opcionesAdicionales = '';
$opcionesAdicionales2 = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    $usuario_id = $row["id"];
    switch ($rolUsuario) {
        case "Usuario":
            $accesoUsuario = true;
            break;
        case "Baneado":
            $accesoBaneado = true;
            header("Location: dashboard.php");
            exit();
            break;
        case "Administrador":
            $accesoAdministrador = true;
            $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
            $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';
            break;
        case "Charter":
            $accesoCharter = true;
            $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';
            break;
    }
} else {
    echo "No se encontraron filas en la consulta SQL.";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Comentarios</title>
</head>
<body>
    <h2>Agregar un Comentario</h2>
    <form method="post" action="add_comment.php">
        <textarea name="comment" placeholder="Escribe tu comentario aquí..." required></textarea><br>
        <input type="hidden" name="parent_id" value="0">
        <input type="submit" value="Agregar Comentario">
    </form>

    <h2>Comentarios</h2>
    <?php
    $stmt = $conn->prepare("SELECT c.*, u.nombre AS usuario FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE parent_id = 0 ORDER BY created_at DESC");
    $stmt->execute();
    $comments = $stmt->get_result();

    while ($comment = $comments->fetch_assoc()) {
        echo "<div>";
        echo "<p><strong>" . htmlspecialchars($comment['usuario']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
        echo "<form method='post' action='add_comment.php'>";
        echo "<textarea name='comment' placeholder='Escribe tu respuesta aquí...' required></textarea><br>";
        echo "<input type='hidden' name='parent_id' value='" . $comment['id'] . "'>";
        echo "<input type='submit' value='Responder'>";
        echo "</form>";

        // Fetch responses
        $stmt2 = $conn->prepare("SELECT c.*, u.nombre AS usuario FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE parent_id = ? ORDER BY created_at ASC");
        $stmt2->bind_param("i", $comment['id']);
        $stmt2->execute();
        $responses = $stmt2->get_result();

        while ($response = $responses->fetch_assoc()) {
            echo "<div style='margin-left: 20px;'>";
            echo "<p><strong>" . htmlspecialchars($response['usuario']) . ":</strong> " . htmlspecialchars($response['comment']) . "</p>";
            echo "</div>";
        }

        echo "</div>";
    }

    $stmt->close();
    $conn->close();
    ?>
</body>
</html>
