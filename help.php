<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $youtube = $_POST["youtube"];
    $chart = $_POST["chart"];
    $discord = $_POST["discord"];
    $email = $_POST["email"];

    $destinatario = "smuggling@chilehero.cl";

    $asunto = "Te quiere ayudar $nombre";

    $cuerpo = "Nombre: $nombre\n";
    $cuerpo .= "Youtube:$youtube\n";
    $cuerpo .= "Mi chart:$chart\n";
    $cuerpo .= "Discord:$discord\n";
    $cuerpo .= "Correo: $email\n";

    $headers = "From: $email\r\n";
    $headers .= "Reply-To: $email\r\n";

    mail($destinatario, $asunto, $cuerpo, $headers);

    header("Location: gracias2.html");
    exit();
}
?>