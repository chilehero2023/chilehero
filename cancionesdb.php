<?php

// Conexión a la base de datos de usuarios

$servername = "localhost";

$username = "chileher_smuggling";

$password = "aweonaoctm2024";

$dbname = "chileher_chartsoficiales";



$conn = new mysqli($servername, $username, $password, $dbname);



if ($conn->connect_error) {

    die("Conexión fallida: " . $conn->connect_error);

}



// Consulta SQL para obtener toda la información de las imágenes

$sql = "SELECT ID, imagen_nombre, imagen_ruta, Artista, Cancion, Descarga_CH, genero, año, duracion, liricas, instrumentos, link_youtube, Observacion FROM chartssmuggling ORDER BY Artista";

$result = $conn->query($sql);

$sqlContador = "SELECT COUNT(*) AS total_solicitudes FROM chartssmuggling";

$resultContador = $conn2->query($sqlContador);

$totalSolicitudes = 0;

if ($resultContador->num_rows > 0) {

    $rowContador = $resultContador->fetch_assoc();

    $totalSolicitudes = $rowContador["total_solicitudes"];

}



$imagenes = [];



if ($result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        $imagenes[] = $row;

    }

}



$conn->close();

?>



<!DOCTYPE html>

<html lang="es">

<head>

    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="icon" type="image/png" href="https://raw.githubusercontent.com/chilehero2023/chilehero.github.io/main/smugglinglogo2.png"/>

    <title>Canciones</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    <link href="css/estilos.css" rel="stylesheet">

    <link href="css/background.css" rel="stylesheet">

    <link href="css/fontello.css" rel="stylesheet">

    <link href="css/color.css" rel="stylesheet">

    <style>

        .btn-group .btn {

            margin-right: 5px; 

        }

        th {

            text-align: center;

        }

        .not-available {

            color: red;

        }

    </style>

</head>

<body class="bg-light text-white">

<div id="miModal" class="modal fade text-dark">

		<div class="modal-dialog">

			<div class="modal-content">

				<div class="modal-header">

					<h5 class="modal-title">Leer antes de - Read Before</h5>

					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>

				</div>

				<div class="modal-body">

					[ES] Lamentablemente, he quitado la página en ingles de las canciones, ya que me resulta dificil adaptar el idioma a mi base de datos,

                    sin embargo, dejaré aqui abajo los botones para el idioma ingles. Lo siento.<br>



                    [EN] Unfortunately, I have removed the English page for the songs, since it is difficult for me to adapt the language to my database,

                    However, I will leave the buttons for the English language below. I'm sorry.

				</div>

				<div class="modal-footer">

                    <a class="btn btn-danger" href="buy.html" role="button">Go to buy</a>

					<a class="btn btn-danger" href="packs_en.html" role="button">Go to packs</a>

				</div>

			</div>

		</div>

	</div>

	<script>

		window.onload = function() {

			var miModal = new bootstrap.Modal(document.getElementById('miModal'));

			miModal.show();

		};

	</script>

    <nav class="navbar navbar-expand-md navbar-dark bg-dark">

		<div class="container-fluid">

			<a class="navbar-brand" href="index.html"><img src="https://raw.githubusercontent.com/chilehero2023/chilehero.github.io/main/smugglinglogo2.png"class="img-fluid" alt="..."width="40" height="40"></a>

			<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">

      		<span class="navbar-toggler-icon"></span>

      		</button>

      		<div class="collapse navbar-collapse" id="navbarSupportedContent">

      			<ul class="navbar-nav me-auto mb-2 mb-lg-0">

      				<li class="nav-item">

      					<a class="nav-link active" href="#" aria-current="page" >Charts</a>

      				</li>

      				<li class="nav-item">

						<a class="nav-link" href="compra.html">Compra tu canción</a>

					</li>

					<li class="nav-item">

						<a class="nav-link" href="packs.html">Packs</a>

					</li>

					<li class="nav-item">

						<a class="nav-link" href="https://www.chilehero.cl">Chile Hero</a>

					</li>

				</ul>

                <ul class="navbar-nav ms-auto">

                    <li class="nav-item">

                        <a class="btn btn-danger" href="login.php">Accede</a>

                    </li>

                </ul>

			</div>

		</div>

	</nav>

<div class="container mt-5">

    <center><h1>Lista de canciones</h1></center>

    <center><h3>Total canciones: <?php echo $totalSolicitudes; ?></h3></center>

</div>

    <table class="table table-dark table-striped table-bordered">

        <thead>

            <tr>

                <th scope="col">Imagen</th>

                <th scope="col">Artista</th>

                <th scope="col">Canción</th>

                <th scope="col">Género</th>

                <th scope="col">Año</th>

                <th scope="col">Duración</th>

                <th scope="col">Líricas</th>

                <th scope="col">Instrumentos</th>

                <th scope="col">Descargar</th>

                <th scope="col">YouTube</th>

                <th scope="col">Observación</th>

            </tr>

        </thead>

        <tbody>

            <?php foreach ($imagenes as $imagen): ?>

            <tr>

                <td><center><img src="imagenes/<?php echo htmlspecialchars($imagen['imagen_nombre']); ?>" alt="Imagen" style="max-width: 40px;"></center></td>

                <td><?php echo htmlspecialchars($imagen['Artista']); ?></td>

                <td><?php echo htmlspecialchars($imagen['Cancion']); ?></td>

                <td><?php echo htmlspecialchars($imagen['genero']); ?></td>

                <td><?php echo htmlspecialchars($imagen['año']); ?></td>

                <td><?php echo htmlspecialchars($imagen['duracion']); ?></td>

                <td><?php echo htmlspecialchars($imagen['liricas']); ?></td>

                <td><?php echo htmlspecialchars($imagen['instrumentos']); ?></td>

                <td>

                    <?php if (!empty($imagen['Descarga_CH'])): ?>

                        <a href="<?php echo htmlspecialchars($imagen['Descarga_CH']); ?>" class="download-link">Descargar</a>

                    <?php else: ?>

                        <span class="not-available">Próximamente</span>

                    <?php endif; ?>

                </td>

                <td><center>

                    <?php if (!empty($imagen['link_youtube'])): ?>

                        <a href="<?php echo htmlspecialchars($imagen['link_youtube']); ?>" target="_blank">

                            <i class="fab fa-youtube fa-2x"></i>

                        </a>

                    <?php else: ?>

                        <span class="not-available">No disponible</span>

                    <?php endif; ?>

                </center></td>

                <td><?php echo htmlspecialchars($imagen['Observacion']); ?></td>

            </tr>

            <?php endforeach; ?>

        </tbody>

    </table>

</div>



<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>

</body>

</html>

