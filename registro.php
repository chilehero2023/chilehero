<?php

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $nombre = $_POST['nombre'];

    $correo = $_POST['correo'];

    $contraseña = $_POST['contraseña'];

    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    $fecha_nacimiento = $_POST['fecha_nacimiento'];

    $token = $_POST['token'];

    $fecha_registro = date("Y-m-d H:i:s");

    $rol = "Usuario"; // Asignar rol por defecto

    $ip_usuario = $_SERVER['REMOTE_ADDR']; // Obtener la IP del usuario



    // Datos para la conexión a la base de datos principal

    $servername = "localhost";

    $username = "chileher_smuggling";

    $password = "aweonaoctm2024";

    $dbname = "chileher_usuariosregistrados";



    // Conexión a la base de datos principal

    $conn = new mysqli($servername, $username, $password, $dbname);



    if ($conn->connect_error) {

        die("Conexión fallida: " . $conn->connect_error);

    }



    // Verificar si el usuario ya está registrado

    $sql_check_user = "SELECT * FROM usuarios WHERE nombre = ?";

    $stmt_check_user = $conn->prepare($sql_check_user);

    $stmt_check_user->bind_param("s", $nombre);

    $stmt_check_user->execute();

    $result_check_user = $stmt_check_user->get_result();



    if ($result_check_user->num_rows > 0) {

        $message = "Usuario en uso.";

    } else {

        // Verificar si el correo ya está registrado

        $sql_check_email = "SELECT * FROM usuarios WHERE correo = ?";

        $stmt_check_email = $conn->prepare($sql_check_email);

        $stmt_check_email->bind_param("s", $correo);

        $stmt_check_email->execute();

        $result_check_email = $stmt_check_email->get_result();



        if ($result_check_email->num_rows > 0) {

            $message = "Correo en uso.";

        } else {

            if ($contraseña !== $confirmar_contraseña) {

                $message = "Las contraseñas no coinciden.";

            } else {

                // Datos para la conexión a la base de datos de tokens

                $token_dbname = "chileher_tokens2";

                $token_conn = new mysqli($servername, $username, $password, $token_dbname);



                if ($token_conn->connect_error) {

                    die("Conexión fallida: " . $token_conn->connect_error);

                }



                $token_query = "SELECT * FROM token WHERE registrotoken = ?";

                $stmt_token = $token_conn->prepare($token_query);

                $stmt_token->bind_param("s", $token);

                $stmt_token->execute();

                $token_result = $stmt_token->get_result();



                if ($token_result->num_rows > 0) {

                    // Continuar con el registro del usuario

                    $hashed_password = hash('sha256', $contraseña);



                    // Insertar el usuario en la base de datos principal

                    $sql = "INSERT INTO usuarios (nombre, correo, contraseña, fecha_nacimiento, fecha_registro, rol, ip) 

                            VALUES (?, ?, ?, ?, ?, ?, ?)";



                    $stmt = $conn->prepare($sql);

                    $stmt->bind_param("sssssss", $nombre, $correo, $hashed_password, $fecha_nacimiento, $fecha_registro, $rol, $ip_usuario);



                    if ($stmt->execute()) {

                        $message = "Usuario registrado exitosamente. Te enviaremos un correo con tus datos (sin tu contraseña, ya que está encriptada)";

                    } else {

                        $message = "Error: " . $stmt->error;

                    }



                    $stmt->close();

                } else {

                    $message = "Token inválido.";

                }



                $stmt_token->close();

                $token_conn->close();

            }

        }



        $stmt_check_email->close();

    }



    $stmt_check_user->close();

    $conn->close();

}

?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>

    <title>Registro</title>

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

        .token{

            display: none;

        }

    </style>

</head>

<body class="text-white">

    <div id="miModal" class="modal fade text-dark">

        <div class="modal-dialog">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">Leer antes de registrarte</h5>

                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

                </div>

                <div class="modal-body">

                    <p>Gracias a todos por sus registros en la página y por su apoyo en el proyecto.<br>

                    <font color="red">Sin embargo he visto que hay correos de dudosa procedencia, por lo tanto esas cuentas serán <b>baneadas</b>, lo mismo que las multicuentas.

                        En caso de las multicuentas, serán notificadas a sus correos respectivos, y en caso de que el correo rebote, seran baneadas las todas las cuentas (multicuentas) <br>
                        <b>LEER BIEN LAS REGLAS, PORQUE SI TE REGISTRAS CON MÁS DE 3 CUENTAS, YA SE BANEA DE PÁGINA Y DE IP; ESTE BANEO ES INAPELABLE.</b><br>

                    Ya no será necesario el token, para el registro, el token está incluido de forma automatica, sin embargo si es que se te olvida tu contraseña ahi sí, deberás pedir tu token. <br>

                    Smuggling</p>

                </div>

            </div>

        </div>

    </div>

    <div class="container mt-5">

        <div class="row justify-content-center">

            <div class="col-md-6">

                <div class="card">

                    <div class="card-body bg-dark2 text-white">

                        <h2 class="card-title mb-4">Registro de Usuario</h2>

                        <?php if ($message !== ""): ?>

                            <div class="alert alert-primary" role="alert">

                                <?php echo $message; ?>

                            </div>

                        <?php endif; ?>

                        <center><form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">

                            <div class="mb3">

                                <label for="nombre" class="form-label">Nombre:</label>

                                <input type="text" class="form-control" id="nombre" name="nombre" required style="width: 400px;">

                            </div>

                            <div class="mb-3">

                                <label for="correo" class="form-label">Correo Electrónico:</label>

                                <input type="email" class="form-control" id="correo" name="correo" required style="width: 400px;">

                            </div>

                            <div class="mb-3">

                                <label for="contraseña" class="form-label">Contraseña:</label>

                                <input type="password" class="form-control" id="contraseña" name="contraseña" required style="width: 400px;">

                            </div>

                            <div class="mb-3">

                                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña:</label>

                                <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" required style="width: 400px;">

                            </div>

                            <div class="mb-3">

                                <label for="fecha_nacimiento" class="form-label">Fecha de Nacimiento:</label>

                                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento" required style="width: 400px;">

                            </div>

                            <div class="mb-3">

                                <label for="token" class="form-label token">Token:</label>

                                <input type="text" class="form-control token" readonly id="token" name="token" required style="width: 400px;" value="b6?6AxV0xGk1vIAG9qigq8EG4-g7grumvvi9pERZ7gDI3M!n37eK?Ehlu0Le8saSvy4hvldVGpgAGCmaY90trIo7caXMBqdx/M?e/KIYVvJJ2a!0!9vwkWTHE=jRnOblg?rIQBWgJs6pn33O/yT8=djeyE!OWTN=V9ah25Qt0Tli19BL?vBgGKj9IVn?sU95=X!Gc5VT8/6U-Djo=OTII1?/0p9ctSifOT?lNmaKkwjtdy!XAc/92OX4v3!rMXco">

                            </div>

                            <div class="text-center">

                                <button type="submit" class="btn btn-primary">Registrarse</button>

                                <p>Al registrarse aceptas las reglas de la comunidad de Chile Hero, si no las conoces las puedes <a href="reglas.php">ver aquí</a></p>

                            </div>

                        </form></center>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

    <script>

        document.addEventListener('DOMContentLoaded', function () {

            var myModal = new bootstrap.Modal(document.getElementById('miModal'));

            myModal.show();

        });

    </script>

</body>

</html>

