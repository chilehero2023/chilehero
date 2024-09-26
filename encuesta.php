<?php
session_start();

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";

// Conectar a la base de datos de encuestas
$dbname = "chileher_encuesta";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Verificar el ID de la encuesta
if (isset($_GET['id'])) {
    $encuestaId = $_GET['id'];

    // Consulta para obtener los resultados
    $stmt = $conn->prepare("SELECT o.opcion, COUNT(v.id) AS votos FROM opciones o LEFT JOIN votos v ON o.id = v.opcion_id WHERE o.encuesta_id = ? GROUP BY o.opcion");
    $stmt->bind_param("i", $encuestaId);
    $stmt->execute();
    $resultados = $stmt->get_result();
} else {
    echo "No se proporcionó el ID de la encuesta.";
    exit;
}

// Consultar opciones de la encuesta
$sql = "SELECT * FROM opciones WHERE encuesta_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_encuesta);
$stmt->execute();
$opciones = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Consultar votos por opción
$votos = [];
foreach ($opciones as $opcion) {
    $sql = "SELECT COUNT(*) as votos FROM votos WHERE encuesta_id = ? AND opcion_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $id_encuesta, $opcion['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $votos[$opcion['id']] = $row['votos'];
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Resultados de la Encuesta</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
    <div class="container mt-5">
        <h2>Resultados de la Encuesta</h2>

        <?php if ($resultados->num_rows > 0): ?>
            <ul class="list-group">
                <?php while ($row = $resultados->fetch_assoc()): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <?php echo htmlspecialchars($row['opcion']); ?>
                        <span class="badge bg-primary rounded-pill"><?php echo $row['votos']; ?></span>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No hay votos registrados para esta encuesta.</p>
        <?php endif; ?>
    </div>
</body>
</html>
