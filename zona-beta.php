<?php
// Inicia sesión y verifica si el usuario está autenticado
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos
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

// Verificar el rol del usuario
$accesoAutorizado = false;
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row["rol"] === 'Administrador' || $row["rol"] === 'Tester') {
        $accesoAutorizado = true;
    }
    else{
        header("Location: dashboard.php");
    }
}

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Zona de Administración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
<div class="modal fade" id="baneadoModal" tabindex="-1" aria-labelledby="baneadoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content text-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="baneadoModalLabel">BIENVENID@ TESTER!</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
            <p>En esta zona, se implementará algunas novedades a la página, pero necesito tester y con tu ayuda, podré arreglar o mejores algunas novedades con respecto a la página.</p>
            <p> <font color="red"><b>CUALQUIER SUGERENCIA CON RESPECTO A LAS BETAS, ME LO PUEDES HACER LLEGAR EN INSTAGRAM <a href="https://www.instagram.com/chilehero2023">(ig: chilehero2023)</a> o me lo puedes mandar <a href="mailto:smuggling@chilehero.cl">por correo</a> con asunto: BETAS<p>
            <p> EN CASO CONTRARIO NO SE TOMARÁ EN CUENTA TU SUGERENCIA</b></font></p>
            <p> Se implementará más cosas, cuando este listo la implementación se lanzará al publico</p>
            </div>
        </div>
</div>
</div>
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="https://github.com/chilehero2023/chilehero/blob/main/chilehero_horizontal.png?raw=true" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
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
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?php echo htmlspecialchars($usuario); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="dashboard.php">Ir al Panel</a></li>
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php if ($accesoAutorizado): ?>
                            <li><a class="dropdown-item" href="zona_administracion.php">Zona administración</a></li>
                        <?php endif; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <?php if ($accesoAutorizado): ?>
        <h1>Bienvenido a la zona de Administración, <?php echo htmlspecialchars($usuario); ?>!</h1>
        <p>Selecciona una opción del menú para continuar.</p>
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                    <h5 style="text-align:center" class="card-title">ZONA TESTER</h5>
                    <p style="text-align:center" class="card-text">Aquí puedes ver la zona beta antes de implementarse.</p>
                    <div class="row mt-3">
                        <div class="col">
                            <a href="dashboardv2.php" class="btn btn-primary w-100">Dashboard versión 2</a>
                        </div>
                        <div class="col">
                            <a href="dashboard.php" class="btn btn-danger w-100">Regresar al Dashboard</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var baneadoModal = new bootstrap.Modal(document.getElementById('baneadoModal'));
        baneadoModal.show();
        });

    </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
