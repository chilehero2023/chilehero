<?php
// Datos de conexión a la base de datos
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";

// Conectar a la base de datos de ranking
$conn_ranking = new mysqli($servername, $username, $password, "chileher_usuariosregistrados");

if ($conn_ranking->connect_error) {
    die("Conexión fallida a ranking: " . $conn_ranking->connect_error);
}

// Consultar los puntajes
$sql = "SELECT id, cancion, usuario_nombre, puntuacion, porcentaje, captura, estado FROM ranking";
$result = $conn_ranking->query($sql);

if ($result === false) {
    die("Error en la consulta: " . $conn_ranking->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Puntajes</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
        .captura {
            max-width: 100px;
            max-height: 100px;
        }
    </style>
</head>
<body>
    <h1>Gestión de Puntajes</h1>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Canción</th>
                <th>Nombre de Usuario</th>
                <th>Puntuación</th>
                <th>Porcentaje</th>
                <th>Captura</th>
                <th>Estado</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['cancion']); ?></td>
                    <td><?php echo htmlspecialchars($row['usuario_nombre']); ?></td>
                    <td><?php echo htmlspecialchars($row['puntuacion']); ?></td>
                    <td><?php echo htmlspecialchars($row['porcentaje']); ?></td>
                    <td>
                        <a href="puntuaciones/<?php echo htmlspecialchars($row['captura']); ?>" target="_blank">
                            Ver Captura
                        </a>
                    </td>
                    <td><?php echo htmlspecialchars($row['estado']); ?></td>
                    <td>
                        <a href="editar_puntuacion.php?id=<?php echo htmlspecialchars($row['id']); ?>">Modificar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <?php
    $result->free();
    $conn_ranking->close();
    ?>
</body>
</html>
