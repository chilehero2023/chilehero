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

$rolUsuario = "";

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $rolUsuario = $row["rol"];

    if ($rolUsuario == "Baneado") {
        header("Location: dashboard.php");
        exit();
    }
} else {
    echo "No se encontraron filas en la consulta SQL: $sql";
}

$stmt->close();
$conn->close();

$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_chartsoficiales";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

$imagenes = [];
$descargas = [];

// Obtener canciones más descargadas en los últimos 7 días
$fechaHace7Dias = date('Y-m-d', strtotime('-7 days'));
$sqlTopDescargas = "SELECT ID, imagen_nombre, Artista, Cancion, Descargas 
                    FROM chartsoficiales 
                    WHERE Fecha_Descarga >= '$fechaHace7Dias'
                    ORDER BY Descargas DESC 
                    LIMIT 5"; // Limitar a 5 canciones

$resultTopDescargas = $conn2->query($sqlTopDescargas);

if (!$resultTopDescargas) {
    die("Error en la consulta: " . $conn2->error);
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['buscar'])) {
    $filtroArtista = $_GET['artista'];
    $filtroCancion = $_GET['cancion'];

    // Construir la consulta SQL con los filtros aplicados
    $sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales WHERE 1=1";
    
    $paramTypes = "";
    $params = [];

    if (!empty($filtroArtista)) {
        $sql .= " AND Artista LIKE ?";
        $paramTypes .= "s";
        $params[] = "%$filtroArtista%";
    }
    
    if (!empty($filtroCancion)) {
        $sql .= " AND Cancion LIKE ?";
        $paramTypes .= "s";
        $params[] = "%$filtroCancion%";
    }
    
    $sql .= " ORDER BY Artista";

    $stmt = $conn2->prepare($sql);
    
    if ($stmt === false) {
        die("Error al preparar la consulta: " . $conn2->error);
    }
    
    if (!empty($paramTypes)) {
        $stmt->bind_param($paramTypes, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result === false) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagenes[] = $row;
        }
    } else {
        
    }
    $stmt->close();
} else {
    // Si no se envió el formulario de búsqueda, cargar todas las canciones sin filtros
    $sql = "SELECT ID, imagen_nombre, Artista, Cancion, Descarga_CH, Descarga_GHWTDE, Descarga_RB3 FROM chartsoficiales ORDER BY Artista";
    $result = $conn2->query($sql);

    if (!$result) {
        die("Error en la consulta: " . $conn2->error);
    }

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $imagenes[] = $row;
        }
    }
}

$sqlContador = "SELECT COUNT(*) AS total_canciones FROM chartsoficiales";
$resultContador = $conn2->query($sqlContador);
$totalcanciones = 0;
if ($resultContador->num_rows > 0) {
    $rowContador = $resultContador->fetch_assoc();
    $totalcanciones = $rowContador["total_canciones"];
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>En Mantenimiento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            text-align: center;
            background-color: #fff;
            padding: 40px;
            
        }

        h1 {
            font-size: 3rem;
            color: #333;
        }

        p {
            font-size: 1.2rem;
            color: #555;
        }

        .progress-container {
            width: 100%;
            max-width: 400px;
            margin: 20px auto;
            background-color: #e0e0e0;
            border-radius: 25px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .progress-bar {
            height: 25px;
            background-color: #890000;
            width: 0%;
            transition: width 1s ease-in-out;
            border-radius: 25px;
        }

        #countdown {
            font-size: 1.5rem;
            color: #ff0000;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <div class="container bg-dark2">
        <h1><font color="white">Estamos en Mantenimiento</h1><span><small>NO SE DESESPEREN, sigan en mi instagram, para que esten al tanto</small></span>
        <p>Volveremos en:</p></font>
        <div id="countdown"></div>
        <div class="progress-container">
            <div class="progress-bar" id="progress-bar"></div>
        </div>
        <a href="dashboard.php" class="btn btn-danger">Ir al Panel</a>
    </div>

    <script>
        // Fecha a la que queremos que finalice la cuenta regresiva
        var countdownDate = new Date("Sep 18, 2024 00:00:00").getTime();
        var startDate = new Date(); // Fecha de inicio del mantenimiento

        // Actualizar la cuenta regresiva y la barra de progreso cada segundo
        var countdownFunction = setInterval(function() {

            // Obtener la fecha y hora actual
            var now = new Date().getTime();

            // Calcular la distancia entre la fecha actual y la fecha de cuenta regresiva
            var distance = countdownDate - now;
            var totalDuration = countdownDate - startDate.getTime();
            var elapsedTime = now - startDate.getTime();

            // Calcular el progreso en porcentaje
            var progress = (elapsedTime / totalDuration) * 100;
            document.getElementById("progress-bar").style.width = progress + "%";

            // Cálculos de tiempo para días, horas, minutos y segundos
            var days = Math.floor(distance / (1000 * 60 * 60 * 24));
            var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
            var seconds = Math.floor((distance % (1000 * 60)) / 1000);

            // Mostrar el resultado en el elemento con id="countdown"
            document.getElementById("countdown").innerHTML = days + "d " + hours + "h "
            + minutes + "m " + seconds + "s ";

            // Si la cuenta regresiva termina
            if (distance < 0) {
                clearInterval(countdownFunction);
                document.getElementById("countdown").innerHTML = "";
                document.getElementById("progress-bar").style.width = "100%";
            }
        }, 1000);
    </script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"></script>
</body>
</html>
