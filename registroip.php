<?php
$conexion = mysqli_connect("localhost:3306", "chileher_smuggling", "aweonaoctm2024", "chileher_requestip");

if (mysqli_connect_errno()) {
    echo "Error al conectar a MySQL: " . mysqli_connect_error();
    exit();
}

$nombre = $_POST['nombre'];
$ip = $_POST['ip'];

$query = "INSERT INTO registro_ip (nombre, ip) VALUES ('$nombre', '$ip')";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
    echo "IP Ingresado correctamente";
    } else {
        echo "Error al enviar el comentario: " . mysqli_error($conexion);
}

mysqli_close($conexion);
?>