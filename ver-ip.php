<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>IP Registradas</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <link href="css/stylesip.css" rel="stylesheet">
    <script>
        document.addEventListener('contextmenu', function (e) {
            e.preventDefault();
        });

        function confirmarEliminacion(id) {
            if (confirm('¿Estás seguro de que deseas eliminar esta IP?')) {
                window.location.href = 'ver-ip.php?id_eliminar=' + id;
            }
        }
    </script>
</head>
<body class="text-white">
    <div class="container">
        <h1 class="registro_de_ip">Listado de IPs registradas:</h1>
        <?php
        // Conexión a la base de datos
        $conexion = mysqli_connect("localhost:3306", "chileher_smuggling", "aweonaoctm2024", "chileher_requestip");

        if (mysqli_connect_errno()) {
            echo "<div class='alert alert-danger'>Error al conectar a MySQL: " . mysqli_connect_error() . "</div>";
            exit();
        }

        // Eliminar IP si se ha solicitado
        if (isset($_GET['id_eliminar'])) {
            $id_eliminar = $_GET['id_eliminar'];
            $query_eliminar = "DELETE FROM registro_ip WHERE id = '$id_eliminar'";
            $resultado_eliminar = mysqli_query($conexion, $query_eliminar);

            if ($resultado_eliminar) {
                echo "<div class='alert alert-success'>IP eliminada correctamente.</div>";
            } else {
                echo "<div class='alert alert-danger'>Error al eliminar la IP: " . mysqli_error($conexion) . "</div>";
            }
        }

        // Consulta para obtener las IPs registradas
        $query = "SELECT id, nombre, ip FROM registro_ip";
        $resultado = mysqli_query($conexion, $query);
        ?>

        <table class="table table-bordered table-dark">
            <thead class="thead-dark">
                <tr>
                    <th>Nombre</th>
                    <th>IP</th>
                    <th>Modificar</th>
                    <th>Eliminar</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (mysqli_num_rows($resultado) > 0) {
                    while ($fila = mysqli_fetch_assoc($resultado)) {
                        echo "<tr id='fila_{$fila['id']}'>";
                        echo "<td>{$fila['nombre']}</td>";
                        echo "<td>{$fila['ip']}</td>";
                        echo "<td><a href='modificar-ip.php?id_modificar={$fila['id']}' class='btn btn-primary'>Modificar</a></td>";
                        echo "<td><button onclick='confirmarEliminacion({$fila['id']})' class='btn btn-danger'>Eliminar</button></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='4'>No hay IPs registradas.</td></tr>";
                }

                // Cerrar la conexión
                mysqli_close($conexion);
                ?>
            </tbody>
        </table>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</html>
