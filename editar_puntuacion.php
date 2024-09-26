<?php
session_start();

// Verifica si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

// Crear conexión para la base de datos de usuarios registrados
$connUsuarios = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($connUsuarios->connect_error) {
    die("Conexión fallida a la base de datos de usuarios: " . $connUsuarios->connect_error);
}

// Consultar el rol del usuario
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $connUsuarios->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$rolUsuario = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }

    if ($rolUsuario != "Administrador") {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();
$connUsuarios->close();

$connRanking = new mysqli($servername, $username, $password, "chileher_usuariosregistrados");

if ($connRanking->connect_error) {
    die("Conexión fallida a la base de datos de ranking: " . $connRanking->connect_error);
}

// Procesar el formulario de modificación si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['estado']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $nuevo_estado = $_POST['estado'];

    // Mensaje de depuración
    echo "<p>Estado enviado: " . htmlspecialchars($nuevo_estado) . "</p>";

    if (in_array($nuevo_estado, ['Rechazado', 'Aprobado', 'En revisión'])) {
        // Actualiza el estado en la tabla ranking
        $sql_update = "UPDATE ranking SET estado = ? WHERE id = ?";
        $stmtUpdate = $connRanking->prepare($sql_update);
        $stmtUpdate->bind_param("si", $nuevo_estado, $id);

        if ($stmtUpdate->execute()) {
            $mensaje = "Estado actualizado con éxito.";
            $mensaje_clase = "alert alert-success";
        } else {
            $mensaje = "Error al actualizar el estado: " . $stmtUpdate->error;
            $mensaje_clase = "alert alert-danger";
        }
        $stmtUpdate->close();
    } else {
        $mensaje = "Estado no válido.";
        $mensaje_clase = "alert alert-danger";
    }
}

// Obtener el ID del chart desde la URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Obtener el estado actual del chart para prellenar el formulario
$estadoActual = '';
if ($id > 0) {
    $sql = "SELECT estado FROM ranking WHERE id = ?";
    $stmtSelect = $connRanking->prepare($sql);
    $stmtSelect->bind_param("i", $id);
    $stmtSelect->execute();
    $result = $stmtSelect->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $estadoActual = $row['estado'];
    } else {
        echo "Puntuación no encontrada.";
        exit();
    }

    $stmtSelect->close();
}

$connRanking->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Modificar Estado de Puntuación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-dark">
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
                    <a class="nav-link" href="#">Soporte</a>
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
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <center>
        <h2>Modificar Estado de la Puntuación</h2>

        <?php if (isset($mensaje) && $mensaje != ""): ?>
            <div class="<?php echo htmlspecialchars($mensaje_clase); ?>" role="alert">
                <?php echo htmlspecialchars($mensaje); ?>
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($id); ?>">
            <div class="mb-3">
                <label for="estado" class="form-label">Estado</label>
                <select class="form-select" id="estado" name="estado" style="width: 200px" required>
                    <option value="En revisión" <?php echo $estadoActual == 'En revisión' ? 'selected' : ''; ?>>En revisión</option>
                    <option value="Rechazado" <?php echo $estadoActual == 'Rechazado' ? 'selected' : ''; ?>>Rechazado</option>
                    <option value="Aprobado" <?php echo $estadoActual == 'Aprobado' ? 'selected' : ''; ?>>Aprobado</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Actualizar Estado</button>
        </form>
    </center>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
