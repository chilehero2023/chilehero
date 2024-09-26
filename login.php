<?php

session_start();



$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $correo = $_POST['correo'];

    $contraseña = $_POST['contraseña'];



    // Datos para la conexión a la base de datos

    $servername = "localhost";

    $username = "chileher_smuggling";

    $password = "aweonaoctm2024";

    $dbname = "chileher_usuariosregistrados";



    // Conexión a la base de datos

    $conn = new mysqli($servername, $username, $password, $dbname);



    if ($conn->connect_error) {

        die("Conexión fallida: " . $conn->connect_error);

    }



    // Consulta para verificar el usuario y la contraseña

    $sql = "SELECT * FROM usuarios WHERE correo = ?";

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("s", $correo);

    $stmt->execute();

    $result = $stmt->get_result();



    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();

        if (hash('sha256', $contraseña) === $row['contraseña']) {

            // La contraseña es correcta

            $_SESSION['usuario'] = $row['nombre'];

            header("Location: dashboard.php");

            exit();

        } else {

            $message = "Contraseña incorrecta.";

        }

    } else {

        $message = "No se encontró el usuario.";

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

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Inicio de Sesión</title>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link href="css/estilos.css" rel="stylesheet">

    <link href="css/background.css" rel="stylesheet">

    <link href="css/fontello.css" rel="stylesheet">

    <link href="css/color.css" rel="stylesheet">

    <script>

        document.addEventListener('contextmenu', function (e) {

            e.preventDefault();

        });

    </script>

    <style>

        .card {

            border: none;

        }

    </style>

</head>

<body class="text-white">

    <div class="container">

        <br>

        <br>

        <div class="row">

            <div class="col-6 d-flex justify-content-center align-items-center">

                <img src="img/chileherologo2.png" alt="..." class="img-fluid" width="600" height="600">

            </div>

            <div class="col-6">

            <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card">

                    <div class="card-body bg-dark2 text-white">

                        <center><h2 class="card-title mb-4">Inicio de Sesión</h2></center>

                        <?php if ($message !== ""): ?>

                            <div class="alert alert-primary" role="alert">

                                <?php echo $message; ?>

                            </div>

                        <?php endif; ?>

                        <center><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                            <div class="mb-3">

                                <label for="correo" class="form-label">Correo Electrónico:</label>

                                <input type="email" class="form-control" id="correo" name="correo" required style="width: 300px;">

                            </div>

                            <div class="mb-3">

                                <label for="contraseña" class="form-label">Contraseña:</label>

                                <input type="password" class="form-control" id="contraseña" name="contraseña" required style="width: 300px;">

                            </div>

                            <div class="text-center">

                                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                                <i class="fas fa-spinner fa-spin" id="loadingIcon" style="display: none;"></i>

                            </div>

                        </form></center>

                        <div class="mt-4 text-center">

                            <p>¿No tienes cuenta? <a href="registro.php" class="text-primary">Regístrate aquí</a></p>

                            <p>¿Perdiste tu contraseña? <a href="recuperar.php" class="text-primary">Recupérala acá</a></p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>
    <script>
    document.getElementById('loginForm').addEventListener('submit', function() {
        document.getElementById('submitButton').disabled = true; // Desactiva el botón para evitar múltiples envíos
        document.getElementById('loadingIcon').style.display = 'inline-block'; // Muestra el ícono de carga
    });
    </script>


    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

</body>

</html>

