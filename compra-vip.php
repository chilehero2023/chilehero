<?php

session_start();

$accesoAdministrador = false;
$accesoUsuario = false;
$accesoBaneado = false;
$accesoCharter = false;
$opcionesAdicionales = '';
$opcionesAdicionales2 = '';


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

    $sql = "SELECT rol FROM usuarios WHERE nombre = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {

        $row = $result->fetch_assoc();
        $rolUsuario = $row["rol"];
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
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/estilos.css" rel="stylesheet">
    <link href="css/background.css" rel="stylesheet">
    <link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
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
                                <a class="nav-link" href="https://discord.gg/XMw8ysskdU">Discord</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="navbar-nav ms-auto">
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
                    </ul>
                </div>
            </div>
        </nav>
    <center><h1>Compra tu VIP</h1></center>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <div class="card-body">
                        <center><h5>Rol Donador</h5>
                        <p>- Tendrás el rol de donador</p>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <br>
                        <small>Desde $100/mensual<br>
                        (Tú decides que cantidad quieres donar)</small>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="https://www.ceneka.net/chilehero2024" class="btn btn-primary">Donar</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <div class="card-body">
                        <center><h5>Rol Superusuario</h5>
                        <p>- Tendrás Rol Superusuario <br>
                        - Tendrás prioridad de 3 pedidos (request), al azar.<br>
                        - Tendrás acceso a los charts de forma anticipada.<br>
                        - Puedes tener multicuentas, máximo 2</p><br>
                        <center>$5.000/mensual</center>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="https://mpago.la/2Noseym" class="btn btn-light">Obtener VIP Plata</a></center>
                        <br>
                    </ul>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-dark text-white" style="width: 18rem;">
                    <div class="card-body">
                        <center><h5>Rol VIP</h5>
                        <p>- Tendrás beneficios del superusuario<br>
                        - Tendrás prioridad de 5 pedidos (request), al azar.<br>
                        - Tendrás acceso a pruebas de las novedades.<br>
                        - Puedes tener multicuentas, máximo 3</p>
                        <center>$7.500/mensual</center>
                    </div>
                    <ul class="list-group list-group-flush">
                        <br>
                        <center><a href="https://mpago.la/1JEL4hK" class="btn btn-danger">Obtener VIP Oro</a></center>
                        <br>
                    </ul>
                </div>
            </div>
        </div>
        <br>
        <center><h2><font color="red">QUE COMPRES VIP O SUPERUSUARIO NO SIGNIFICA QUE PUEDAS ROMPER LAS REGLAS, SI LAS ROMPES ES BAN</font></h2></center>
        <h4>RECUERDA DEBES ENVIAR UN COMPROBANTE A smuggling@chilehero.cl, con asunto de "PAGO VIP [el tipo de VIP]" (TIENE QUE SER CON ASUNTO, DE LO CONTRARIO SE IGNORARÁ EL CORREO) o a mi ig: chilehero2023</h4>
        <h4>MÁXIMO DE ACTIVACIÓN 2 HORAS, de lo contrario, puedes mandar un mensaje a los correos ya mencionados.</h4>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
    </body>
    </html>

