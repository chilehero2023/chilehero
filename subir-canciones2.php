<?php

// Inicia sesión y verifica si el usuario está autenticado
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
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn1->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$mensaje = "";
$accesoAutorizado = false;

if ($result->num_rows > 0) {
    // Mostrar el rol del usuario si se encontró en la base de datos
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    // Verificar si el usuario tiene Rol de Administrador
    if ($rolUsuario == "Administrador") {
        // Si es Administrador, añadir opciones adicionales
        $accesoAutorizado = true;
    }
} else {
    // Mensaje de depuración
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();
$conn1->close();

if (!$accesoAutorizado) {
    header("Location: dashboard.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $target_dir = "/home/chileher/public_html/imgchilehero/";
    $uploadOk = 1;

    // Check if image file is selected
    if (!empty($_FILES["fileToUpload"]["name"])) {
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if image file is a real image
        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $mensaje = "El archivo no es una imagen.";
            $uploadOk = 0;
        }

        // Check if file already exists
        if (file_exists($target_file)) {
            $mensaje = "Lo siento, el archivo ya existe.";
            $uploadOk = 0;
        }

        // Check file size
        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $mensaje = "Lo siento, tu archivo es demasiado grande.";
            $uploadOk = 0;
        }

        // Allow only certain file formats
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $mensaje = "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
            $uploadOk = 0;
        }

        // Upload file if no errors
        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $mensaje = "El archivo " . basename($_FILES["fileToUpload"]["name"]) . " ha sido subido.";
            } else {
                $mensaje = "Lo siento, hubo un error subiendo tu archivo.";
                $uploadOk = 0;
            }
        }
    }

    // Solo intenta registrar la canción si se proporcionaron los datos necesarios
    if (!empty($_POST['imagen_nombre']) && !empty($_POST['artista']) && !empty($_POST['cancion']) && !empty($_POST['charter'])) {
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
        $sql = "INSERT INTO chartsoficiales (imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3, Charter)
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn2->prepare($sql);
        $stmt->bind_param("sssssss", $imagen_nombre, $artista, $cancion, $descarga_ch, $descarga_ghwtde, $descarga_rb3, $charter);

        $imagen_nombre = $_POST['imagen_nombre'];
        $artista = $_POST['artista'];
        $cancion = $_POST['cancion'];
        $descarga_ch = $_POST['descarga_ch'];
        $descarga_ghwtde = $_POST['descarga_ghwtde'];
        $descarga_rb3 = $_POST['descarga_rb3'];
        $charter = $_POST['charter'];

        if ($stmt->execute()) {
            $mensaje .= "<br>La información de la canción ha sido registrada con éxito.";
        } else {
            $mensaje .= "<br>Error: " . $stmt->error;
        }

        $stmt->close();
        $conn2->close();
    }
}
?>

<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Sube canciones al Chile Hero</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />

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

                        <li><a class="dropdown-item" href="zona-administracion.php">Zona Administración</a></li>

                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>

                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>

                    </ul>

                </li>

            </ul>

        </div>

    </div>

</nav>
    <div class="container mt-4">
        <h2>Subir Canción</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="imagen_nombre">Nombre de la imagen:</label>
                <input type="text" class="form-control" id="imagen_nombre" name="imagen_nombre" required>
            </div>
            <div class="form-group">
                <label for="artista">Artista:</label>
                <input type="text" class="form-control" id="artista" name="artista" required>
            </div>
            <div class="form-group">
                <label for="cancion">Canción:</label>
                <input type="text" class="form-control" id="cancion" name="cancion" required>
            </div>
            <div class="form-group">
                <label for="descarga_ch">Descarga CH:</label>
                <input type="text" class="form-control" id="descarga_ch" name="descarga_ch">
            </div>
            <div class="form-group">
                <label for="descarga_ghwtde">Descarga GHWTDE:</label>
                <input type="text" class="form-control" id="descarga_ghwtde" name="descarga_ghwtde">
            </div>
            <div class="form-group">
                <label for="descarga_rb3">Descarga RB3:</label>
                <input type="text" class="form-control" id="descarga_rb3" name="descarga_rb3">
            </div>
            <div class="form-group">
                <label for="charter">Charter:</label>
                <select class="form-control" id="charter" name="charter" required>
                    <?php
                    // Conexión a la base de datos para obtener los charters
                    $servername = "localhost";
                    $username = "chileher_smuggling";
                    $password = "aweonaoctm2024";
                    $dbname = "chileher_usuariosregistrados";

                    // Crear conexión
                    $conn = new mysqli($servername, $username, $password, $dbname);

                    // Verificar la conexión
                    if ($conn->connect_error) {
                        die("Conexión fallida: " . $conn->connect_error);
                    }

                    // Consulta SQL para obtener los charters
                    $sql = "SELECT id, nombre FROM Charters";
                    $result = $conn->query($sql);

                    // Verificar si se encontraron resultados
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value='" . $row["id"] . "'>" . $row["nombre"] . "</option>";
                        }
                    } else {
                        echo "<option value=''>No hay charters disponibles</option>";
                    }

                    $conn->close();
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="fileToUpload">Imagen a subir:</label>
                <input type="file" class="form-control-file" id="fileToUpload" name="fileToUpload" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Subir Canción</button>
        </form>
        <?php
        if (!empty($mensaje)) {
            echo "<div class='alert alert-info mt-3'>$mensaje</div>";
        }
        ?>
    </div>

    <!-- Scripts necesarios para Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
