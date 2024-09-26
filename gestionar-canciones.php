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

$rolUsuario = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    // Verificar si el usuario tiene rol de Administrador o Charter
    if ($rolUsuario != "Administrador" && $rolUsuario != "Charter") {
        // Si no es Administrador o Charter, redirigir al dashboard
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
}

$stmt->close();

// Conexión a la base de datos de canciones
$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_chartsoficiales";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

// Verificar si se ha enviado el formulario de eliminación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $idEliminar = $_POST['id'];

    // Eliminar la canción de la base de datos
    $sqlEliminar = "DELETE FROM chartsoficiales WHERE ID = ?";
    $stmtEliminar = $conn2->prepare($sqlEliminar);
    $stmtEliminar->bind_param("i", $idEliminar);
    $stmtEliminar->execute();

    // Verificar si la eliminación fue exitosa
    if ($stmtEliminar->affected_rows > 0) {
        echo "<script>alert('Canción eliminada con éxito');</script>";
    } else {
        echo "<script>alert('No se pudo eliminar la canción');</script>";
    }

    $stmtEliminar->close();
}

// Consulta SQL para obtener toda la información de las canciones desde la ID 1
$sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales WHERE ID >= 1 ORDER BY ID";
$result = $conn2->query($sql);

$imagenes = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row;
    }
}

$conn2->close();
?>


<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Gestionar canciones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="css/estilos.css" rel="stylesheet">

    <link href="css/background.css" rel="stylesheet">

    <link href="css/fontello.css" rel="stylesheet">

    <link href="css/color.css" rel="stylesheet">

    <style>

        .btn-group .btn {

            margin-right: 5px; 

        }

        th {

            text-align: center;

        }

        .not-available {

            color: red;

        }

    </style>

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

                        <li><a class="dropdown-item" href="zona-administracion.php">Zona Administración</a></li>

                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>

                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>

                    </ul>

                    </li>

            </ul>

        </div>

    </div>

</nav>

<div class="container mt-5">

    <center><h1>Gestión de canciones</h1></center>

    <p style="text-align:center">¿Qué deseas hacer?</p>

    <table class="table table-dark table-striped table-bordered">

        <thead>

            <tr>

                <th scope="col">ID</th>

                <th scope="col">Album</th>

                <th scope="col">Artista</th>

                <th scope="col">Canción</th>

                <th scope="col"><img src="img/CloneHero.png?raw=true" width="50" height="50"></img></th>

                <th scope="col"><img src="img/wtde_logo.png?raw=true" width="50" height="50"></img></th>

                <th scope="col"><img src="img/Rock_Band_3_Logo.png?raw=true" width="50" height="50"></th>

                <?php if ($rolUsuario == "Administrador" || $rolUsuario == "Charter") echo '<th scope="col">Acciones</th>'; ?>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($imagenes as $imagen): ?>

            <tr>

            <td><?php echo htmlspecialchars($imagen['ID']); ?></td>
            <td><center><img src="imgchilehero/<?php echo htmlspecialchars($imagen['imagen_nombre']); ?>" alt="Imagen" style="max-width: 50px;"></center></td>
            <td><?php echo htmlspecialchars($imagen['Artista']); ?></td>
            <td><?php echo htmlspecialchars($imagen['Cancion']); ?></td>
            <td><?php echo ($imagen['Descarga_CH']) ? "<a href='" . htmlspecialchars($imagen['Descarga_CH']) . "' class='download-link'>Descargar</a>" : "Próximamente"; ?></td>
            <td><?php echo ($imagen['ID'] == 65) ? "<span class='not-available'>No disponible</span>" : (($imagen['Descarga_GHWTDE']) ? "<a href='" . htmlspecialchars($imagen['Descarga_GHWTDE']) . "' class='download-link'>Descargar</a>" : "Próximamente"); ?></td>
            <td><?php echo ($imagen['Descarga_RB3']) ? "<a href='" . htmlspecialchars($imagen['Descarga_RB3']) . "' class='download-link'>Descargar</a>" : "Próximamente"; ?></td>
            <?php if ($rolUsuario == "Administrador" || $rolUsuario == "Charter"): ?>
            <td>
                <form method="post" action="modificar-cancion.php" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($imagen['ID']); ?>">
                    <button type="submit" name="modificar" class="btn btn-primary">Modificar</button>
                </form>
                <?php if ($rolUsuario == "Administrador"): ?>
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?php echo htmlspecialchars($imagen['ID']); ?>">
                    <button type="submit" name="eliminar" class="btn btn-danger">Eliminar</button>
                </form>
                <?php endif; ?>
            </td>
            <?php endif; ?>


            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>

