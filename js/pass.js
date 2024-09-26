function validateLogin() {
  var username = document.getElementById("username").value;
  var password = document.getElementById("password").value;

  if (username === "smuggling" && password === "chilehero") {
    var welcomeMessage = "Bienvenido " + username; // Mensaje de bienvenida personalizado
    alert(welcomeMessage);
    
    // Redireccionar a registro-ip.php
    var formulario = document.getElementById('loginForm');
    formulario.setAttribute("action", "registro-ip.php");
    formulario.submit();
  } else {
    alert("Habla con la persona que te proporcionó el acceso");
    
    // Redireccionar a index.html
    var formulario = document.getElementById('loginForm');
    formulario.setAttribute("action", "index.html");
    formulario.submit();
  }
}