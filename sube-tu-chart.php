<?php

// Inicia sesión y verifica si el usuario está autenticado
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$estado = "En progreso";

// Conexión a la base de datos para obtener el rol del usuario
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consulta SQL para obtener el rol del usuario
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$rolUsuario = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    // Redirigir al dashboard si el usuario es "Baneado"
    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }

    // Verificar si el usuario tiene Rol de Administrador
    if ($rolUsuario == "Administrador") {
        // Si es Administrador, añadir opciones adicionales
        $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
    } else {
        // Si no es Administrador, no añadir opciones adicionales
        $opcionesAdicionales = '';
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();
$conn->close();

// Variables para el mensaje de confirmación
$mensaje = "";

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username_form = $_POST['username'];
    $fileLink = $_POST['fileLink'];

    // Conexión a la base de datos de canciones subidas
    $dbname_canciones = "chileher_cancionessubidas";
    $conn = new mysqli($servername, $username, $password, $dbname_canciones);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO chartssubidos (nombre, link, estado) VALUES (?, ?, ?)";
    $stmtInsert = $conn->prepare($sql);
    $stmtInsert->bind_param("sss", $username_form, $fileLink, $estado);

    if ($stmtInsert->execute() === TRUE) {
        $mensaje = "Chart enviado correctamente. Se te enviará un correo en caso de que se te haya aceptado o rechazado tu chart";
    } else {
        $mensaje = "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmtInsert->close();
    $conn->close();
}
?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Sube tus charts</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="css/estilos.css" rel="stylesheet">

    <link href="css/background.css" rel="stylesheet">

    <link href="css/fontello.css" rel="stylesheet">

    <link href="css/color.css" rel="stylesheet">

    <script>

        document.addEventListener('contextmenu', function (e) {

            e.preventDefault();

        });

    </script>

</head>

<body class="text-white">

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

    <div class="container mt-5">

        <h1 class="text-center">Sube tu chart</h1>

        <?php if (!empty($mensaje)): ?>

            <div class="alert alert-success" role="alert">

                <?php echo $mensaje; ?>

            </div>

        <?php endif; ?>

        <center><form method="POST" action="">

            <div class="mb-3">

                <label for="username" class="form-label">Nombre de Usuario (Recuerda que tiene que ser la de tu correo)</label>

                <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su nombre de usuario" required style="width: 400px;">

            </div>

            <div class="mb-3">

                <label for="fileLink" class="form-label">Link de Archivo</label>

                <input type="text" class="form-control" id="fileLink" name="fileLink" placeholder="Ingrese el link del archivo" required style="width: 400px;">

                <p><font color="red">Tiene que ser un link valido, de lo contrario, no se tomará en cuenta y será eliminado de la base de datos<font></p>

            </div>

            <button type="submit" class="btn btn-primary">Enviar</button>

        </form></center>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>