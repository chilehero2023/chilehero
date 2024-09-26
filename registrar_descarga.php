<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header("Location: login.php");
    exit();
}

$servername2 = "localhost";
$username2 = "chileher_smuggling";
$password2 = "aweonaoctm2024";
$dbname2 = "chileher_chartsoficiales";

$conn2 = new mysqli($servername2, $username2, $password2, $dbname2);

if ($conn2->connect_error) {
    die("Conexión fallida: " . $conn2->connect_error);
}

// Recibir ID de la canción y plataforma (desde la solicitud AJAX)
$idCancion = $_POST['id'];
$plataforma = $_POST['plataforma'];

// Incrementar contador en la base de datos
$sqlUpdate = "UPDATE chartsoficiales SET Descargas = Descargas + 1, Fecha_Descarga = NOW() WHERE ID = $idCancion"; // Actualizar la fecha de descarga
$conn2->query($sqlUpdate);
