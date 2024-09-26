<?php
// Verificar si se ha enviado el formulario de inicio de sesión
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Conectar a la base de datos
    $servername = "localhost";
    $dbUsername = "chileher_smuggling";
    $dbPassword = "aweonaoctm2024";
    $dbname = "chileher_registrosusuarios";

    $conn = new mysqli($servername, $dbUsername, $dbPassword, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Consulta para verificar las credenciales del usuario
    $sql = "SELECT * FROM cuentasregistradas WHERE Username = '$username'";
    $result = $conn->query($sql);

    // Verificar si se encontró un usuario con el nombre proporcionado
    if ($result->num_rows > 0) {
        // Obtener la fila de resultados como un array asociativo
        $row = $result->fetch_assoc();

        // Verificar si la contraseña proporcionada coincide con la contraseña almacenada
        if (password_verify($password, $row['Password'])) {
            // Iniciar sesión
            session_start();
            $_SESSION['username'] = $username; // Guardar el nombre de usuario en la sesión

            // Redirigir a la página de inicio
            header("Location: inicio.php");
            exit();
        } else {
            // Contraseña incorrecta
            echo "Contraseña incorrecta. Por favor, vuelva a intentarlo.";
        }
    } else {
        // Usuario no encontrado
        echo "El nombre de usuario '$username' no existe.";
    }

    // Cerrar la conexión a la base de datos
    $conn->close();
}
?>
