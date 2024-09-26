<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$id = htmlspecialchars($_GET['id']);

$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_chartsoficiales";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener los datos actuales de la canción seleccionada
$sql = "SELECT Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales WHERE ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($artista, $cancion, $descargaCH, $descargaGHWTDE, $descargaRB3);
$stmt->fetch();
$stmt->close();

// Procesar el formulario de modificación
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['artista']) && isset($_POST['cancion'])) {
    $nuevoArtista = $_POST['artista'];
    $nuevaCancion = $_POST['cancion'];
    $nuevaDescargaCH = $_POST['descargaCH'];
    $nuevaDescargaGHWTDE = $_POST['descargaGHWTDE'];
    $nuevaDescargaRB3 = $_POST['descargaRB3'];

    // Actualizar la canción en la base de datos
    $sql = "UPDATE chartsoficiales SET Artista = ?, Cancion = ?, Descarga_CH = ?, Descarga_GHWTDE = ?, Descarga_RB3 = ? WHERE ID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $nuevoArtista, $nuevaCancion, $nuevaDescargaCH, $nuevaDescargaGHWTDE, $nuevaDescargaRB3, $id);
    $stmt->execute();

    // Verificar si la actualización fue exitosa
    if ($stmt->affected_rows > 0) {
        $mensaje = "Modificación realizada con éxito";
        $mensajeClase = "alert alert-success";
    } else {
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
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Modificar Canción</title>
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
                <h2>Modificar Canción</h2>
                <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]) . "?id=" . $id; ?>" method="post">
                    <div class="mb-3">
                        <label for="artista" class="form-label">Artista:</label>
                        <input type="text" id="artista" name="artista" class="form-control" value="<?php echo htmlspecialchars($artista); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="cancion" class="form-label">Canción:</label>
                        <input type="text" id="cancion" name="cancion" class="form-control" value="<?php echo htmlspecialchars($cancion); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descargaCH" class="form-label">Descarga CH:</label>
                        <input type="text" id="descargaCH" name="descargaCH" class="form-control" value="<?php echo htmlspecialchars($descargaCH); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descargaGHWTDE" class="form-label">Descarga GHWTDE:</label>
                        <input type="text" id="descargaGHWTDE" name="descargaGHWTDE" class="form-control" value="<?php echo htmlspecialchars($descargaGHWTDE); ?>">
                    </div>
                    <div class="mb-3">
                        <label for="descargaRB3" class="form-label">Descarga RB3:</label>
                        <input type="text" id="descargaRB3" name="descargaRB3" class="form-control" value="<?php echo htmlspecialchars($descargaRB3); ?>">
                    </div>
                    <button type="submit" class="btn btn-primary">Actualizar Canción</button>
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