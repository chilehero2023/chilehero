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
$fechaVencimiento = null;

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    $fechaVencimiento = $row["fecha_vencimiento_vip"];
    
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6oIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
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
                        <?php if (in_array($rolUsuario, ['Donador', 'Superusuario', 'VIP'])): ?>
                            <p class="card-text">Fecha de vencimiento: <?php echo $fechaVencimiento ? htmlspecialchars($fechaVencimiento) : 'Sin fecha definida'; ?></p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Encuestas Disponibles</h5>
                        <?php if (!empty($encuestas)): ?>
                            <?php foreach ($encuestas as $encuesta): ?>
                                <form method="post" action="">
                                    <div class="mb-3">
                                        <label for="encuesta-<?php echo $encuesta['id']; ?>" class="form-label"><?php echo htmlspecialchars($encuesta['titulo']); ?></label>
                                        <select class="form-select" id="encuesta-<?php echo $encuesta['id']; ?>" name="opcion">
                                            <?php foreach ($opciones[$encuesta['id']] as $opcion): ?>
                                                <option value="<?php echo $opcion['id']; ?>"><?php echo htmlspecialchars($opcion['texto']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                    <input type="hidden" name="id_encuesta" value="<?php echo $encuesta['id']; ?>">
                                    <button type="submit" class="btn btn-primary">Votar</button>
                                </form>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p>No hay encuestas disponibles en este momento.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card bg-dark text-white">
                    <div class="card-body">
                        <h5 class="card-title">Anuncios</h5>
                        <?php if (!empty($anuncios)): ?>
                            <ul class="list-group list-group-flush">
                                <?php foreach ($anuncios as $anuncio): ?>
                                    <li class="list-group-item bg-dark text-white">
                                        <strong><?php echo htmlspecialchars($anuncio['titulo']); ?>:</strong> <?php echo htmlspecialchars($anuncio['texto']); ?>
                                        <br><small>Prioridad: <?php echo htmlspecialchars($anuncio['prioridad']); ?> | Fecha: <?php echo htmlspecialchars($anuncio['fecha_creacion']); ?></small>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <p>No hay anuncios disponibles en este momento.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php endif; ?>

</body>
</html>
