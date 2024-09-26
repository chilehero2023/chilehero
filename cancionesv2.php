<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos de usuarios
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

// Conexión a la base de datos de chartsoficiales
$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_chartsoficiales";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

// Consulta a la base de datos de chartsoficiales
$imagenes = [];
$sql = "SELECT chartsoficiales.ID, chartsoficiales.imagen_nombre, chartsoficiales.Artista, chartsoficiales.Cancion, chartsoficiales.Descarga_CH, chartsoficiales.Descarga_GHWTDE, chartsoficiales.Descarga_RB3, chartsoficiales.Album, chartsoficiales.Genero, chartsoficiales.Ano, chartsoficiales.Dificultad_Guitarra, chartsoficiales.Dificultad_Bajo
        FROM chartsoficiales
        ORDER BY chartsoficiales.Artista, chartsoficiales.Cancion";
$result = $conn2->query($sql);

if (!$result) {
    die("Error en la consulta: " . $conn2->error);
}

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $imagenes[] = $row;
    }
}

$sqlContador = "SELECT COUNT(*) AS total_canciones FROM chartsoficiales";
$resultContador = $conn2->query($sqlContador);
$totalcanciones = 0;
if ($resultContador->num_rows > 0) {
    $rowContador = $resultContador->fetch_assoc();
    $totalcanciones = $rowContador["total_canciones"];
}

$conn2->close();
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
        @font-face {
            font-family: 'Lato';
            src: url('font/Lato-Regular.ttf') format('truetype');
            font-weight: normal;
        }

        @font-face {
            font-family: 'Lato';
            src: url('font/Lato-Bold.ttf') format('truetype');
            font-weight: bold;
        }

        @font-face {
            font-family: 'Lato';
            src: url('font/Lato-Black.ttf') format('truetype');
            font-weight: 900;
        }

        .song-list {
            list-style-type: none;
            padding: 0;
        }

        .artist-group {
            margin-bottom: 20px;
        }

        body {
            font-family: Lato;
        }

        .artist-name {
            font-size: 1.5em;
            font-weight: bold;
            font-family: 'Lato', sans-serif;
            margin-bottom: 10px;
        }

        .song-item {
            cursor: pointer;
            padding: 10px;
            border-bottom: 1px solid #ddd;
            color: white;
            transition: color 0.3s ease;
            display: flex;
            align-items: center;
        }

        .song-item:hover {
            color: black;
            background-color: #f8f9fa;
        }

        .album-preview {
            position: fixed;
            top: 10%;
            right: 5%;
            width: 300px;
            height: 300px;
            object-fit: cover;
            border: 2px solid #ddd;
            display: none;
            z-index: 1000;
        }

        .info-container {
            display: none;
            position: fixed;
            top: 60%;
            right: 5%;
            width: 300px;
            background: rgba(0, 0, 0, 0.8); 
            color: white;
            padding: 10px;
            border: 2px solid #ddd;
            z-index: 1000;
            border-radius: 5px;
        }

        .info-container p {
            margin: 5px 0;
        }

        .info-container .info-title {
            font-weight: bold;
        }

        .info-container .info-value {
            color: white;
        }

        .modal-body {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .modal-body a {
            display: inline-block;
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
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container">
    <h1 class="text-center mb-4">Lista de Canciones (<?php echo $totalcanciones; ?>)</h1>
    <?php
    $currentArtist = "";
    $count = 0;
    foreach ($imagenes as $img) {
        if ($currentArtist != $img["Artista"]) {
            if ($count > 0) echo '</ul></div>';
            $currentArtist = $img["Artista"];
            echo '<div class="artist-group">';
            echo '<div class="artist-name">' . htmlspecialchars($currentArtist) . '</div>';
            echo '<ul class="song-list">';
            $count = 0;
        }
        echo '<li class="song-item" data-id="' . htmlspecialchars($img["ID"]) . '"
                data-imagen="' . htmlspecialchars($img["imagen_nombre"]) . '"
                data-album="' . htmlspecialchars($img["Album"]) . '"
                data-genero="' . htmlspecialchars($img["Genero"]) . '"
                data-ano="' . htmlspecialchars($img["Ano"]) . '"
                data-dificultad-guitarra="' . htmlspecialchars($img["Dificultad_Guitarra"]) . '"
                data-dificultad-bajo="' . htmlspecialchars($img["Dificultad_Bajo"]) . '"
                data-descarga-ch="' . htmlspecialchars($img["Descarga_CH"]) . '"
                data-descarga-ghwtd="' . htmlspecialchars($img["Descarga_GHWTDE"]) . '"
                data-descarga-rb3="' . htmlspecialchars($img["Descarga_RB3"]) . '">
                <span>' . htmlspecialchars($img["Cancion"]) . '</span>
            </li>';
        $count++;
    }
    if ($count > 0) echo '</ul></div>';
    ?>
</div>

<!-- Modal -->
<div class="modal fade" id="modalDescargas" tabindex="-1" aria-labelledby="modalDescargasLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalDescargasLabel">Enlaces de Descarga</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Aquí se mostrarán los enlaces de descarga -->
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js" integrity="sha384-oBqDVmMz4fnFO9jYy0jLJ4OCpXxXo6H0B5EIBXYRFOA68SEh5E4E6yI6dB6G05aF" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-rbsA+9x4fXZokWvK/zWbUTROwJrFuZ8aOgKn5mF23MLzEfAlxh6xu3A07Asbx92T" crossorigin="anonymous"></script>
<script>
    const songItems = document.querySelectorAll('.song-item');
    const infoContainer = document.querySelector('.info-container');
    const albumPreview = document.querySelector('.album-preview');
    const modalBody = document.querySelector('.modal-body');

    songItems.forEach(item => {
        item.addEventListener('click', function() {
            const imagen = this.getAttribute('data-imagen');
            const album = this.getAttribute('data-album');
            const genero = this.getAttribute('data-genero');
            const ano = this.getAttribute('data-ano');
            const dificultadGuitarra = this.getAttribute('data-dificultad-guitarra');
            const dificultadBajo = this.getAttribute('data-dificultad-bajo');
            const descargaCH = this.getAttribute('data-descarga-ch');
            const descargaGHWTDE = this.getAttribute('data-descarga-ghwtd');
            const descargaRB3 = this.getAttribute('data-descarga-rb3');

            albumPreview.src = 'imgchilehero/' + imagen;
            albumPreview.style.display = 'block';
            infoContainer.style.display = 'block';
            infoContainer.innerHTML = `
                <p class="info-title">Álbum:</p><p class="info-value">${album}</p>
                <p class="info-title">Género:</p><p class="info-value">${genero}</p>
                <p class="info-title">Año:</p><p class="info-value">${ano}</p>
                <p class="info-title">Dificultad Guitarra:</p><p class="info-value">${dificultadGuitarra}</p>
                <p class="info-title">Dificultad Bajo:</p><p class="info-value">${dificultadBajo}</p>
            `;

            // Actualizar el contenido del modal
            modalBody.innerHTML = '';
            if (descargaCH) modalBody.innerHTML += `<a href="${descargaCH}" class="btn btn-primary me-2">Descarga CH</a>`;
            if (descargaGHWTDE) modalBody.innerHTML += `<a href="${descargaGHWTDE}" class="btn btn-primary me-2">Descarga GHWTDE</a>`;
            if (descargaRB3) modalBody.innerHTML += `<a href="${descargaRB3}" class="btn btn-primary me-2">Descarga RB3</a>`;

            // Mostrar el modal
            const modal = new bootstrap.Modal(document.getElementById('modalDescargas'));
            modal.show();
        });
    });

    document.querySelectorAll('.song-item').forEach(item => {
        item.addEventListener('mouseover', function() {
            const imagen = this.getAttribute('data-imagen');
            const album = this.getAttribute('data-album');
            const genero = this.getAttribute('data-genero');
            const ano = this.getAttribute('data-ano');
            const dificultadGuitarra = this.getAttribute('data-dificultad-guitarra');
            const dificultadBajo = this.getAttribute('data-dificultad-bajo');

            albumPreview.src = 'imgchilehero/' + imagen;
            albumPreview.style.display = 'block';
            infoContainer.style.display = 'block';
            infoContainer.innerHTML = `
                <p class="info-title">Álbum:</p><p class="info-value">${album}</p>
                <p class="info-title">Género:</p><p class="info-value">${genero}</p>
                <p class="info-title">Año:</p><p class="info-value">${ano}</p>
                <p class="info-title">Dificultad Guitarra:</p><p class="info-value">${dificultadGuitarra}</p>
                <p class="info-title">Dificultad Bajo:</p><p class="info-value">${dificultadBajo}</p>
            `;
        });

        item.addEventListener('mouseout', function() {
            infoContainer.style.display = 'none';
        });
    });
</script>
</body>
</html>
