<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos de usuarios registrados
$servername1 = "localhost";
$username1 = "chileher_smuggling";
$password1 = "aweonaoctm2024";
$dbname1 = "chileher_usuariosregistrados";

$conn1 = new mysqli($servername1, $username1, $password1, $dbname1);

// Verificar la conexión
if ($conn1->connect_error) {
    die("Conexión fallida: " . $conn1->connect_error);
}

// Consulta SQL para verificar si el usuario está baneado
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$rolUsuario = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    // Redirigir al dashboard si el usuario está "Baneado"
    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }
}

// Conexión a la base de datos de solicitudes
$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_request";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

// Verificar la conexión
if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

// Manejo de eliminación de solicitudes
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['eliminar'])) {
    $idEliminar = $_POST['id'];

    // Eliminar la solicitud de la base de datos
    $sqlEliminar = "DELETE FROM request WHERE ID = ?";
    $stmtEliminar = $conn2->prepare($sqlEliminar);
    $stmtEliminar->bind_param("i", $idEliminar);
    $stmtEliminar->execute();

    // Mensaje de éxito o error
    if ($stmtEliminar->affected_rows > 0) {
        echo "<script>alert('Solicitud eliminada con éxito');</script>";
    } else {
        echo "<script>alert('No se pudo eliminar la solicitud');</script>";
    }

    $stmtEliminar->close();
}

// Consulta SQL para obtener los datos de la tabla Request
$sql2 = "SELECT id, Artista, cancion, Nombre AS 'Pedido por', estado, Mensaje FROM request ORDER BY ID";
$result2 = $conn2->query($sql2);

// Almacenar los resultados en una variable para su uso posterior
$tableData = "";

if ($result2->num_rows > 0) {
    $tableData .= "<div class='container mt-4'>";
    $tableData .= "<div class='row justify-content-center'>";
    $tableData .= "<div class='col-auto'>";
    $tableData .= "<table class='table table-dark table-bordered table-striped'>";
    $tableData .= "<thead class='thead-dark'><tr>";

    if ($rolUsuario == "Administrador") {
        $tableData .= "<th>ID</th>";
    }

    $tableData .= "<th>Artista</th><th>Canción</th><th>Pedido por</th><th>Estado</th>";

    if ($rolUsuario == "Administrador") {
        $tableData .= "<th>Mensaje</th><th>Acciones</th>";
    }

    $tableData .= "</tr></thead>";
    $tableData .= "<tbody>";

    while ($row2 = $result2->fetch_assoc()) {
        $tableData .= "<tr>";

        if ($rolUsuario == "Administrador") {
            $tableData .= "<td>" . htmlspecialchars($row2["id"]) . "</td>";
            
        }

        $tableData .= "<td>" . htmlspecialchars($row2["Artista"]) . "</td>";
        $tableData .= "<td>" . htmlspecialchars($row2["cancion"]) . "</td>";
        $tableData .= "<td>" . htmlspecialchars($row2["Pedido por"]) . "</td>";
        $tableData .= "<td>" . htmlspecialchars($row2["estado"]) . "</td>";

        if ($rolUsuario == "Administrador") {
            $mensaje = $row2["Mensaje"];
            if (is_null($mensaje) || trim($mensaje) === "") {
                $mensaje = "<span style='color: red;'>Sin mensaje</span>";
            } else {
                $mensaje = htmlspecialchars($mensaje);
            }
            $tableData .= "<td>" . $mensaje . "</td>";
            $tableData .= "<td>";
            $tableData .= "<a href='modificar-estado.php?id=" . $row2["id"] . "' class='btn btn-primary btn-sm me-2'>Modificar</a>";
            $tableData .= "<form action='" . htmlspecialchars($_SERVER["PHP_SELF"]) . "' method='post' style='display:inline;'>";
            $tableData .= "<input type='hidden' name='id' value='" . $row2["id"] . "'>";
            $tableData .= "<button type='submit' name='eliminar' class='btn btn-danger btn-sm'>Eliminar</button>";
            $tableData .= "</form>";
            $tableData .= "</td>";
        }

        $tableData .= "</tr>";
    }

    $tableData .= "</tbody></table>";
    $tableData .= "</div></div></div>";
} else {
    $tableData = "<div class='container mt-4'><p>No se encontraron resultados.</p></div>";
}

// Contar solicitudes
$sqlContador = "SELECT COUNT(*) AS total_solicitudes FROM request WHERE estado='En Cola'";
$resultContador = $conn2->query($sqlContador);
$totalSolicitudes = 0;

if ($resultContador->num_rows > 0) {
    $rowContador = $resultContador->fetch_assoc();
    $totalSolicitudes = $rowContador["total_solicitudes"];
}

$sqlContador2 = "SELECT COUNT(*) AS total_solicitudes2 FROM request WHERE estado='En Progreso'";
$resultContador2 = $conn2->query($sqlContador2);
$totalSolicitudes2 = 0;

if ($resultContador2->num_rows > 0) {
    $rowContador2 = $resultContador2->fetch_assoc();
    $totalSolicitudes2 = $rowContador2["total_solicitudes2"];
}

$sqlContador3 = "SELECT COUNT(*) AS total_solicitudes3 FROM request WHERE estado='Listo'";
$resultContador3 = $conn2->query($sqlContador3);
$totalSolicitudes3 = 0;

if ($resultContador3->num_rows > 0) {
    $rowContador3 = $resultContador3->fetch_assoc();
    $totalSolicitudes3 = $rowContador3["total_solicitudes3"];
}

$conn1->close();
$conn2->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Revisa tu canción</title>
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
                            <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                            <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                            <?php echo $opcionesAdicionales; ?>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <center><h2>Listado de Solicitudes</h2></center>
    <div class="container mt-4">
        <center><h5>Canciones en cola: <?php echo $totalSolicitudes; ?></h5></center>
        <center><h5>Canciones en progreso: <?php echo $totalSolicitudes2; ?></h5></center>
        <center><h5>Canciones Listas: <?php echo $totalSolicitudes3; ?></h5></center>
    </div>
    <?php echo $tableData; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
