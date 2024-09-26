<?php
$conexion = mysqli_connect("localhost:3306", "chileher_smuggling", "aweonaoctm2024", "chileher_comentarios2");

if (mysqli_connect_errno()) {
     echo "Error al conectar a MySQL: " . mysqli_connect_error();
     exit();
}

$nombre = $_POST['nombre'];
$comentario = $_POST['comentario'];
$ip = $_SERVER['REMOTE_ADDR'];

$query = "INSERT INTO comentarios (nombre, comentario, ip) VALUES ('$nombre', '$comentario', '$ip')";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
    echo "Comentario enviado correctamente.";
    } else {
        echo "Error al enviar el comentario: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>