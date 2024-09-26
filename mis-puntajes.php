<?php
session_start();

// Datos de conexión a la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

// Conectar a la base de datos de usuarios
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida a la base de datos: " . $conn->connect_error);
}

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

// Obtener el nombre del usuario desde la sesión
$nombre_usuario = $_SESSION['usuario'];

// Obtener la lista de puntajes del usuario actual
$sql_puntajes = "SELECT cancion, puntuacion, porcentaje, fecha_puntuacion, estado, captura 
                 FROM ranking 
                 WHERE usuario_nombre = ?";
$stmt_puntajes = $conn->prepare($sql_puntajes);
$stmt_puntajes->bind_param("s", $nombre_usuario);
$stmt_puntajes->execute();
$result_puntajes = $stmt_puntajes->get_result();

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Puntajes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Mis Puntajes</h1>
    
    <?php if ($result_puntajes->num_rows > 0): ?>
        <table>
            <tr>
                <th>Canción</th>
                <th>Puntuación</th>
                <th>Porcentaje</th>
                <th>Fecha de Puntuación</th>
                <th>Estado</th>
                <th>Captura</th>
            </tr>
            <?php while ($row = $result_puntajes->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['cancion']); ?></td>
                    <td><?php echo htmlspecialchars($row['puntuacion']); ?></td>
                    <td><?php echo htmlspecialchars($row['porcentaje']); ?></td>
                    <td><?php echo htmlspecialchars($row['fecha_puntuacion']); ?></td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td>
                        <?php if (!empty($row['captura'])): ?>
                            <a href="/puntuaciones/<?php echo htmlspecialchars($row['captura']); ?>" target="_blank">Ver imagen</a>
                        <?php else: ?>
                            No disponible
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No hay puntajes para mostrar.</p>
    <?php endif; ?>

    <?php
    // Cerrar la conexión
    $stmt_puntajes->close();
    $conn->close();
    ?>
</body>
</html>
