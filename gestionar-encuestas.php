<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit(); // Detener la ejecución del script
}

// Obtener el nombre de usuario de la sesión
$usuario = $_SESSION['usuario'];

// Credenciales de la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

// Crear una conexión a la base de datos de usuarios
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
$conn->close();

// Conectar a la base de datos de encuestas
$dbname = "chileher_encuesta";
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar si la conexión fue exitosa
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener todas las encuestas de la base de datos
$sql = "SELECT * FROM encuestas";
$resultEncuestas = $conn->query($sql);

// Aquí puedes agregar el código para mostrar las encuestas o realizar otras acciones
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Gestionar Encuestas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
</head>
<body class="bg-light text-white">
    <div class="container mt-5">
        <h1>Gestionar Encuestas</h1>

        <div id="encuestas-container">
            <?php if ($resultEncuestas->num_rows > 0): ?>
                <?php while ($rowEncuesta = $resultEncuestas->fetch_assoc()): ?>
                    <div class="card mb-3" id="encuesta-<?php echo $rowEncuesta['id']; ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($rowEncuesta['pregunta']); ?></h5>
                            
                            <?php
                            $encuesta_id = $rowEncuesta['id'];

                            // Consultar opciones y votos
                            $stmt_opciones = $conn->prepare("SELECT o.id, o.opcion, COUNT(v.id) AS votos 
                                                               FROM opciones o
                                                               LEFT JOIN votos v ON o.id = v.opcion_id
                                                               WHERE o.encuesta_id = ?
                                                               GROUP BY o.id, o.opcion");
                            $stmt_opciones->bind_param("i", $encuesta_id);
                            $stmt_opciones->execute();
                            $result_opciones = $stmt_opciones->get_result();
                            ?>

                            <ul class="list-group">
                                <?php while ($row_opcion = $result_opciones->fetch_assoc()): ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?php echo htmlspecialchars($row_opcion['opcion']); ?>
                                        <span class="badge bg-primary rounded-pill"><?php echo $row_opcion['votos']; ?></span>
                                    </li>
                                <?php endwhile; ?>
                            </ul>

                            <button class="btn btn-danger mt-2" onclick="eliminarEncuesta(<?php echo $rowEncuesta['id']; ?>)">Eliminar</button>
                            <a href="editar_encuesta.php?id=<?php echo $rowEncuesta['id']; ?>" class="btn btn-primary mt-2">Editar</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="alert alert-info" role="alert">No hay encuestas disponibles.</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function eliminarEncuesta(id) {
            if (confirm('¿Estás seguro de que quieres eliminar esta encuesta?')) {
                const xhr = new XMLHttpRequest();
                xhr.open('GET', 'eliminar_encuesta.php?id=' + id, true);
                xhr.onload = function() {
                    if (xhr.status === 200) {
                        document.getElementById('encuesta-' + id).remove();
                    } else {
                        alert('Error al eliminar la encuesta.');
                    }
                };
                xhr.send();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close(); // Cerrar la conexión a la base de datos
?>
