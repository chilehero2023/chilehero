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

    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
}

$stmt->close();
$conn->close();

$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_chartsoficiales";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

$imagenes = [];
$descargas = [];

// Obtener canciones más descargadas en los últimos 7 días
$fechaHace7Dias = date('Y-m-d', strtotime('-7 days'));
$sqlTopDescargas = "SELECT ID, imagen_nombre, Artista, Cancion, Descargas 
                    FROM chartsoficiales 
                    WHERE Fecha_Descarga >= '$fechaHace7Dias'
                    ORDER BY Descargas DESC 
                    LIMIT 5"; // Limitar a 5 canciones

$resultTopDescargas = $conn2->query($sqlTopDescargas);

if (!$resultTopDescargas) {
    die("Error en la consulta: " . $conn2->error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['buscar'])) {
    $filtroArtista = $_GET['artista'];
    $filtroCancion = $_GET['cancion'];

    // Construir la consulta SQL con los filtros aplicados
    $sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales WHERE 1=1";
    
    $paramTypes = "";
    $params = [];

    if (!empty($filtroArtista)) {
        $sql .= " AND Artista LIKE ?";
        $paramTypes .= "s";
        $params[] = "%$filtroArtista%";
    }
    
    if (!empty($filtroCancion)) {
        $sql .= " AND Cancion LIKE ?";
        $paramTypes .= "s";
        $params[] = "%$filtroCancion%";
    }
    
    $sql .= " ORDER BY Artista";

    $stmt = $conn2->prepare($sql);
    
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn2->error);
    }
    
    if (!empty($paramTypes)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagenes[] = $row;
        }
    } else {
        
    }
    $stmt->close();
} else {
    // Si no se envió el formulario de búsqueda, cargar todas las canciones sin filtros
    $sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales ORDER BY Artista";
    $result = $conn2->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn2->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagenes[] = $row;
        }
    }
}

$sqlContador = "SELECT COUNT(*) AS total_canciones FROM chartsoficiales";
$resultContador = $conn2->query($sqlContador);
$totalcanciones = 0;
if ($resultContador->num_rows > 0) {
    $rowContador = $resultContador->fetch_assoc();
    $totalcanciones = $rowContador["total_canciones"];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Lista de canciones</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
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
        .canciones-count {
            font-family: Digital_Dismay;
        }
    </style>
    <script>
        function registrarDescarga(idCancion, plataforma) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "registrar_descarga.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // Actualizar el contador de descargas en la interfaz (opcional)
                    // ...
                }
            };
            xhr.send("id=" + idCancion + "&plataforma=" + plataforma);
        }
    </script>
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
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <h1 class="text-white text-center mb-4">Canciones Disponibles: <span class="canciones-count"> <?php echo $totalcanciones; ?></span></h1>

    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="GET" class="mb-4">
        <div class="row justify-content-center">
            <div class="col-md-4 mx-auto">
                <input type="text" name="artista" class="form-control" placeholder="Buscar por Artista">
            </div>
            <div class="col-md-4 mx-auto">
                <input type="text" name="cancion" class="form-control" placeholder="Buscar por Canción">
            </div>
            <div class="col-md-2 mx-auto">
                <button type="submit" name="buscar" class="btn btn-primary w-100">Buscar</button>
            </div>
        </div>
    </form>

    <h2 class="text-white text-center mt-5 mb-3">Top 5 Canciones Más Descargadas (Últimos 7 Días)</h2>
    <div class="card bg-dark text-white p-4 mb-4" style="max-width: 800px; margin: 0 auto;">
        <?php
        $hayDescargas = false; // Variable para controlar si hay descargas
        while ($rowTop = $resultTopDescargas->fetch_assoc()) {
            $hayDescargas = true; // Si hay al menos una canción, cambiamos el valor a true
            ?>
            <div class="list-group-item list-group-item-dark d-flex justify-content-between align-items-center">
                <div class="d-flex align-items-center">
                    <img src="imgchilehero/<?php echo $rowTop['imagen_nombre']; ?>" alt="<?php echo $rowTop['Cancion']; ?>" width="40" class="me-3">
                    <div>
                        <h5 class="mb-1"><?php echo $rowTop['Cancion']; ?></h5>
                        <p class="mb-0">Artista: <?php echo $rowTop['Artista']; ?></p>
                    </div>
                </div>
                <span class="badge bg-primary rounded-pill"><?php echo $rowTop['Descargas']; ?> Descargas</span>
            </div>
        <?php } 

        // Si no hubo descargas, mostramos el mensaje
        if (!$hayDescargas) {
            echo '<p class="text-center">No se han descargado las canciones.</p>';
        }
        ?>
    </div>

    <div class="table-responsive">
        <table class="table table-dark table-striped table-bordered text-center">
            <thead>
                <tr>
                    <th scope="col">Album</th>
                    <th scope="col">Artista</th>
                    <th scope="col">Canción</th>
                    <th scope="col"><img src="img/CloneHero.png?raw=true" width="50" height="50"></th>
                    <th scope="col"><img src="img/wtde_logo.png?raw=true" width="50" height="50"></th>
                    <th scope="col"><img src="img/Rock_Band_3_Logo.png?raw=true" width="50" height="50"></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($imagenes)): ?>
                    <?php foreach ($imagenes as $imagen): ?>
                        <tr>
                            <td><img src="imgchilehero/<?php echo $imagen['imagen_nombre']; ?>" class="img-fluid" width="40"></td>
                            <td><?php echo $imagen['Artista']; ?></td>
                            <td><?php echo $imagen['Cancion']; ?></td>
                            <td>
                                <?php if ($imagen['Descarga_CH']): ?>
                                    <a href="<?php echo $imagen['Descarga_CH']; ?>" onclick="registrarDescarga(<?php echo $imagen['ID']; ?>, 'CH')">Descargar CH</a>
                                <?php else: ?>
                                    <span class="not-available">No disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($imagen['Descarga_GHWTDE']): ?>
                                    <a href="<?php echo $imagen['Descarga_GHWTDE']; ?>" onclick="registrarDescarga(<?php echo $imagen['ID']; ?>, 'GHWTDE')">Descargar GHWTDE</a>
                                <?php else: ?>
                                    <span class="not-available">No disponible</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($imagen['Descarga_RB3']): ?>
                                    <a href="<?php echo $imagen['Descarga_RB3']; ?>" onclick="registrarDescarga(<?php echo $imagen['ID']; ?>, 'RB3')">Descargar RB3</a>
                                <?php else: ?>
                                    <span class="not-available">No disponible</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6">No se encontraron canciones.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
