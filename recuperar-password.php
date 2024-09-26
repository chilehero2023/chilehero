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
        .register-container {
            max-width: 400px;
            margin: 100px auto;
        }
    </style>
</head>
<body class="text-white">

<div class="register-container">
    <h2>Recuperar Contraseña</h2>
    <form action="recover_password.php" method="post">
        <div class="mb-3">
            <label for="token" class="form-label">Token</label>
            <input type="text" class="form-control" id="token" name="token" required>
        </div>
        <div class="mb-3">
            <label for="correo" class="form-label">Correo</label>
            <input type="email" class="form-control" id="correo" name="correo" required>
        </div>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="mb-3">
            <label for="new_password" class="form-label">Nueva Contraseña</label>
            <input type="password" class="form-control" id="new_password" name="new_password" required>
        </div>
        <button type="submit" class="btn btn-primary">Cambiar Contraseña</button>
    </form>
</div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'];
    $correo = $_POST['correo'];
    $username = $_POST['username'];
    $new_password = $_POST['new_password'];

    $conn = new mysqli('localhost', 'chileher_smuggling', 'aweonaoctm2024', 'chileher_tokens');
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("SELECT * FROM tokens WHERE token = ?");
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 0) {
        echo "<p class='text-danger'>Token inválido.</p>";
        exit;
    }

    $conn->close();

    $conn = new mysqli('localhost', 'chileher_smuggling', 'aweonaoctm2024', 'chileher_registros');
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    $stmt = $conn->prepare("UPDATE registros SET password = ? WHERE username = ? AND correo = ?");
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
    $stmt->bind_param('sss', $hashed_password, $username, $correo);

    if ($stmt->execute()) {
        echo "<p class='text-success'>Contraseña cambiada exitosamente.</p>";
    } else {
        echo "<p class='text-danger'>Error al cambiar la contraseña.</p>";
    }

    $stmt->close();
    $conn->close();
}
?>

</body>
</html>
