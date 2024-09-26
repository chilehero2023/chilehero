<?php
// Inicia sesión y verifica si el usuario está autenticado
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos para verificar el rol del usuario y si está baneado
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbnameUsuarios = "chileher_usuariosregistrados";
$dbnameCanciones = "chileher_cancionessubidas";

// Crear conexión para la base de datos de usuarios registrados
$connUsuarios = new mysqli($servername, $username, $password, $dbnameUsuarios);

// Verificar la conexión a la base de datos de usuarios registrados
if ($connUsuarios->connect_error) {
    die("Conexión fallida a la base de datos de usuarios: " . $connUsuarios->connect_error);
}

// Consulta SQL para verificar si el usuario está baneado y obtener su rol
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $connUsuarios->prepare($sql);
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
        $opcionesAdicionales = '<li><a class="dropdown-item" href="modificar_chart.php">Modificar Charts</a></li>';
    } else {
        // Si no es Administrador, no añadir opciones adicionales
        $opcionesAdicionales = '';
    }
} else {
    // Mensaje de depuración
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();
$connUsuarios->close();

// Variables para el mensaje de confirmación
$mensaje = "";

// Procesar el formulario si se ha enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username_form = $_POST['username'];
    $fileLink = $_POST['fileLink'];

    // Crear conexión para la base de datos de canciones subidas
    $connCanciones = new mysqli($servername, $username, $password, $dbnameCanciones);

    // Verificar la conexión
    if ($connCanciones->connect_error) {
        die("Conexión fallida: " . $connCanciones->connect_error);
    }

    // Insertar los datos en la base de datos
    $sql = "INSERT INTO chartssubidos (nombre, link) VALUES (?, ?)";
    $stmtInsert = $connCanciones->prepare($sql);
    $stmtInsert->bind_param("ss", $username_form, $fileLink);

    if ($stmtInsert->execute() === TRUE) {
        $mensaje = "Chart enviado correctamente. Se te enviará un correo en caso de que se te haya aceptado o rechazado tu chart";
    } else {
        $mensaje = "Error: " . $stmtInsert->error;
    }

    $stmtInsert->close();
    $connCanciones->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Revisa tu solicitud de chart</title>
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
                        <?php echo $opcionesAdicionales; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <center><h2>Datos de Canciones Subidas por los demás usuarios (NO OFICIALES)</h2></center>
    <table class="table table-dark table-striped table-bordered">
        <thead>
            <tr>
                <th scope="col">Link</th>
                <th scope="col">Usuario quien lo subió</th>
                <th scope="col">Estado</th>
                <?php if ($rolUsuario == "Administrador"): ?>
                    <th scope="col">Acción</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            // Conexión a la base de datos para obtener los datos de canciones subidas
            $conn = new mysqli($servername, $username, $password, $dbnameCanciones);
            // Verificar la conexión
            if ($conn->connect_error) {
                die("Conexión fallida: " . $conn->connect_error);
            }
            // Consulta SQL para obtener los datos de canciones subidas
            $sql = "SELECT id, link, nombre, estado FROM chartssubidos";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                // Mostrar los datos en la tabla
                while($row = $result->fetch_assoc()) {
                    $estadoClass = "";
                    if ($row["estado"] == "Rechazado") {
                        $estadoClass = "text-danger";
                    } elseif ($row["estado"] == "Aceptado") {
                        $estadoClass = "text-success";
                    }
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($row["link"]) . "</td>";
                    echo "<td>" . htmlspecialchars($row["nombre"]) . "</td>";
                    echo "<td class='$estadoClass'>" . htmlspecialchars($row["estado"]) . "</td>";

                    if ($rolUsuario == "Administrador") {
                        echo "<td><a href='modificar_chart.php?id=" . htmlspecialchars($row["id"]) . "' class='btn btn-warning'>Modificar</a></td>";
                    }

                    echo "</tr>";
                }
            } else {
                echo '<tr><td colspan="4" class="text-center">No se encontraron datos</td></tr>';
            }
            $conn->close();
            ?>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
