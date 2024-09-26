<?php

session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_usuariosregistrados";

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
    $rolUsuario = $row["rol"];

    if ($rolUsuario != "Administrador") {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

// Consulta SQL para obtener los usuarios
$sqlUsuarios = "SELECT id, nombre, correo, fecha_registro, fecha_nacimiento, rol, fecha_vencimiento_vip, IP FROM usuarios ORDER BY id";
$resultUsuarios = $conn->query($sqlUsuarios);

// Consulta SQL para contar los usuarios
$sqlcontador = "SELECT COUNT(nombre) AS total_usuarios FROM usuarios";
$resultcontador = $conn->query($sqlcontador);

$totalusuarios = 0;

if ($resultcontador->num_rows > 0) {
    $rowcontador = $resultcontador->fetch_assoc();
    $totalusuarios = $rowcontador["total_usuarios"] - 3;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Gestión de Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">

    <style>
        .table-container {
            display: flex;
            justify-content: center;
        }
        table {
            width: 80%; /* Ajusta el ancho de la tabla según tus necesidades */
        }
    </style>
</head>

<body class="bg-light text-white">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">
            <img src="https://github.com/chilehero2023/chilehero/blob/main/chilehero_horizontal.png?raw=true" class="img-fluid" alt="ChileHero Logo" width="120" height="auto">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="canciones.php">Canciones</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="pide-tu-cancion.php">Request</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="https://discord.gg/XMw8ysskdU">Discord</a>
                </li>
            </ul>
            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <?php echo htmlspecialchars($usuario); ?>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="dashboard.php">Ir al Panel</a></li>
                        <li><a class="dropdown-item" href="zona-administracion.php">Zona Administración</a></li>
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <center><h2>Usuarios Registrados: <?php echo $totalusuarios; ?></h2></center>
    <div class="table-container">
        <table class="table table-striped table-dark">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Fecha de Registro</th>
                    <th>Fecha de Nacimiento</th>
                    <th>Rol</th>
                    <th>Fecha de Vecimiento (VIP)</th>
                    <th>IP</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($resultUsuarios->num_rows > 0) {
                    while ($row = $resultUsuarios->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['nombre']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['correo']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fecha_registro']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['fecha_nacimiento']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['rol']) . "</td>";
                        if(is_null($row['fecha_vencimiento_rol'])){
                            echo "<td><span style='color:red;'>No tiene VIP ni Donador</span></td>";
                        }else{
                            echo "<td>" . htmlspecialchars($row['fecha_vencimiento_rol']) . "</td>";
                        }
                        if (is_null($row['IP'])) {
                            echo "<td><span style='color: red;'>No disponible</span></td>";
                        } else {
                            echo "<td>" . htmlspecialchars($row['IP']) . "</td>";
                        }
                        echo "<td><a href='modificar_usuario.php?id=" . htmlspecialchars($row['id']) . "' class='btn btn-primary'>Modificar</a></td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8'>No se encontraron usuarios</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>