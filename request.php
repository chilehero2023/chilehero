<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $artista = $_POST["artista"];
    $cancion = $_POST["cancion"];
    $email = $_POST["email"];
    $mensaje = $_POST["mensaje"];
    $ip = $_SERVER['REMOTE_ADDR'];

    $destinatario = "smuggling@chilehero.cl";
    
    $asunto = "Pedido de $nombre";

    $cuerpo = "Artista: $artista\n";
    $cuerpo .= "Cancion: $cancion\n";
    $cuerpo .= "Correo: $email\n";
    $cuerpo .= "Mensaje: $mensaje\n";
    $cuerpo .= "IP del remitente: $ip\n";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";
    
    mail($destinatario, $asunto, $cuerpo, $headers);

    header("Location: gracias.html");
    exit();
}
?>