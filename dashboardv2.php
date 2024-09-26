<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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

$accesoAdministrador = false;
$accesoUsuario = false;
$accesoBaneado = false;
$accesoCharter = false;
$opcionesAdicionales = '';
$opcionesAdicionales2 = '';
$opcionesAdicionales3 = '';

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];
    
    switch ($rolUsuario) {
        case "Usuario":
            $accesoUsuario = true;
            break;
        case "Baneado":
            $accesoBaneado = true;
            break;
        case "Administrador":
            $accesoAdministrador = true;
            $opcionesAdicionales = '<a class="nav-link text-white" href="zona-administracion.php">Zona Administración</a>';
            $opcionesAdicionales2 = '<a class="nav-link text-white" href="subir-canciones.php">Sube tu chart</a>';
            break;
        case "Charter":
            $accesoCharter = true;
            $opcionesAdicionales2 = '<a class="nav-link text-white" href="subir-canciones.php">Sube tu chart</a>';
            $opcionesAdicionales3 = '<a class="nav-link text-white" href="gestionar-canciones.php">Gestionar canciones</a>';
            break;
    }
} else {
    $rolUsuario = "Rol no encontrado";
}
$stmt->close();

// Cambiar a la base de datos de encuestas
$dbname = "chileher_encuesta";
$conn->select_db($dbname);

// Consultar encuestas disponibles
$sql = "SELECT * FROM encuestas";
$result = $conn->query($sql);

$encuestas = [];
if ($result->num_rows > 0) {
    $encuestas = $result->fetch_all(MYSQLI_ASSOC);
}

// Consultar opciones de cada encuesta
$opciones = [];
foreach ($encuestas as $encuesta) {
    $sql = "SELECT * FROM opciones WHERE encuesta_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $encuesta['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $opciones[$encuesta['id']] = $result->fetch_all(MYSQLI_ASSOC);
}

// Consultar anuncios disponibles
$sql = "SELECT titulo, texto, prioridad, fecha_creacion FROM anuncios ORDER BY prioridad DESC";
$result = $conn->query($sql);

$anuncios = [];
if ($result->num_rows > 0) {
    $anuncios = $result->fetch_all(MYSQLI_ASSOC);
}

// Manejo de votos
$mensaje = '';

// Crear un array para almacenar las encuestas en las que el usuario ha votado
$encuestasVotadas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['opcion'], $_POST['id_encuesta']) && !empty($_POST['opcion']) && !empty($_POST['id_encuesta'])) {
        // Verificación de voto existente
        $sql = "SELECT COUNT(*) AS votos FROM votos WHERE usuario = ? AND encuesta_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $usuario, $_POST['id_encuesta']);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row['votos'] == 0) {
            try {
                $stmt = $conn->prepare("INSERT INTO votos (usuario, encuesta_id, opcion_id) VALUES (?, ?, ?)");
                $stmt->bind_param("sii", $usuario, $_POST['id_encuesta'], $_POST['opcion']);
                $stmt->execute();
                $mensaje = "Has votado exitosamente.";
            } catch (mysqli_sql_exception $e) {
                $mensaje = "Error al registrar el voto: " . $e->getMessage();
            }
        } else {
            $mensaje = "Ya has votado en esta encuesta.";
        }
    } else {
        $mensaje = "Datos incompletos.";
    }
}

// Cerrar la conexión
$conn->close();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Panel de Usuario</title>
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .sidebar-menu {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: 250px;
            background-color: #000773;
            color: #fff;
            z-index: 1000;
            display: flex;
            flex-direction: column;
        }
        .sidebar-menu .logo {
            padding: 15px;
            text-align: center;
        }
        .sidebar-menu .user-profile {
            padding: 15px;
            border-bottom: 1px solid #333;
            position: relative;
        }
        .sidebar-menu .user-profile .user-link {
            display: flex;
            align-items: center;
            cursor: pointer;
        }
        .sidebar-menu .user-profile .user-link img {
            border-radius: 50%;
        }
        .sidebar-menu .username {
            margin-left: 10px;
            color: #fff;
        }

        /* Flecha hacia abajo */
        .sidebar-menu .user-link .arrow {
            margin-left: auto;
            transition: transform 0.4s ease;
        }
        /* Flecha girada cuando el menú está desplegado */
        .sidebar-menu .user-link .arrow.show {
            transform: rotate(180deg);
        }

        /* Estilo del menú desplegable */
        .sidebar-menu .dropdown-menu {
            background-color: #000773;
            color: #fff;
            position: absolute;
            top: 100%; 
            left: 0;
            width: 100%;
            display: block;
            max-height: 0;
            overflow: hidden;
            opacity: 0;
            transition: max-height 0.4s ease, opacity 0.4s ease;
            padding: 0;
        }
        .sidebar-menu .dropdown-menu.show {
            max-height: 200px; /* Ajusta según el número de elementos */
            opacity: 1;
            padding: 10px 0;
        }
        .sidebar-menu .dropdown-menu li {
            list-style: none;
        }
        .sidebar-menu .dropdown-menu a {
            display: block;
            padding: 10px 20px;
            color: #fff;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }
        .sidebar-menu .dropdown-menu a:hover {
            background-color: #730000;
        }
        .sidebar-menu .nav {
            flex-grow: 1;
            padding: 15px;
        }
        .sidebar-menu .nav-link {
            color: #fff;
            padding: 10px 20px;
            text-decoration: none;
        }
        .sidebar-menu .nav-link:hover {
            background-color: #730000;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .main-content img {
            max-width: 100%;
            height: auto;
        }
        .img-rounded {
            border-radius: 15px; /* Ajusta el valor según el nivel de redondeo deseado */
        }
        .img-rounded:hover {
            filter: brightness(0.7); /* Aplica el efecto de oscurecimiento */
        }
    </style>
</head>
<body class="bg-light text-white">
    <div class="sidebar-menu">
        <header class="logo-env">
            <div class="logo">
                <img src="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true" width="160" class="img-responsive" alt="" />
            </div>
        </header>
        <div class="user-profile">
            <div class="user-link" onclick="toggleDropdown()">				
                <span class="username">Bienvenid@ <strong><?php echo htmlspecialchars($usuario); ?>!</strong></span>
                <!-- Flecha hacia abajo -->
                <span class="arrow">&#9662;</span>
            </div>
            <ul class="dropdown-menu" id="profileOptions">
                <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                <?php echo $opcionesAdicionales; ?>
                <?php echo $opcionesAdicionales2; ?>
                <li><a class="dropdown-item" href="#" onclick="toggleDropdown()">Cerrar</a></li>
            </ul>
        </div>
        <nav class="nav flex-column" id="mainMenu">
            <a class="nav-link text-white" href="#home">Inicio</a>
            <a class="nav-link text-white" href="#about">Acerca de</a>
            <a class="nav-link text-white" href="#services">Servicios</a>
            <a class="nav-link text-white" href="#contact">Contacto</a>
        </nav>
    </div>
    <div class="main-content">
        <div class="container mt-5">
            <div class="row">
                <div class="col-4">
                    <a class="navbar-brand" href="https://discord.gg/XMw8ysskdU">
                    <center><img src="img/Sin título-2.png" class="img-fluid img-rounded" alt="ChileHero Logo" width="200"></center>
                    </a>
                </div>
                <div class="col-4">
                    <a class="navbar-brand" href="canciones.php">
                    <center><img src="img/Sin título-3.png" class="img-fluid img-rounded" alt="ChileHero Logo" width="200"></center>
                    </a>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-12">
                    <h5 class="card-title">Anuncios</h5>
                        <?php foreach ($anuncios as $anuncio): ?>
                                <?php

                                if ($anuncio['prioridad'] === 'urgente') {
                                    $tituloEstilo = 'color: darkred;';
                                } else {
                                    $tituloEstilo = '';
                                    }
                                ?>
                                <h5 class="card-title" style="<?php echo $tituloEstilo; ?>">
                                    <?php echo htmlspecialchars($anuncio['titulo']); ?>
                                </h5>
                                <p class="card-text"><?php echo htmlspecialchars($anuncio['texto']); ?><br>
                                <small>Publicado el: <?php echo htmlspecialchars(date('d-m-Y', strtotime($anuncio['fecha_creacion']))); ?></small></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function toggleDropdown() {
            const profileOptions = document.getElementById('profileOptions');
            const arrow = document.querySelector('.arrow');
            
            profileOptions.classList.toggle('show');
            arrow.classList.toggle('show'); // Esto hará que la flecha rote
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
</body>
</html>
