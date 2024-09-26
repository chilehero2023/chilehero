<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre de usuario de la sesión
$usuario = $_SESSION['usuario'];

// Credenciales de la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname_usuarios = "chileher_usuariosregistrados";
$dbname_encuestas = "chileher_encuesta";

// Crear una conexión a la base de datos de usuarios
$conn_usuarios = new mysqli($servername, $username, $password, $dbname_usuarios);

// Verificar si la conexión fue exitosa
if ($conn_usuarios->connect_error) {
    die("Conexión fallida: " . $conn_usuarios->connect_error);
}

// Verificar si el usuario tiene el rol de Administrador
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn_usuarios->prepare($sql);
if ($stmt === false) {
    die('Error en la preparación de la consulta de rol: ' . $conn_usuarios->error);
}
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    if ($rolUsuario != "Administrador") {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

// Cerrar la consulta y la conexión a la base de datos de usuarios
$stmt->close();
$conn_usuarios->close();

// Crear una conexión a la base de datos de encuestas
$conn_encuestas = new mysqli($servername, $username, $password, $dbname_encuestas);

// Verificar si la conexión fue exitosa
if ($conn_encuestas->connect_error) {
    die("Conexión fallida: " . $conn_encuestas->connect_error);
}

// Variable para almacenar el estado de la actualización
$actualizado = false;

// Verificar si se recibió el ID del anuncio
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Obtener el anuncio de la base de datos
    $sql = "SELECT * FROM anuncios WHERE id = ?";
    $stmt = $conn_encuestas->prepare($sql);
    if ($stmt === false) {
        die('Error en la preparación de la consulta de anuncio: ' . $conn_encuestas->error);
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $anuncio = $result->fetch_assoc();
    } else {
        echo "Anuncio no encontrado.";
        exit();
    }

    // Verificar si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $titulo = $_POST['titulo'];
        $texto = $_POST['texto'];

        // Actualizar el anuncio en la base de datos
        $sql = "UPDATE anuncios SET titulo = ?, texto = ? WHERE id = ?";
        $stmt = $conn_encuestas->prepare($sql);
        if ($stmt === false) {
            die('Error en la preparación de la consulta de actualización: ' . $conn_encuestas->error);
        }
        $stmt->bind_param("ssi", $titulo, $texto, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $actualizado = true;
        } else {
            echo "Error al actualizar el anuncio o no se realizaron cambios.";
        }

        // Cerrar la consulta y la conexión
        $stmt->close();
    }
} else {
    echo "ID de anuncio no recibido.";
}

$conn_encuestas->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Anuncio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
    <div class="container mt-5">
        <h1>Editar Anuncio</h1>

        <?php if ($actualizado): ?>
            <div class="alert alert-success" role="alert">
                El anuncio se modificó correctamente.
            </div>
        <?php endif; ?>

        <form method="post" action="">
            <div class="mb-3">
                <label for="titulo" class="form-label">Título</label>
                <input type="text" class="form-control" id="titulo" name="titulo" value="<?php echo htmlspecialchars($anuncio['titulo']); ?>" required>
            </div>
            <div class="mb-3">
                <label for="texto" class="form-label">Texto</label>
                <textarea class="form-control" id="texto" name="texto" rows="5" required><?php echo htmlspecialchars($anuncio['texto']); ?></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Guardar cambios</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
