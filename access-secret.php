<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
    <title>Página Oficial de Chile Hero</title>
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
        /* Estilos personalizados */
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body class="text-white">
    <div class="container login-container">
        <h2 class="text-center">Registro</h2>

        <?php
        $servername = "localhost";
        $username = "chileher_smuggling";
        $password = "aweonaoctm2024";
        $dbname = "chileher_tokens";

        $conn = new mysqli($servername, $username, $password, $dbname);

        if ($conn->connect_error) {
            die("Conexión fallida: " . $conn->connect_error);
        } 

        $message = ""; // Variable para mostrar mensajes

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $token = $_POST["token"];
            $username = $_POST["username"];
            $password = $_POST["password"];
            $confirm_password = $_POST["confirm_password"];
            $fecha_nacimiento = $_POST["fecha_nacimiento"];
            $genero = $_POST["genero"];
            $correo = $_POST["correo"];

            // Validación del token
            $stmt = $conn->prepare("SELECT * FROM usuarios WHERE Token = ?");
            $stmt->bind_param("s", $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                // Token válido, procede con la inserción
                if($password === $confirm_password){
                    // Hashea la contraseña antes de insertarla (importante)
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Inserta el nuevo usuario
                    $insert_stmt = $conn->prepare("INSERT INTO usuarios (Username, Password, Fecha_Nacimiento, Genero, Token, Correo) VALUES (?, ?, ?, ?, ?, ?)");
                    $insert_stmt->bind_param("ssssss", $username, $hashed_password, $fecha_nacimiento, $genero, $token, $correo);

                    if ($insert_stmt->execute()) {
                        $message = "Registro exitoso."; 
                    } else {
                        $message = "Error al registrar: " . $conn->error;
                    }
                } else {
                    $message = "Las contraseñas no coinciden";
                }
            } else {
                $message = "Token inválido. Inténtalo de nuevo.";
            }

            $stmt->close();
        }

        $conn->close();
        ?>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info" role="alert">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="form-group">
                <label for="token">Token:</label>
                <input type="text" class="form-control" id="token" name="token" required>
            </div>

            <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>

            <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirmar contraseña:</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>

            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de nacimiento:</label>
                <input type="date" class="form-control" id="fecha_nacimiento" name="fecha_nacimiento">
            </div>

            <div class="form-group">
                <label for="genero">Género:</label>
                <select class="form-control" id="genero" name="genero">
                    <option value="Masculino">Masculino</option>
                    <option value="Femenino">Femenino</option>
                    <option value="Otro">Otro</option>
                </select>
            </div>
            <div class="form-group">
                <label for="correo">Correo:</label>
                <input type="email" class="form-control" id="correo" name="correo">
            </div>
            <br>
            <button type="submit" class="btn btn-primary">Registrarse</button>
            <p>¿Tienes cuenta? <a href="login.php" class="text-white">Accede aquí</a></p> 
        </form>
    </div>
</body>
</html>
