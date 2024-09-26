<?php

session_start();



// Inicializar variables para la gestión de roles

$accesoAdministrador = false;

$accesoUsuario = false;

$accesoBaneado = false;

$accesoCharter = false;

$opcionesAdicionales = '';

$opcionesAdicionales2 = '';



// Verificar si hay sesión activa para obtener el rol del usuario

if (isset($_SESSION['usuario'])) {

    $usuario = $_SESSION['usuario'];



    $servername = "localhost";

    $username = "chileher_smuggling";

    $password = "aweonaoctm2024";

    $dbname = "chileher_usuariosregistrados";



    $conn = new mysqli($servername, $username, $password, $dbname);



    if ($conn->connect_error) {

        die("Conexión fallida: " . $conn->connect_error);

    }



    // Consulta SQL para obtener el rol del usuario

    $sql = "SELECT rol FROM usuarios WHERE nombre = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $usuario);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        $rolUsuario = $row["rol"];



        // Verificar si el usuario está baneado

        if ($rolUsuario === "Baneado") {

            header("Location: dashboard.php");

            exit();

        }



        switch ($rolUsuario) {

            case "Usuario":

                $accesoUsuario = true;

                break;

            case "Administrador":

                $accesoAdministrador = true;

                $opcionesAdicionales = '<li><a class="dropdown-item" href="zona-administracion.php">Zona administración</a></li>';

                $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';

                break;

            case "Charter":

                $accesoCharter = true;

                $opcionesAdicionales2 = '<li><a class="dropdown-item" href="subir-canciones.php">Sube tu chart</a></li>';

                break;

        }

    } else {

        echo "No se encontraron filas en la consulta SQL.";

        $rolUsuario = "Rol no encontrado";

    }



    $stmt->close();

    $conn->close();

}

?>



<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chile Hero - La Casa de la Música Chilena para el Clone Hero</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <style>
        /* Estilos personalizados aquí */
        .carousel-item img {
            max-height: 400px; /* Ajusta la altura máxima de las imágenes del carrusel */
            object-fit: cover; /* Para que las imágenes se ajusten al contenedor sin deformarse */
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
                <?php if ($accesoUsuario || $accesoAdministrador): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="canciones.php">Canciones</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="pide-tu-cancion.php">Request</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Soporte</a>
                    </li>
                <?php endif; ?>
                <!-- Elementos que deben estar visibles sin estar logueado -->
                <li class="nav-item">
                    <a class="nav-link" href="#">Descarga Logos</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Charters</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Ranking</a>
                </li>
            </ul>
            <ul class="navbar-nav ms-auto">
                <?php if (!$accesoUsuario && !$accesoAdministrador && !$accesoCharter): ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="login.php">Accede</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php echo htmlspecialchars($usuario); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <?php if ($accesoUsuario || $accesoAdministrador || $accesoCharter): ?>
                                <li><a class="dropdown-item" href="dashboard.php">Ir al Panel</a></li>
                                <li><a class="dropdown-item" href="cambiar-contraseña.php">Cambiar Contraseña</a></li>
                                <li><a class="dropdown-item" href="cerrar_sesion.php">Cerrar Sesión</a></li>
                                <?php echo $opcionesAdicionales; ?>
                                <?php echo $opcionesAdicionales2; ?>
                            <?php endif; ?>
                        </ul>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

    <section class="hero text-center text-white py-5">
        <div class="container">
            <h1 class="titulos">Descubre lo mejor de la música chilena para Clone Hero</h1>
            <p class="lead">Descarga, escucha y comparte tus canciones favoritas para Clone Hero.</p>
            <a href="#" class="btn btn-primary btn-lg">Explorar Catálogo</a>
        </div>
    </section>

    <section class="artistas-destacados py-5">
		<div id="img1">
			<img src="img/aeros.png" height="550">
		</div>	
        <div class="container">
            <h2 id="tcentral">Artistas Destacados</h2>
            <div id="carouselExample" class="carousel slide" data-bs-interval="10000">
                <div class="carousel-inner">
                    <div class="carousel-item active">
                    <img src="img/losprisioneros.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                    <img src="img/bunkers-grande.jpg" class="d-block w-100" alt="...">
                    </div>
                    <div class="carousel-item">
                    <img src="img/tronic.jpg" class="d-block w-100" alt="...">
                    </div>
                </div>
                <button class="carousel-control-prev" type="button" data-bs-target="#carouselExample" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#carouselExample" data-bs-slide="next">
                    <span class="carousel-control-next-icon" aria-hidden="true"></span>
                    <span class="visually-hidden">Next</span>
                </button>
            </div>
        </div>
    </section>
    

    <center><section class="ultimos-lanzamientos py-5">
        <div class="container">
            <h2 class="titulos">Últimos Lanzamientos</h2>
            
        </div>
    </section>

    <section class="video-destacado">
        <div class="embed-responsive">
        <iframe width="560" height="315" src="https://www.youtube.com/embed/b74HDmRprX8?si=X_WioSJFj5_jjQ-5" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>
        </div>
    </center></section>
    <center><section class="contenido-exclusivo text-center py-5">
        <div class="container">
            <h5>Accede a contenido exclusivo, recomendaciones personalizadas y más.</h5>
            <?php if ($usuario): ?>
                <a href="dashboard.php" class="btn btn-primary">Ir al Panel</a>
            <?php else: ?>
                <a href="login.php" class="btn btn-primary">Acceder</a>
            <?php endif; ?>
        </div>
    </section></center>


    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
