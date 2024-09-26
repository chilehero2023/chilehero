<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
    $titulo = $_POST['titulo'];
    $texto = $_POST['texto'];
    $prioridad = $_POST['prioridad']; // Obtén la prioridad seleccionada

    // Insertar anuncio en la base de datos
    $sql = "INSERT INTO anuncios (titulo, texto, prioridad) VALUES (?, ?, ?)";
    $stmt = $conn2->prepare($sql);
    $stmt->bind_param("sss", $titulo, $texto, $prioridad);
    $stmt->execute();

    // Establecer mensaje de éxito en la sesión
    $_SESSION['mensaje'] = "Anuncio creado";
    
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
    <title>Crear Anuncio</title>
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="text-white">
    <div class="container mt-5">
        <h1>Crear Anuncio</h1>
        <!-- Formulario para crear anuncio -->
        <form method="post">
            <div class="mb-3">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="texto">Texto:</label>
                <textarea id="texto" name="texto" class="form-control" required></textarea>
            </div>
            <div class="mb-3">
                <label for="prioridad">Prioridad:</label>
                <select id="prioridad" name="prioridad" class="form-control" required>
                    <option value="normal">Normal</option>
                    <option value="urgente">Urgente</option>
                </select>
            </div>
            <input type="submit" value="Crear anuncio" class="btn btn-primary">
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
</body>
</html>
