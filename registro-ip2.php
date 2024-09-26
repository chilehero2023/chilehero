<!DOCTYPE html>
<style type="text/css">
	.registro_de_ip{
		text-align: center;
	}
</style>
</style>
<html lang="es">
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="icon" type="image/png" href="https://github.com/chilehero2023/chilehero/blob/main/icono.png?raw=true"/>
	<title>Registro IP</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" integrity="sha512-z3gLpd7yknf1YoNbCzqRKc4qyor8gaKU1qmn+CShxbuBusANI9QpRohGBreCFkKxLhei6S9CQXFEbbKuqLg0DA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
	<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
	<link href="css/estilos.css" rel="stylesheet">
	<link href="css/background.css" rel="stylesheet">
	<link href="css/fontello.css" rel="stylesheet">
    <link href="css/color.css" rel="stylesheet">
    <link href="css/stylesip.css" rel="stylesheet">
	<script>
    document.addEventListener('contextmenu', function (e) {
      e.preventDefault();
    });
  </script>
</head>
<body class="text-white">
<div class="form">
    <form id="loginForm" action="registroip.php" method="POST">
        <h1 class="titlelogin">Ingresa el nombre y la IP</h1>
        <label>
            <i class="fa-regular fa-user"></i>
            <input type="text" id="nombre" name="nombre" placeholder="Nombre o nick"><br>
        </label>
        <label>
            <i class="fa-solid fa-lock"></i>
            <input type="text" id="ip" name="ip" placeholder="8.8.8.8"><br>
        </label>
      <button type="submit" class="btn btn-primary" id="loginButton" onclick="validateLogin()">Ingresar IP</button>
    </form>
    </div>
</body>
<script src="https://code.jquery.com/jquery-3.7.1.js" integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js" integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</html>