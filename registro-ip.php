<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

$servernameUsuarios = "localhost";
$usernameUsuarios = "chileher_smuggling";
$passwordUsuarios = "aweonaoctm2024";
$dbnameUsuarios = "chileher_usuariosregistrados";

$connUsuarios = new mysqli($servernameUsuarios, $usernameUsuarios, $passwordUsuarios, $dbnameUsuarios);

if ($connUsuarios->connect_error) {
    die("Conexión fallida a la base de datos de usuarios: " . $connUsuarios->connect_error);
}

$sqlUsuarios = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmtUsuarios = $connUsuarios->prepare($sqlUsuarios);
$stmtUsuarios->bind_param("s", $usuario);
$stmtUsuarios->execute();
$resultUsuarios = $stmtUsuarios->get_result();

$accesoAutorizado = false;
$opcionesAdicionales = '';

if ($resultUsuarios->num_rows > 0) {
    $rowUsuarios = $resultUsuarios->fetch_assoc();
    $rolUsuario = $rowUsuarios["rol"];

    if ($rolUsuario == "Administrador") {
        $accesoAutorizado = true;
        $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
    }
} else {
    echo "No se encontraron filas en la consulta SQL de usuarios: $sqlUsuarios";
    $rolUsuario = "Rol no encontrado";
}

$stmtUsuarios->close();
$connUsuarios->close();

if (!$accesoAutorizado) {
    header("Location: dashboard.php");
    exit();
}

$servernameIP = "localhost";
$usernameIP = "chileher_smuggling";
$passwordIP = "aweonaoctm2024";
$dbnameIP = "chileher_requestip";

$connIP = new mysqli($servernameIP, $usernameIP, $passwordIP, $dbnameIP);

if ($connIP->connect_error) {
    die("Conexión fallida a la base de datos de IPs: " . $connIP->connect_error);
}

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $ip = $_POST['ip'];

    // Verificar si la IP ya está registrada
    $checkQuery = "SELECT * FROM IP WHERE IP = ?";
    $checkStmt = $connIP->prepare($checkQuery);
    $checkStmt->bind_param("s", $ip);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();

    if ($checkResult->num_rows > 0) {
        // Si la IP ya está registrada, mostrar mensaje
        $message = '<center><div class="alert alert-warning" role="alert" style="width: 300px; text-align:center">La IP ya está registrada en la base de datos.</div></center>';
    } else {
        // Si la IP no está registrada, proceder a la inserción
        $query = "INSERT INTO IP (nombre, IP) VALUES (?, ?)";
        $stmt = $connIP->prepare($query);
        $stmt->bind_param("ss", $nombre, $ip);
        $resultado = $stmt->execute();

        if ($resultado) {
            $message = '<center><div class="alert alert-success" role="alert" style="width: 300px; text-align:center">IP ingresada correctamente.</div></center>';
        } else {
            $message = '<center><div class="alert alert-danger" role="alert" style="width: 300px; text-align:center">Error al ingresar la IP: ' . $stmt->error . '</div></center>';
        }

        $stmt->close();
    }

    $checkStmt->close();
}

$connIP->close();
?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Registro IP</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="css/estilos.css" rel="stylesheet">

    <link href="css/background.css" rel="stylesheet">

    <link href="css/fontello.css" rel="stylesheet">

    <link href="css/color.css" rel="stylesheet">

</head>

<body class="bg-light text-white">

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

                        <?php echo $opcionesAdicionales; ?>

                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>

                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav> 

    <h2 class="card-title mb-4" style="text-align:center;">Registro de IP</h2>

    <center><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

        <div class="mb-3">

            <label for="nombre" class="form-label">Nombre:</label>

            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre o nick" style="width: 200px;">

        </div>

        <div class="mb-3">

            <label for="nombre" class="form-label">IP:</label>

            <input type="text" class="form-control" id="ip" name="ip" placeholder="8.8.8.8" style="width: 200px;">

        </div>

        <div class="text-center">

            <button type="submit" class="btn btn-danger">Ingresar la IP</button>

        </div>

        <br>

        <?php echo $message; ?>

    </form></center>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>

