<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$accesoAdministrador = false;
$accesoUsuario = false;
$accesoBaneado = false;
$accesoCharter = false;
$opcionesAdicionales = '';
$opcionesAdicionales2 = '';
$opcionesAdicionales3 = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    
    switch ($rolUsuario) {
        case "Usuario":
            $accesoUsuario = true;
            break;
        case "Baneado":
            $accesoBaneado = true;
            break;
        case "Administrador":
            $accesoAdministrador = true;
            $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
            $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';
            break;
        case "Charter":
            $accesoCharter = true;
            $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';
            $opcionesAdicionales3 = '<li><a class="dropdown-item" href="gestionar-canciones.php">Gestionar canciones</a></li>';
            break;
    }
} else {
    $rolUsuario = "Rol no encontrado";
}
$stmt->close();

// Cambiar a la base de datos de encuestas
$dbname = "chileher_encuesta";
$conn->select_db($dbname);

// Consultar encuestas disponibles
$sql = "SELECT * FROM encuestas";
$result = $conn->query($sql);

$encuestas = [];
if ($result->num_rows > 0) {
    $encuestas = $result->fetch_all(MYSQLI_ASSOC);
}

// Consultar opciones de cada encuesta
$opciones = [];
foreach ($encuestas as $encuesta) {
    $sql = "SELECT * FROM opciones WHERE encuesta_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $encuesta['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $opciones[$encuesta['id']] = $result->fetch_all(MYSQLI_ASSOC);
}

// Consultar anuncios disponibles
$sql = "SELECT titulo, texto, prioridad, fecha_creacion FROM anuncios ORDER BY prioridad DESC";
$result = $conn->query($sql);

$anuncios = [];
if ($result->num_rows > 0) {
    $anuncios = $result->fetch_all(MYSQLI_ASSOC);
}

// Manejo de votos
$mensaje = '';

// Crear un array para almacenar las encuestas en las que el usuario ha votado
$encuestasVotadas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['opcion'], $_POST['id_encuesta']) && !empty($_POST['opcion']) && !empty($_POST['id_encuesta'])) {
        // Verificación de voto existente
        $sql = "SELECT COUNT(*) AS votos FROM votos WHERE usuario = ? AND encuesta_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario, $_POST['id_encuesta']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['votos'] == 0) {
            try {
                $stmt = $conn->prepare("INSERT INTO votos (usuario, encuesta_id, opcion_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sii", $usuario, $_POST['id_encuesta'], $_POST['opcion']);
                $stmt->execute();
                $mensaje = "Has votado exitosamente.";
            } catch (mysqli_sql_exception $e) {
                $mensaje = "Error al registrar el voto: " . $e->getMessage();
            }
        } else {
            $mensaje = "Ya has votado en esta encuesta.";
        }
    } else {
        $mensaje = "Datos incompletos.";
    }
}

// Cerrar la conexión
$conn->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <style>
        /* Estilos básicos para el sidebar */
        .sidebar-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #000773; /* Color de fondo */
            color: #fff; /* Color del texto */
            z-index: 1000;
            overflow-y: auto;
            transition: all 0.3s ease;
        }
        .sidebar-menu .logo {
            padding: 15px;
            text-align: center;
        }
        .sidebar-menu .nav {
            padding: 0;
        }
        .sidebar-menu .nav-item {
            margin: 0;
        }
        .sidebar-menu .nav-link {
            color: #fff;
            padding: 10px 20px;
        }
        .sidebar-menu .nav-link:hover {
            background-color: #730000;
        }
        .sidebar-collapse-icon, .sidebar-mobile-menu {
            display: none;
        }
    </style>
</head>
<body class="bg-light text-white">
<?php if ($accesoBaneado): ?>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">
                <img src="https://github.com/chilehero2023/chilehero/blob/main/chilehero_horizontal.png?raw=true" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($usuario); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php echo $opcionesAdicionales; ?>
                        <?php echo $opcionesAdicionales2; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <div class="container mt-5">
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Rol del Usuario</h5>
                        <p class="card-text">Tienes el Rol de: <?php echo htmlspecialchars($rolUsuario); ?></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-danger text-white">
                    <div class="card-body">
                        <h5 class="card-title text-center">¡Estás baneado!</h5>
                        <p class="card-text text-center">
                            <font color="red">INFRINGISTE LAS REGLAS, POR LO TANTO TE ENCUENTRAS RESTRINGIDO.</font><br>
                            Si quieres que te desbaneemos, solo manda un correo a admin@chilehero.cl, especificando tu correo, tu usuario y las razones del porque quieres que te desbaneemos.<br>
                            Puedes revisar tu correo como spam, ahi tienes la razón del porque estás baneado.
                        </p>
                        <div class="text-center">
                            <a href="cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="row">
        <div class="col-3">
            <div class="sidebar-menu">
                <header class="logo-env">
                    <div class="logo">
                        <img src="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true" width="160" class="img-responsive" alt="" />
                    </div>
                    <div class="sidebar-collapse">
                        <a href="#" class="sidebar-collapse-icon with-animation">
                            <i class="entypo-menu"></i>
                        </a>
                    </div>
                    <div class="sidebar-mobile-menu visible-xs">
                        <a href="#" class="with-animation">
                            <i class="entypo-menu"></i>
                        </a>
                    </div>
                </header>
                <div class="sidebar-user-info">
                    <div class="sui-normal">
                        <a href="#" class="user-link">
                            <img src="img/miembro.png" alt="" class="img-circle" width="55"/>					
                            <span>Bienvenid@</span>
                            <strong><?php echo htmlspecialchars($usuario); ?>!</strong>
                        </a>
                    </div>
                </div>
                <nav class="nav flex-column">
                    <a class="nav-link text-white" href="#home">Inicio</a>
                    <a class="nav-link text-white" href="#about">Acerca de</a>
                    <a class="nav-link text-white" href="#services">Servicios</a>
                    <a class="nav-link text-white" href="#contact">Contacto</a>
                </nav>
            </div>
        </div>
        <div class="col-3">
        <a class="navbar-brand" href="#">
            <img src="img/Sin título-1.png" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">
        </a>
    </div>
<?php endif; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
