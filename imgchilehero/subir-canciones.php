<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos de usuarios
$servername1 = "localhost";
$username1 = "chileher_smuggling";
$password1 = "aweonaoctm2024";
$dbname1 = "chileher_usuariosregistrados";

// Crear conexión
$conn1 = new mysqli($servername1, $username1, $password1, $dbname1);

// Verificar la conexión
if ($conn1->connect_error) {
    die("Conexión fallida: " . $conn1->connect_error);
}

// Consulta SQL para obtener el rol del usuario
$sql = "SELECT rol FROM usuarios WHERE nombre = '$usuario'";
$result = $conn1->query($sql);

$mensaje = "";
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    if ($rolUsuario == "Administrador") {
        $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
    } else {
        $opcionesAdicionales = '';
    }
} else {
    $mensaje = "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$conn1->close();

// Configuración del directorio de destino
$target_dir = "/home/chileher/public_html/img/";
$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
$uploadOk = 1;
$imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

// Verifica si el archivo es una imagen real
if(isset($_POST["submit"])) {
    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
    if($check !== false) {
        $uploadOk = 1;
    } else {
        $mensaje = "El archivo no es una imagen.";
        $uploadOk = 0;
    }
}

// Verifica si el archivo ya existe
if (file_exists($target_file)) {
    $mensaje = "Lo siento, el archivo ya existe.";
    $uploadOk = 0;
}

// Verifica el tamaño del archivo
if ($_FILES["fileToUpload"]["size"] > 500000) {
    $mensaje = "Lo siento, tu archivo es demasiado grande.";
    $uploadOk = 0;
}

// Permitir solo ciertos formatos de archivo
if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif" ) {
    $mensaje = "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
    $uploadOk = 0;
}

// Verifica si $uploadOk es 0 debido a un error
if ($uploadOk == 0) {
    if (empty($mensaje)) {
        $mensaje = "Lo siento, tu archivo no fue subido.";
    }
// Si todo está bien, intenta subir el archivo
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $mensaje = "El archivo ". basename($_FILES["fileToUpload"]["name"]). " ha sido subido.";

        // Conexión a la base de datos de chartsoficiales
        $servername2 = "localhost";
        $username2 = "chileher_smuggling";
        $password2 = "aweonaoctm2024";
        $dbname2 = "chileher_chartsoficiales";

        // Crear conexión
        $conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

        // Verificar la conexión
        if ($conn2->connect_error) {
            die("Conexión fallida: " . $conn2->connect_error);
        }

        // Preparar la consulta SQL
        $album = $conn2->real_escape_string($_POST['album']);
        $artista = $conn2->real_escape_string($_POST['artista']);
        $cancion = $conn2->real_escape_string($_POST['cancion']);
        $descarga_ch = $conn2->real_escape_string($_POST['descarga_ch']);
        $descarga_ghwtde = $conn2->real_escape_string($_POST['descarga_ghwtde']);
        $descarga_rb3 = $conn2->real_escape_string($_POST['descarga_rb3']);
        $sql = "INSERT INTO chartsoficiales (Album, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3)
                VALUES ('$album', '$artista', '$cancion', '$descarga_ch', '$descarga_ghwtde', '$descarga_rb3')";

        if ($conn2->query($sql) === TRUE) {
            $mensaje .= "<br>La información de la canción ha sido registrada con éxito.";
        } else {
            $mensaje .= "<br>Error: " . $sql . "<br>" . $conn2->error;
        }

        $conn2->close();
    } else {
        $mensaje = "Lo siento, hubo un error subiendo tu archivo.";
    }
}
?>

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
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $mensaje; ?>
            </div>
        <?php endif; ?>
        <h1 class="mt-5">Subir Foto y Registrar Canción</h1>
        <form action="upload.php" method="post" enctype="multipart/form-data" class="mx-auto" style="max-width: 600px;">
            <div class="mb-3">
                <label for="album" class="form-label">Album:</label>
                <input type="text" class="form-control" id="album" name="album" required>
            </div>
            <div class="mb-3">
                <label for="artista" class="form-label">Artista:</label>
                <input type="text" class="form-control" id="artista" name="artista" required>
            </div>
            <div class="mb-3">
                <label for="cancion" class="form-label">Canción:</label>
                <input type="text" class="form-control" id="cancion" name="cancion" required>
            </div>
            <div class="mb-3">
                <label for="descarga_ch" class="form-label">Descarga_CH:</label>
                <input type="text" class="form-control" id="descarga_ch" name="descarga_ch" required>
            </div>
            <div class="mb-3">
                <label for="descarga_ghwtde" class="form-label">Descarga_GHWTDE:</label>
                <input type="text" class="form-control" id="descarga_ghwtde" name="descarga_ghwtde" required>
            </div>
            <div class="mb-3">
                <label for="descarga_rb3" class="form-label">Descarga_RB3:</label>
                <input type="text" class="form-control" id="descarga_rb3" name="descarga_rb3" required>
            </div>
            <div class="mb-3">
                <label for="fileToUpload" class="form-label">Selecciona una foto para subir:</label>
                <input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" required>
            </div>
            <button type="submit" class="btn btn-primary" name="submit">Subir Foto y Registrar Canción</button>
        </form>
    </div>
</body>
</html>