<?php
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = $_POST['correo'];
    $token = $_POST['token'];
    $nueva_contraseña = $_POST['nueva_contraseña'];
    $confirmar_contraseña = $_POST['confirmar_contraseña'];

    if ($nueva_contraseña !== $confirmar_contraseña) {
        $message = "Las contraseñas no coinciden.";
    } else {
        $hashed_password = hash('sha256', $nueva_contraseña);

        $servername = "localhost";
        $username = "chileher_smuggling";
        $password = "aweonaoctm2024";
        $token_dbname = "chileher_tokens2";
        $token_conn = new mysqli($servername, $username, $password, $token_dbname);

        if ($token_conn->connect_error) {
            die("Conexión fallida: " . $token_conn->connect_error);
        }

        $token_query = "SELECT * FROM token WHERE registrotoken = ?";
        $stmt = $token_conn->prepare($token_query);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {

            $stmt->close();
            $token_conn->close();

            $user_dbname = "chileher_usuariosregistrados";
            $user_conn = new mysqli($servername, $username, $password, $user_dbname);

            if ($user_conn->connect_error) {
                die("Conexión fallida: " . $user_conn->connect_error);
            }

            $update_query = "UPDATE usuarios SET contraseña = ? WHERE correo = ?";
            $stmt = $user_conn->prepare($update_query);
            $stmt->bind_param("ss", $hashed_password, $correo);

            if ($stmt->execute()) {
                $message = "Contraseña actualizada exitosamente.";
            } else {
                $message = "Error al actualizar la contraseña: " . $stmt->error;
            }

            $stmt->close();
            $user_conn->close();
        } else {
            $message = "Token inválido.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Recuperar Contraseña</title>
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
<div id="miModal" class="modal fade text-dark">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title">Leer antes de recuperar contraseña</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
                    <h5>ATENCIÓN: LAS CONTRASEÑAS ESTAN ENCRIPTADAS, NO SE PUEDE VER, NI EL ADMINISTRADOR PUEDE, ASI QUE NO PREGUNTEN POR SUS CONTRASEÑAS</h5>
					<p>Antes de recuperar tu contraseña, deberas mandarme un correo a smuggling@chilehero.cl, con Asunto: "Perdi mi contraseña" y en el mensaje, mandar tu correo y tu usuario.<br>
                    Como lo mencioné en los registros, esto será momentaneo, en un futuro lo haré de forma automatica.<br>
                    Atte: <br>
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
                        <h2 class="card-title mb-4">Recuperar Contraseña</h2>
                        <?php if ($message !== ""): ?>
                            <div class="alert alert-primary" role="alert">
                                <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Electrónico:</label>
                                <input type="email" class="form-control" id="correo" name="correo" required>
                            </div>
                            <div class="mb-3">
                                <label for="token" class="form-label">Token:</label>
                                <input type="text" class="form-control" id="token" name="token" required>
                            </div>
                            <div class="mb-3">
                                <label for="nueva_contraseña" class="form-label">Nueva Contraseña:</label>
                                <input type="password" class="form-control" id="nueva_contraseña" name="nueva_contraseña" required>
                            </div>
                            <div class="mb-3">
                                <label for="confirmar_contraseña" class="form-label">Confirmar Contraseña:</label>
                                <input type="password" class="form-control" id="confirmar_contraseña" name="confirmar_contraseña" required>
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Actualizar Contraseña</button>
                            </div>
                        </form>
                        <div class="mt-4 text-center">
                            <p>¿No tienes cuenta? <a href="registro.php" class="text-primary">Regístrate aquí</a></p>
                            <p>¿Tienes cuenta? <a href="login.php" class="text-primary">Accede aquí</a></p>
                        </div>
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
