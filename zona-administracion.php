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
    if ($row["rol"] === 'Administrador') {
        $accesoAutorizado = true;
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

        <!-- Contenido adicional para administradores -->
        <div class="row mt-5">
            <div class="col-md-12">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 style="text-align:center" class="card-title">Panel Administrativo</h5>
                        <p style="text-align:center" class="card-text">Aquí puedes gestionar las opciones administrativas.</p>
                        
                        <div class="row mt-3">

                            <div class="col">

                                <a href="subir-canciones.php" class="btn btn-primary w-100">Subir canciones</a>

                            </div>

                            <div class="col">

                                <a href="gestionar-canciones.php" class="btn btn-primary w-100">Gestionar canciones</a>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col">

                                <a href="registro-ip.php" class="btn btn-primary w-100">Registrar IP</a>

                            </div>

                            <div class="col">

                                <a href="gestionar-ip.php" class="btn btn-primary w-100">Gestionar IP</a>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col">

                                <a href="gestionar-usuarios.php" class="btn btn-primary w-100">Gestionar Usuarios</a>

                            </div>

                            <div class="col">

                                <a href="gestionar_comentarios.php" class="btn btn-primary w-100">Gestionar Comentarios</a>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col">

                                <a href="crear_encuesta.php" class="btn btn-primary w-100">Crear Encuestas</a>

                            </div>

                            <div class="col">

                                <a href="gestionar-encuestas.php" class="btn btn-primary w-100">Gestionar Encuestas</a>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col">

                                <a href="crear-anuncios.php" class="btn btn-primary w-100">Crear Anuncios</a>

                            </div>

                            <div class="col">

                                <a href="gestionar-anuncios.php" class="btn btn-primary w-100">Gestionar Anuncios</a>

                            </div>

                        </div>

                        <div class="row mt-3">

                            <div class="col">

                                <a href="dashboard.php" class="btn btn-danger w-100">Regresar al Dashboard</a>

                            </div>

                            <div class="col">

                                <a href="https://chilehero.cl:2083/" class="btn btn-danger w-100">Ir al Panel General</a>

                            </div>

                        </div>
                        <!-- Más opciones de administración... -->
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 style="text-align:center" class="card-title">No eres Administrador</h5>
                        <p style="text-align:center" class="card-text">Esta zona es para acceso al personal, regresa al panel de usuario.</p>
                        <center><a href="dashboard.php" class="btn btn-danger">Ir al Dashboard</a></center>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
