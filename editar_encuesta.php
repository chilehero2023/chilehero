<?php
session_start();

// Verificar si el usuario está autenticado y tiene rol de Administrador
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

// Conectar a la base de datos para verificar el rol
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['rol'] !== 'Administrador') {
        header("Location: dashboard.php");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}

$stmt->close();
$conn->close();

// Conectar a la base de datos de encuestas
$conn = new mysqli($servername, $username, $password, "chileher_encuesta");

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos de la encuesta
$encuesta_id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$sql_encuesta = "SELECT * FROM encuestas WHERE id = ?";
$stmt_encuesta = $conn->prepare($sql_encuesta);
$stmt_encuesta->bind_param("i", $encuesta_id);
$stmt_encuesta->execute();
$result_encuesta = $stmt_encuesta->get_result();
$encuesta = $result_encuesta->fetch_assoc();

$sql_opciones = "SELECT * FROM opciones WHERE encuesta_id = ?";
$stmt_opciones = $conn->prepare($sql_opciones);
$stmt_opciones->bind_param("i", $encuesta_id);
$stmt_opciones->execute();
$result_opciones = $stmt_opciones->get_result();

$opciones = [];
while ($row = $result_opciones->fetch_assoc()) {
    $opciones[] = $row;
}

$stmt_encuesta->close();
$stmt_opciones->close();

$mensaje = '';
// Actualizar la encuesta si se envía el formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pregunta = $_POST['pregunta'];
    $nuevas_opciones = $_POST['opciones'];

    // Actualizar la pregunta en la base de datos
    $sql_update_encuesta = "UPDATE encuestas SET pregunta = ? WHERE id = ?";
    $stmt_update_encuesta = $conn->prepare($sql_update_encuesta);
    $stmt_update_encuesta->bind_param("si", $pregunta, $encuesta_id);
    $stmt_update_encuesta->execute();

    // Eliminar las opciones existentes
    $sql_delete_opciones = "DELETE FROM opciones WHERE encuesta_id = ?";
    $stmt_delete_opciones = $conn->prepare($sql_delete_opciones);
    $stmt_delete_opciones->bind_param("i", $encuesta_id);
    $stmt_delete_opciones->execute();

    // Insertar nuevas opciones
    foreach ($nuevas_opciones as $opcion) {
        $sql_insert_opcion = "INSERT INTO opciones (encuesta_id, opcion) VALUES (?, ?)";
        $stmt_insert_opcion = $conn->prepare($sql_insert_opcion);
        $stmt_insert_opcion->bind_param("is", $encuesta_id, $opcion);
        $stmt_insert_opcion->execute();
    }

    // Mensaje de éxito
    $mensaje = 'Los cambios se han guardado correctamente.';
    // Redirigir a la misma página con el mensaje en la URL
    header("Location: editar_encuesta.php?id=$encuesta_id&mensaje=" . urlencode($mensaje));
    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Editar Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
    <div class="container mt-5">
        <h1>Editar Encuesta</h1>

        <?php if (isset($_GET['mensaje'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo htmlspecialchars($_GET['mensaje']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post">
            <div class="mb-3">
                <label for="pregunta" class="form-label">Pregunta</label>
                <input type="text" class="form-control" id="pregunta" name="pregunta" value="<?php echo htmlspecialchars($encuesta['pregunta']); ?>" required>
            </div>
            <div id="opciones-container">
                <?php foreach ($opciones as $index => $opcion): ?>
                    <div class="mb-3">
                        <label for="opcion-<?php echo $index; ?>" class="form-label">Opción <?php echo $index + 1; ?></label>
                        <input type="text" class="form-control" id="opcion-<?php echo $index; ?>" name="opciones[]" value="<?php echo htmlspecialchars($opcion['opcion']); ?>" required>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-start">
                <button type="button" class="btn btn-secondary me-1" onclick="agregarOpcion()">Agregar Opción</button>
                <button type="submit" class="btn btn-primary ms-1">Guardar Cambios</button>
            </div>
        </form>
    </div>

    <script>
        function agregarOpcion() {
            const container = document.getElementById('opciones-container');
            const index = container.children.length;
            const div = document.createElement('div');
            div.classList.add('mb-3');
            div.innerHTML = `
                <label for="opcion-${index}" class="form-label">Opción ${index + 1}</label>
                <input type="text" class="form-control" id="opcion-${index}" name="opciones[]" required>
            `;
            container.appendChild(div);
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
