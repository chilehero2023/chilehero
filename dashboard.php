<?php

session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";

// Conectar a la base de datos de usuarios
$dbname = "chileher_usuariosregistrados";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar el rol del usuario y la fecha de vencimiento si aplica
$sql = "SELECT rol, fecha_vencimiento_vip FROM usuarios WHERE nombre = ?";
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
$opcionesAdicionales4 = '';
$fechaVencimiento = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    $fechaVencimiento = $row["fecha_vencimiento_vip"];
    
    switch ($rolUsuario) {
        case "Usuario":
            $accesoUsuario = true;
            break;
        case "Tester":
            $accesoUsuario = true;
            $opcionesAdicionales4 = '<li><a class="dropdown-item" href="zona-beta.php">Zona beta</a></li>';
            break;
        case "Baneado":
            $accesoBaneado = true;
            break;
        case "Administrador":
            $accesoAdministrador = true;
            $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
            $opcionesAdicionales4 = '<li><a class="dropdown-item" href="zona-beta.php">Zona beta</a></li>';
            break;
        case "Charter":
            $accesoCharter = true;
            $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';
            $opcionesAdicionales3 = '<li><a class="dropdown-item" href="gestionar-canciones.php">Gestionar canciones</a></li>';
            break;
        case "Donador":
        case "Superusuario":
        case "VIP":
            $accesoUsuario = true;
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
        // ... (verificación de voto existente)

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

                <img src="logos/chilehero_horizontal2.png" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">

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

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">

            <div class="container-fluid">

                <a class="navbar-brand" href="#">

                    <img src="logos/chilehero_horizontal2.png" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">

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

                                <a class="nav-link" href="https://discord.gg/XMw8ysskdU">Discord</a>

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

                                    <?php echo $opcionesAdicionales3; ?>
                                    <?php echo $opcionesAdicionales4; ?>

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

                        <h5>20/08</h5>

                        <p>- Se añadieron las siguientes canciones: <br>

                        &emsp;- <b>Los Bunkers - Las Cosas que Cambie y Deje por Ti</b><br>

                        &emsp;- <b>Los Bunkers - Nada Nuevo Bajo el Sol</b><br>

                        &emsp;- <b>Los Bunkers - Quien Fuera</b><br>

                        &emsp;- <b>Los Bunkers - Rey</b><br>

                        &emsp;- <b>Los Bunkers - Ven Aquí</b><br>

                        - Además se actualizó el apartado de packs, en donde ya incluí el link de descarga para los bunkers.

                        </p>

                        <p><font color="red">Aquí dejo de publicar contenido, hasta la fecha anunciada. Sin embargo si quieren seguir al tanto pueden ir al instagram pinchando <a href="https://www.instagram.com/chilehero2023">acá</a></font></p>

                        <h5>03/08</h5>

                        <p>- Se añadió la siguiente canción: <br>

                        &emsp;- <b>Feeling Every Sunset - Heartless</b></p>

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
                        <p class="card-text">
                            Tienes el Rol de: 
                            <?php echo htmlspecialchars($rolUsuario); ?>
                            
                            <?php if ($rolUsuario === 'VIP'): ?>
                                <img src="img/diamante_vip.png" alt="VIP" style="max-width: 35px; height: auto; margin-left: 5px; vertical-align: middle;">
                            <?php endif; ?>
                            <?php if ($rolUsuario === 'Superusuario'): ?>
                                <img src="img/diamante_superusuario.png" alt="VIP" style="max-width: 35px; height: auto; margin-left: 5px; vertical-align: middle;">
                            <?php endif; ?>
                            <?php if ($rolUsuario === 'Usuario'): ?>
                                <img src="img/miembro.png" alt="VIP" style="max-width: 35px; height: auto; margin-left: 5px; vertical-align: middle;">
                            <?php endif; ?>
                        </p>

                        <?php if (in_array($rolUsuario, ['Donador', 'Superusuario', 'VIP'])): ?>
                            <p class="card-text">Fecha de vencimiento: <?php echo $fechaVencimiento ? htmlspecialchars($fechaVencimiento) : 'Sin fecha definida'; ?></p>
                        <?php endif; ?>

                        </div>

                    </div>

                </div>

            </div>



        <!-- Sección de encuestas -->

        <div class="row mt-3">

            <div class="col-md-12">

                <div class="card bg-dark text-white mb-3">

                    <div class="card-body">

                        <h5 class="card-title">Anuncios</h5>

                        <?php foreach ($anuncios as $anuncio): ?>

                            <div class="card bg-secondary text-white mb-3">

                                <div class="card-body">

                                    <?php

                                    // Verificar si la prioridad es "Urgente" y establecer el estilo en consecuencia

                                    if ($anuncio['prioridad'] === 'urgente') {

                                        $tituloEstilo = 'color: darkred;';

                                    } else {

                                        $tituloEstilo = '';

                                    }

                                    ?>

                                    <h5 class="card-title" style="<?php echo $tituloEstilo; ?>">

                                        <?php echo htmlspecialchars($anuncio['titulo']); ?>

                                    </h5>

                                    <p class="card-text"><?php echo htmlspecialchars($anuncio['texto']); ?><br>

                                    <small>Publicado el: <?php echo htmlspecialchars(date('d-m-Y', strtotime($anuncio['fecha_creacion']))); ?></small></p>

                                </div>

                            </div>

                        <?php endforeach; ?>

                    </div>

                </div>

            </div>

        </div>



            <div class="row mt-3">

                <?php if (!empty($encuestas)): ?>

                    <?php foreach ($encuestas as $encuesta): ?>

                        <div class="col-md-12">

                            <div class="card bg-dark text-white">

                                <div class="card-body">

                                    <h5 class="card-title"><?php echo htmlspecialchars($encuesta['pregunta']); ?></h5>

                                    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">

                                        <input type="hidden" name="id_encuesta" value="<?php echo htmlspecialchars($encuesta['id']); ?>">

                                        <?php foreach ($opciones[$encuesta['id']] as $opcion): ?>

                                            <div class="form-check">

                                                <input class="form-check-input" type="radio" name="opcion" id="opcion<?php echo htmlspecialchars($opcion['id']); ?>" value="<?php echo htmlspecialchars($opcion['id']); ?>">

                                                <label class="form-check-label" for="opcion<?php echo htmlspecialchars($opcion['id']); ?>">

                                                    <?php echo htmlspecialchars($opcion['opcion']); ?>

                                                </label>

                                            </div>

                                        <?php endforeach; ?>

                                        <button type="submit" class="btn btn-primary mt-2">Votar</button>

                                        <a href="encuesta.php?id=<?php echo htmlspecialchars($encuesta['id']); ?>" class="btn btn-info mt-2">Ver votos</a>

                                    </form>

                                    <?php if (isset($mensaje) && $mensaje !== ''): ?>

                                        <div class="alert alert-info mt-2">

                                            <?php echo htmlspecialchars($mensaje); ?>

                                        </div>

                                    <?php endif; ?>

                                </div>

                            </div>

                        </div>

                    <?php endforeach; ?>

                <?php else: ?>

                    <div class="col-md-12">

                        <div class="card bg-dark text-white">

                            <div class="card-body">

                                <p>No hay encuestas disponibles.</p>

                            </div>

                        </div>

                </div>

                <?php endif; ?>

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

<?php endif; ?>

    

