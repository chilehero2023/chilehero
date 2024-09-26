<?php
if (isset($_POST['id_eliminar'])) {
    $id_eliminar = $_POST['id_eliminar'];

    $conexion = mysqli_connect("localhost:3306", "chileher_smuggling", "aweonaoctm2024", "chileher_requestip");

    if (mysqli_connect_errno()) {
        echo "Error al conectar a MySQL: " . mysqli_connect_error();
        exit();
    }

    $query_eliminar = "DELETE FROM registro_ip WHERE id = '$id_eliminar'";
    $resultado_eliminar = mysqli_query($conexion, $query_eliminar);

    if ($resultado_eliminar) {
        echo "IP eliminada correctamente.";
    } else {
        echo "Error al eliminar la IP: " . mysqli_error($conexion);
    }

    mysqli_close($conexion);
}
?>
