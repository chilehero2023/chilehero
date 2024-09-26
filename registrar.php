<?php
// Conexión a la base de datos (reemplaza con tus datos)
$servername = "localhost";
$username = "chileher_smuggling";
$password = "aweonaoctm2024";
$dbname = "chileher_registros"; // Base de datos de usuarios

// Conexión a la base de datos de tokens (reemplaza con tus datos)
$token_dbname = "chileher_tokens"; 

$conn = new mysqli($servername, $username, $password, $dbname);
$token_conn = new mysqli($servername, $username, $password, $token_dbname);

if ($conn->connect_error || $token_conn->connect_error) {
    die("Error de conexión: " . ($conn->connect_error ?? $token_conn->connect_error));
}

// Obtener datos del formulario
$id = $_POST['id'];
$usuario = $_POST['usuario'];
$contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);
$confirmar_contrasena = $_POST['confirmar_contrasena'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$genero = $_POST['genero'];
$token = $_POST['token'];
$rol = $_POST['rol']; 

// Validación de token
$sql_token = "SELECT * FROM tokens2 WHERE token = '$token'";
$result = $token_conn->query($sql_token);

if ($result->num_rows > 0) {
    // Token válido, proceder con el registro
    $sql = "INSERT INTO usuarios (id, usuario, contrasena, fecha_nacimiento, genero, rol)
    VALUES ('$id', '$usuario', '$contrasena', '$fecha_nacimiento', '$genero', '$rol')";

    if ($conn->query($sql) === TRUE) {
        echo "Registro exitoso!";
    } else {
        echo "Error al registrar: " . $conn->error;
    }
} else {
    echo "Token inválido. Registro no permitido.";
}

$conn->close();
$token_conn->close();
?>
