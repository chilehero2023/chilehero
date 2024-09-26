<?php
$conexion = mysqli_connect("localhost:3306", "chileher_smuggling", "aweonaoctm2024", "chileher_comentarios2");
if (mysqli_connect_errno()) {
     echo "Error al conectar a MySQL: " . mysqli_connect_error();
     exit();
}
$query = "SELECT nombre, comentario, fecha FROM comentarios ORDER BY fecha DESC";
$resultado = mysqli_query($conexion, $query);

while ($fila = mysqli_fetch_assoc($resultado)) {
    echo "<div style='border: 1px solid #ccc; padding: 10px; margin-bottom: 10px;'>";
    echo "<strong>" . $fila['nombre'] . "</strong> el " . $fila['fecha'] . ":<br>";
    echo $fila['comentario'] . "<br>";
    echo "</div>";

}

mysqli_free_result($resultado);
mysqli_close($conexion);
?>