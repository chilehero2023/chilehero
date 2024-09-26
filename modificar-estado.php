<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = htmlspecialchars($_GET['id']);

if (empty($id) || !is_numeric($id)) {
    die("ID inválido");
}

$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_request";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos actuales del estado seleccionado
$sql = "SELECT estado FROM request WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($estado);
$stmt->fetch();
$stmt->close();

// Procesar el formulario de modificación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['estado'])) {
    $nuevoEstado = $_POST['estado'];

    // Actualizar el estado en la base de datos
    $sql = "UPDATE request SET estado = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $nuevoEstado, $id);
    $stmt->execute();

    // Verificar si la actualización fue exitosa
    if ($stmt->affected_rows > 0) {
        // Establecer un mensaje de éxito para mostrar debajo del formulario
        $mensaje = "Modificación realizada con éxito";
        $mensajeClase = "alert alert-success";
    } else {
        // Establecer un mensaje de error para mostrar debajo del formulario
        $mensaje = "No se pudo realizar la modificación";
        $mensajeClase = "alert alert-danger";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Modificar Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-auto">
                <h2>Modificar Estado</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
                    <div class="mb-3">
                        <label for="estado" class="form-label">Estado</label>
                        <select class="form-select" id="estado" name="estado">
                            <option value="En Cola" <?php if ($estado == "En Cola") echo "selected"; ?>>En Cola</option>
                            <option value="En Progreso" <?php if ($estado == "En Progreso") echo "selected"; ?>>En Progreso</option>
                            <option value="Listo" <?php if ($estado == "Listo") echo "selected"; ?>>Listo</option>              
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </form>
                <div class="mt-3">
                    <?php
                    if (isset($mensaje)) {
                        echo "<div class='$mensajeClase' role='alert'>$mensaje</div>";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
