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

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    $usuario_id = $row["id"];
    if ($rolUsuario === "Administrador") {
        $accesoAdministrador = true;
    } else {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL.";
    exit();
}

$stmt->close();

// Eliminar comentario si el usuario es administrador
if ($accesoAdministrador && isset($_GET['delete_comment_id'])) {
    $comment_id = $_GET['delete_comment_id'];
    $stmt = $conn->prepare("DELETE FROM comments WHERE id = ? OR parent_id = ?");
    $stmt->bind_param("ii", $comment_id, $comment_id);
    if ($stmt->execute()) {
        header("Location: gestionar_comentarios.php");
        exit();
    } else {
        echo "Error al eliminar el comentario.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Gestionar Comentarios</title>
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        body {
            color: white; /* Texto blanco */
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="text-center">Comentarios</h2>
        <?php
        $stmt = $conn->prepare("SELECT c.*, u.nombre AS usuario FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE parent_id = 0 ORDER BY created_at DESC");
        $stmt->execute();
        $comments = $stmt->get_result();

        while ($comment = $comments->fetch_assoc()) {
            echo "<div class='comment'>";
            echo "<p><strong>" . htmlspecialchars($comment['usuario']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";

            echo "<form method='get' action='' style='display:inline;'>";
            echo "<input type='hidden' name='delete_comment_id' value='" . $comment['id'] . "'>";
            echo "<input type='submit' class='btn btn-danger delete-button' value='Eliminar'>";
            echo "</form>";

            // Fetch responses
            $stmt2 = $conn->prepare("SELECT c.*, u.nombre AS usuario FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE parent_id = ? ORDER BY created_at ASC");
            $stmt2->bind_param("i", $comment['id']);
            $stmt2->execute();
            $responses = $stmt2->get_result();

            while ($response = $responses->fetch_assoc()) {
                echo "<div class='response'>";
                echo "<p><strong>" . htmlspecialchars($response['usuario']) . ":</strong> " . htmlspecialchars($response['comment']) . "</p>";
                echo "<form method='get' action='' style='display:inline;'>";
                echo "<input type='hidden' name='delete_comment_id' value='" . $response['id'] . "'>";
                echo "<input type='submit' class='btn btn-danger delete-button' value='Eliminar'>";
                echo "</form>";
                echo "</div>";
            }

            echo "</div>";
        }

        $stmt->close();
        $conn->close();
        ?>
    </div>
</body>
</html>
