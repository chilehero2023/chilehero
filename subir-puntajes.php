<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";

// Conectar a la base de datos de usuarios
$conn_user = new mysqli($servername, $username, $password, "chileher_usuariosregistrados");

if ($conn_user->connect_error) {
    die("Conexión fallida a usuarios: " . $conn_user->connect_error);
}

// Iniciar la sesión
session_start();
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario desde la sesión
$nombre_usuario = $_SESSION['usuario'];

// Verificar si el usuario existe y obtener su rol
$sql_check_user = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt_check_user = $conn_user->prepare($sql_check_user);
$stmt_check_user->bind_param("s", $nombre_usuario);
$stmt_check_user->execute();
$stmt_check_user->store_result();

if ($stmt_check_user->num_rows === 0) {
    die("El usuario no existe.");
}

$stmt_check_user->bind_result($user_role);
$stmt_check_user->fetch();
$stmt_check_user->close();

// Verificar si el usuario está baneado
if ($user_role == 'Baneado') {
    header("Location: dashboard.php");
    exit();
}

// Conectar a la base de datos de chartsoficiales
$conn_chart = new mysqli($servername, $username, $password, "chileher_chartsoficiales");

if ($conn_chart->connect_error) {
    die("Error de conexión a chartsoficiales: " . $conn_chart->connect_error);
}

// Obtener la lista de canciones
$sql_canciones = "SELECT ID, Cancion FROM chartsoficiales";
$result_canciones = $conn_chart->query($sql_canciones);

$canciones = [];
if ($result_canciones->num_rows > 0) {
    while ($row = $result_canciones->fetch_assoc()) {
        $canciones[] = $row;
    }
}
$result_canciones->free();

// Procesar el formulario cuando se envía
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["submit"])) {
    $target_dir = "/home/chileher/public_html/puntuaciones/";
    $uploadOk = 1;

    if (!empty($_FILES["fileToUpload"]["name"])) {
        $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $mensaje = "El archivo no es una imagen.";
            $uploadOk = 0;
        }

        if (file_exists($target_file)) {
            $mensaje = "Lo siento, el archivo ya existe.";
            $uploadOk = 0;
        }

        if ($_FILES["fileToUpload"]["size"] > 500000) {
            $mensaje = "Lo siento, tu archivo es demasiado grande.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $mensaje = "Lo siento, solo se permiten archivos JPG, JPEG, PNG y GIF.";
            $uploadOk = 0;
        }

        if ($uploadOk == 1) {
            if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
                $mensaje = "El archivo " . basename($_FILES["fileToUpload"]["name"]) . " ha sido subido.";
                $captura = basename($_FILES["fileToUpload"]["name"]);

                // Consultar el nombre de la canción desde la base de datos de chartsoficiales
                $cancion_id = $_POST["cancion_id"];
                $sql_cancion = "SELECT Cancion FROM chartsoficiales WHERE ID = ?";
                $stmt_cancion = $conn_chart->prepare($sql_cancion);
                $stmt_cancion->bind_param("i", $cancion_id);
                $stmt_cancion->execute();
                $stmt_cancion->bind_result($cancion_nombre);
                $stmt_cancion->fetch();
                $stmt_cancion->close();

                // Insertar el puntaje en la tabla ranking
                $puntuacion = $_POST["puntuacion"];
                $porcentaje = $_POST["porcentaje"];
                $fecha_puntuacion = date("Y-m-d H:i:s");

                // Depuración: Imprimir la fecha
                error_log("Fecha de puntuación: $fecha_puntuacion");

                // Conectar a la base de datos de ranking
                $conn_ranking = new mysqli($servername, $username, $password, "chileher_usuariosregistrados");

                if ($conn_ranking->connect_error) {
                    die("Conexión fallida a ranking: " . $conn_ranking->connect_error);
                }

                $sql = "INSERT INTO ranking (cancion, usuario_nombre, puntuacion, porcentaje, fecha_puntuacion, captura, estado) VALUES (?, ?, ?, ?, ?, ?, 'En revisión')";
                $stmt = $conn_ranking->prepare($sql);
                $stmt->bind_param("ssidds", $cancion_nombre, $nombre_usuario, $puntuacion, $porcentaje, $fecha_puntuacion, $captura);

                if ($stmt->execute()) {
                    $mensajeConfirmacion = "Se ha enviado tu puntaje correctamente, puedes revisar tu puntaje en tu perfil si se aprobó o no.";
                } else {
                    $mensaje .= " Error al guardar los datos: " . $stmt->error;
                }

                $stmt->close();
                $conn_ranking->close();
            } else {
                $mensaje = "Lo siento, hubo un error subiendo tu archivo.";
                $uploadOk = 0;
            }
        }
    } else {
        $mensaje = "No se ha seleccionado ningún archivo para subir.";
    }
}
error_log("Fecha de puntuación generada: $fecha_puntuacion");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subir Puntaje</title>
</head>
<body>
    <h1>Subir Puntaje</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <label for="fileToUpload">Subir imagen:</label>
        <input type="file" name="fileToUpload" id="fileToUpload" required><br><br>

        <label for="cancion_id">Seleccionar canción:</label>
        <select name="cancion_id" id="cancion_id" required>
            <option value="">Seleccione una canción</option>
            <?php foreach ($canciones as $cancion): ?>
                <option value="<?php echo htmlspecialchars($cancion['ID']); ?>">
                    <?php echo htmlspecialchars($cancion['Cancion']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="puntuacion">Puntuación:</label>
        <input type="text" name="puntuacion" id="puntuacion" required><br><br>

        <label for="porcentaje">Porcentaje:</label>
        <input type="number" name="porcentaje" id="porcentaje" min="0" max="100" required><br><br>

        <input type="submit" name="submit" value="Enviar Puntaje">
    </form>

    <?php
    if (isset($mensaje)) {
        echo "<p>$mensaje</p>";
    }

    if (isset($mensajeConfirmacion)) {
        echo "<p>$mensajeConfirmacion</p>";
    }
    ?>
</body>
</html>
