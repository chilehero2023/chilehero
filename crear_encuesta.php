<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit(); // Detener la ejecución del script
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos de usuarios
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Preparar y ejecutar una consulta para obtener el rol del usuario
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

// Verificar si se encontró al usuario y si tiene el rol de Administrador
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row['rol'];

    if ($rolUsuario != "Administrador") {
        header("Location: dashboard.php");
        exit(); // Detener la ejecución del script
    }
} else {
    // No se encontró el usuario
    header("Location: login.php");
    exit(); // Detener la ejecución del script
}

// Cerrar la consulta y la conexión a la base de datos de usuarios
$stmt->close();
$conn->close();

// Conectar a la base de datos de encuestas
$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_encuesta";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

// Verificar si la conexión fue exitosa
if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $pregunta = $_POST['pregunta'];
    $opciones = $_POST['opciones'];

    // Insertar pregunta en la base de datos
    $sql = "INSERT INTO encuestas (pregunta) VALUES (?)";
    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("s", $pregunta);
    $stmt->execute();
    $encuesta_id = $stmt->insert_id;

    // Insertar opciones en la base de datos
    foreach ($opciones as $opcion) {
        $sql = "INSERT INTO opciones (encuesta_id, opcion) VALUES (?, ?)";
        $stmt = $conn2->prepare($sql);
        $stmt->bind_param("is", $encuesta_id, $opcion);
        $stmt->execute();
    }

    // Establecer mensaje de éxito en la sesión
    $_SESSION['mensaje'] = "Encuesta creada";
    
    // Redirigir a la misma página para mostrar el mensaje
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$conn2->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Crear Encuesta</title>
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="text-white">
    <div class="container mt-5">
        <h1>Crear Encuesta</h1>
        <!-- Formulario para crear encuesta -->
        <form method="post">
            <div class="mb-3">
                <label for="pregunta">Pregunta:</label>
                <input type="text" id="pregunta" name="pregunta" class="form-control" required>
            </div>
            <div id="opciones-container" class="mb-3">
                <label>Opciones:</label>
                <div class="input-group mb-2">
                    <input type="text" name="opciones[]" class="form-control" required>
                    <button type="button" class="btn btn-outline-secondary" onclick="agregarOpcion()">Añadir opción</button>
                </div>
            </div>
            <input type="submit" value="Crear encuesta" class="btn btn-primary">
        </form>

        <?php
        // Mostrar mensaje de éxito si existe
        $mensaje = isset($_SESSION['mensaje']) ? $_SESSION['mensaje'] : '';
        if ($mensaje) {
            echo '<div class="alert alert-success mt-3" role="alert">';
            echo $mensaje;
            echo '</div>';
            unset($_SESSION['mensaje']);
        }
        ?>
    </div>

    <script>
        let opcionIndex = 1;

        function agregarOpcion() {
            opcionIndex++;
            const container = document.getElementById('opciones-container');
            const div = document.createElement('div');
            div.classList.add('input-group', 'mb-2');
            div.innerHTML = `
                <input type="text" name="opciones[]" class="form-control" required>
                <button type="button" class="btn btn-outline-secondary" onclick="eliminarOpcion(this)">Eliminar opción</button>
            `;
            container.appendChild(div);
        }

        function eliminarOpcion(button) {
            const container = document.getElementById('opciones-container');
            container.removeChild(button.parentElement);
        }
    </script>
</body>
</html>
