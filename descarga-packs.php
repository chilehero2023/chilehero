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

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
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
$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Descargas de Packs</title>
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
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php echo $opcionesAdicionales; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
    </nav>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/setlist.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">Setlist Principal</h5>
                        <p class="card-text">Descarga el Setlist principal.<br>
                        <small>71 canciones</small></p></center>
                        <br>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.usercontent.google.com/download?id=1ZUdUlVpy9JApGuGkHAZeLSy4K5PTJjRH&export=download&authuser=0&confirm=t&uuid=13ef604c-311b-4aa8-868e-7eb17c1e9852&at=APZUnTVNvtLkE-6UApGG0PM0JM0V%3A1721331248588" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/cumbias_chilenas.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">Cumbias Chilenas</h5>
                        <p class="card-text">Disfruta de las cumbias chilenas para el Clone Hero.<br>
                        <small>5 canciones</small></p></center>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.usercontent.google.com/download?id=1WEDhciHOMcmG-Ti-jbu0Wt7jCegHUXre&export=download&authuser=0&confirm=t&uuid=706467f4-5938-46c4-a180-b42a1872d3ad&at=APZUnTUPpfzTHybXaG5aJ4_yq8iW%3A1721331811527" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/detodopoco.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">De Todo un Poco</h5>
                        <p class="card-text">En este DLC, tendrás de diferentes géneros.<br>
                        <small>5 canciones</small></p></center>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.usercontent.google.com/download?id=1Cc--0rYZGDh2vzIUXrEmNnGSqX0ske8r&export=download&authuser=0&confirm=t&uuid=e1a312af-c893-4914-ab9a-747d1b7f8195&at=APZUnTXAG6fpLUdBTokbdO5577JS%3A1721331896267" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
        <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/31_Minutos.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">31 Minutos</h5>
                        <p class="card-text">Disfruta de las canciones de 31 Minutos para el Clone Hero.<br>
                        <small>5 canciones</small></p></center>
                        <br>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.usercontent.google.com/download?id=1FG7N9lo0i9g99nh2IDFJDgX54jKapx2q&export=download&authuser=0&confirm=t&uuid=b0f64e58-4f15-4afa-82e1-c2d8a4b92c42&at=APZUnTUghF-KjGZ3j_dHteWHrwIr%3A1721332363514" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/pack02.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">Mega Pack 02</h5>
                        <p class="card-text">Disfruta de una variedad de canciones chilenas.<br>
                        <small>24 canciones</small></p></center>
                        <br>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.usercontent.google.com/download?id=1P2PZgIA9SzqvBIq1RoWLkPdbLqyg5F-Y&export=download&authuser=0&confirm=t&uuid=56c1e300-d9b5-4651-a0e1-c5392bc8d29c&at=APZUnTXqrmqsSmP1g5XTZyXk3FIX%3A1721332636248" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item bg-dark text-white"><img src="imgpacks/losbunkers.png" alt="..." class="img-fluid" width="250px"></li>
                    </ul>
                    <div class="card-body">
                        <center><h5 class="card-title">Los Bunkers</h5>
                        <p class="card-text">Disfruta de las canciones de los bunkers, aunque saldrá otro pack más adelante<br>
                        <small>5 canciones</small></p></center>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="#" class="btn btn-primary">Ver Canciones</a>
                        <a href="https://drive.google.com/file/d/1O5H2SH-JR3U0ljPU12nQ9XGZm6c4GSSb/view?usp=sharing" class="btn btn-danger">Descargar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
