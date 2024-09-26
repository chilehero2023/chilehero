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
    <title>Panel de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <script type="text/javascript">window.$crisp=[];window.CRISP_WEBSITE_ID="8176b813-b376-40f6-9197-b4c7e6f615ad";(function(){d=document;s=d.createElement("script");s.src="https://client.crisp.chat/l.js";s.async=1;d.getElementsByTagName("head")[0].appendChild(s);})();</script>
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
                            <p class="card-text">Tienes el Rol de: <?php echo $rolUsuario; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card bg-danger text-white">
                        <div class="card-body">
                            <h5 style="text-align:center" class="card-title">¡Estás baneado!</h5>
                            <p style="text-align:center" class="card-text">
                                <font color="red">INFRINGISTE LAS REGLAS, POR LO TANTO TE ENCUENTRAS RESTRINGIDO.</font><br>
                                Si quieres que te desbaneemos, solo manda un correo a admin@chilehero.cl, especificando tu correo, tu usuario y las razones del porque quieres que te desbaneemos.<br>
                                Puedes revisar tu correo como spam, ahi tienes la razón del porque estás baneado.
                            </p>
                            <center><a href="cerrar_sesion.php" class="btn btn-danger">Cerrar Sesión</a></center>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php else: ?>
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
                        <?php if ($accesoUsuario || $accesoAdministrador): ?>
                            <li class="nav-item">
                                <a class="nav-link" href="canciones.php">Canciones</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="pide-tu-cancion.php">Request</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#">Soporte</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo htmlspecialchars($usuario); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                <?php if ($accesoUsuario || $accesoAdministrador || $accesoCharter): ?>
                                    <li><a class="dropdown-item" href="dashboard.php">Ir al Panel</a></li>
                                    <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                                    <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                                    <?php echo $opcionesAdicionales; ?>
                                    <?php echo $opcionesAdicionales2; ?>
                                <?php endif; ?>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="modal fade" id="baneadoModal" tabindex="-1" aria-labelledby="baneadoModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content text-dark">
                    <div class="modal-header">
                        <h5 class="modal-title" id="baneadoModalLabel">Novedades</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h5>24/07</h5>
                        <p>- Se añadió una página para que puedas comentar. <b>Siempre con respeto</b>
                        <h5>19/07</h5>
                        <p>- Se arregló el buscador, ya que al momento de buscar mandaba error 500.<br>
                        - Se añadió la opción "Packs", donde podrás descargar los packs que estaban en la antigua página.</p>
                        <h5>18/07</h5>
                        <p>- Se arregló el apartado de canciones, ya que habia un error que no permitia la descargas<br>
                        - Se añadió un buscador en el apartado de canciones, este buscador sirve para los artistas y/o canciones</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="container mt-5">
            <h1>Bienvenido, <?php echo htmlspecialchars($usuario); ?>!</h1>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="card bg-dark text-white mt-3">
                        <div class="card-body">
                            <h5 class="card-title">Rol del Usuario</h5>
                            <p class="card-text">Tienes el Rol de: <?php echo $rolUsuario; ?></p>
                        </div>
                    </div>
                </div>
            </div>
            <p>Selecciona una opción del menú para continuar.</p>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Subir Canciones</h5>
                            <p class="card-text">Haz clic aquí para subir canciones.</p>
                            <a href="sube-tu-chart.php" class="btn btn-primary">Subir</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Descargar Canciones</h5>
                            <p class="card-text">Haz clic aquí para descargar canciones.</p>
                            <a href="canciones.php" class="btn btn-primary">Descargar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pedir Canciones</h5>
                            <p class="card-text">Haz clic aquí para pedir canciones.</p>
                            <a href="pide-tu-cancion.php" class="btn btn-primary">Pedir</a>
                        </div>
                    </div>
                </div>
            </div>
            <br>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Revisar el estado de tus chart</h5>
                            <p class="card-text">Esto es solo para revisar los chart tus charts y de las demás personas.</p>
                            <br>
                            <a href="revisa-solicitud.php" class="btn btn-primary">Revisar mi estado</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Revisar el estado de tu solicitud de canción</h5>
                            <p class="card-text">Esto es solo tu solicitud y de las canciones que pidieron los demás.</p>
                            <a href="revisa-tu-cancion.php" class="btn btn-primary">Revisar mi solicitud</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Packs</h5>
                            <p class="card-text">Aquí podrás descargar los packs correspondientes, asi se te hará más fácil de descargar las canciones</p>
                            <a href="descarga-packs.php" class="btn btn-primary">Descarga de Packs</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Comentarios</h5>
                            <p class="card-text">Aquí podrás comentar, sugerir o incluso hacer algun reclamo. <font color="red">PERO SIEMPRE CON RESPETO</p>
                            <br>
                            <a href="comentarios.php" class="btn btn-primary">Comentar</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="display:none">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Revisar el estado de tu solicitud de canción</h5>
                            <p class="card-text">Esto es solo tu solicitud y de las canciones que pidieron los demás.</p>
                            <a href="revisa-tu-cancion.php" class="btn btn-primary">Revisar mi solicitud</a>
                        </div>
                    </div>
                </div>
                <div class="col-md-4" style="display:none">
                    <div class="card bg-dark text-white">
                        <div class="card-body">
                            <h5 class="card-title">Packs</h5>
                            <p class="card-text">Aquí podrás descargar los packs correspondientes, asi se te hará más fácil de descargar las canciones</p>
                            <a href="descarga-packs.php" class="btn btn-primary">Descarga de Packs</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var baneadoModal = new bootstrap.Modal(document.getElementById('baneadoModal'));
            baneadoModal.show();
        });
    </script>
</body>
</html>