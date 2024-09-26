<?php

// Inicia sesión y verifica si el usuario está autenticado
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$usuario = $_SESSION['usuario'];

// Conexión a la base de datos para verificar el rol del usuario
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbnameUsuarios = "chileher_usuariosregistrados";
$dbnameRequest = "chileher_request";

// Crear conexión para la base de datos de usuarios registrados
$connUsuarios = new mysqli($servername, $username, $password, $dbnameUsuarios);

// Verificar la conexión a la base de datos de usuarios registrados
if ($connUsuarios->connect_error) {
    die("Conexión fallida a la base de datos de usuarios: " . $connUsuarios->connect_error);
}

// Consulta SQL para verificar si el usuario está baneado
$sqlBaneado = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmtBaneado = $connUsuarios->prepare($sqlBaneado);
$stmtBaneado->bind_param("s", $usuario);
$stmtBaneado->execute();
$resultBaneado = $stmtBaneado->get_result();

if ($resultBaneado->num_rows > 0) {
    $rowBaneado = $resultBaneado->fetch_assoc();
    $rolUsuario = $rowBaneado["rol"];

    // Redirigir al dashboard si el usuario es "Baneado"
    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }
}

$stmtBaneado->close();

// Consulta SQL para obtener el rol del usuario
$sql = "SELECT rol FROM usuarios WHERE nombre = ?";
$stmt = $connUsuarios->prepare($sql);
$stmt->bind_param("s", $usuario);
$stmt->execute();
$result = $stmt->get_result();

$rolUsuario = "";

if ($result->num_rows > 0) {
    // Mostrar el rol del usuario si se encontró en la base de datos
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    // Verificar si el usuario tiene Rol de Administrador
    if ($rolUsuario == "Administrador") {
        // Si es Administrador, añadir opciones adicionales
        $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';
    } else {
        // Si no es Administrador, no añadir opciones adicionales
        $opcionesAdicionales = '';
    }
} else {
    // Mensaje de depuración
    echo "No se encontraron filas en la consulta SQL: $sql";
    $rolUsuario = "Rol no encontrado";
}

$stmt->close();
$connUsuarios->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $artista = $_POST["artista"];
    $cancion = $_POST["cancion"];
    $mensaje = $_POST["mensaje"];
    $ipUsuario = $_SERVER['REMOTE_ADDR'];
    $estado = "En cola";

    // Crear conexión para la base de datos de requests
    $connRequest = new mysqli($servername, $username, $password, $dbnameRequest);

    // Verificar la conexión a la base de datos de requests
    if ($connRequest->connect_error) {
        die("Conexión fallida a la base de datos de requests: " . $connRequest->connect_error);
    }

    // Verificar si la canción y el artista ya están en la base de datos
    $sqlCheck = "SELECT * FROM request WHERE Artista = ? AND Cancion = ?";
    $stmtCheck = $connRequest->prepare($sqlCheck);
    $stmtCheck->bind_param("ss", $artista, $cancion);
    $stmtCheck->execute();
    $resultCheck = $stmtCheck->get_result();

    if ($resultCheck->num_rows > 0) {
        // Mostrar mensaje si la canción y el artista ya están pedidos
        $mensajeAgradecimiento = '<div class="alert alert-warning" role="alert">
                                    Artista y canción ya está pedida.
                                  </div>';
    } else {
        // Preparar la consulta SQL para insertar datos, incluyendo la IP
        $sqlInsert = "INSERT INTO request (Nombre, Artista, Cancion, Mensaje, IP) VALUES (?, ?, ?, ?, ?)";
        $stmtInsert = $connRequest->prepare($sqlInsert);
        $stmtInsert->bind_param("sssss", $nombre, $artista, $cancion, $mensaje, $ipUsuario);

        if ($stmtInsert->execute() === TRUE) {
            // Mostrar mensaje si la inserción fue exitosa
            $mensajeAgradecimiento = '<div class="alert alert-success" role="alert">
                                        Gracias por tu petición, se agregará a la base de datos.
                                      </div>';
        } else {
            // Mostrar mensaje de error si la inserción falló
            $mensajeAgradecimiento = '<div class="alert alert-danger" role="alert">
                                        Error: ' . $sqlInsert . '<br>' . $connRequest->error . '
                                      </div>';
        }

        $stmtInsert->close();
    }

    $stmtCheck->close();
    $connRequest->close();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Pide tu canción</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <style type="text/css">
        .form-group {
            text-align: center
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
                        <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                        <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                        <?php echo $opcionesAdicionales; ?>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>
<div class="container mt-5">
    <div class="card bg-dark text-white">
        <div class="card-body">
            <center><h5 class="card-title">Por favor no pidan canciones de Tronic</h5>
            <p class="card-text">No pidan canciones de Tronic, ya que estoy trabajando en la discografía de Tronic.<br>
                Sí, ya tengo la autorización de ellos.</p></center>          
        </div>
    </div>
</div>
<div class="container mt-4">
    <?php
    // Mostrar el mensaje de agradecimiento si está disponible
    if (isset($mensajeAgradecimiento)) {
        echo $mensajeAgradecimiento;
    }
    ?>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <div class="mb-3">
            <label for="nombre" class="form-label">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Tu Nick, pero recuerda que tiene el nick que registraste" required>
        </div>
        <div class="mb-3">
            <label for="artista" class="form-label">Artista:</label>
            <input type="text" class="form-control" id="artista" name="artista" placeholder="Los Tres" required>
        </div>
        <div class="mb-3">
            <label for="cancion" class="form-label">Canción:</label>
            <input type="text" class="form-control" id="cancion" name="cancion" placeholder="He Barrido el Sol" required>
        </div>
        <div class="mb-3">
            <label for="mensaje" class="form-label">¿Por qué quieres que este en el proyecto?(Opcional)</label>
            <textarea class="form-control" id="mensaje" name="mensaje" rows="4" oninput="limitarCaracteres(this)" placeholder="300 carácteres como máximo."></textarea>
        </div>
        <center><button type="submit" class="btn btn-primary">Enviar Solicitud</button></center>
    </form>
</div>
<script>
        function limitarCaracteres(elemento) {
            const limite = 300;
            if (elemento.value.length > limite) {
                elemento.value = elemento.value.substring(0, limite);
                alert("Has alcanzado el límite máximo de caracteres.");
            }
        }
    </script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
</body>
</html>
