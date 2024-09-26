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

$sqlContador = "SELECT COUNT(*) AS total_comentarios FROM comments";
$resultContador = $conn->query($sqlContador);
$totalcomentarios = 0;
if ($resultContador->num_rows > 0) {
    $rowContador = $resultContador->fetch_assoc();
    $totalcomentarios = $rowContador["total_comentarios"];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Comentarios</title>
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
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="https://github.com/chilehero2023/chilehero/blob/main/chilehero_horizontal.png?raw=true" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="canciones.php">Canciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pide-tu-cancion.php">Request</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://discord.gg/XMw8ysskdU">Discord</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($usuario); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="dashboard.php">Ir al Panel</a></li>
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php echo $opcionesAdicionales; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
    <div class="container">
        <h2 class="text-center">Agregar un Comentario</h2>
        <p>Aqui podrán hacer sus sugerencias, reclamos o algun comentario, pero de la página. Aquí no aceptaré ningun pedido, el que pida alguna canción se elimina el comentario.</p>
        <form method="post" action="add_comment.php">
            <textarea name="comment" class="form-control" placeholder="Escribe tu comentario aquí..." required></textarea><br>
            <input type="hidden" name="parent_id" value="0">
            <input type="submit" class="btn btn-primary" value="Agregar Comentario">
        </form>

        <h2 class="text-center">Comentarios: <?php echo $totalcomentarios; ?></span></h2>
        <?php
        $stmt = $conn->prepare("SELECT c.*, u.nombre AS usuario FROM comments c JOIN usuarios u ON c.user_id = u.id WHERE parent_id = 0 ORDER BY created_at DESC");
        $stmt->execute();
        $comments = $stmt->get_result();

        while ($comment = $comments->fetch_assoc()) {
            echo "<div>";
            echo "<p><strong>" . htmlspecialchars($comment['usuario']) . ":</strong> " . htmlspecialchars($comment['comment']) . "</p>";
            echo "<form method='post' action='add_comment.php'>";
            echo "<textarea name='comment' class='form-control' placeholder='Escribe tu respuesta aquí...' required></textarea><br>";
            echo "<input type='hidden' name='parent_id' value='" . $comment['id'] . "'>";
            echo "<input type='submit' class='btn btn-primary' value='Responder'>";
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
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
