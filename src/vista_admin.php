<?php
session_start();
// Proceso de logout
if (isset($_POST['logout'])) {
    // Destruir todas las variables de sesión
    session_unset();

    // Destruir la sesión
    session_destroy();

    // Redirigir al inicio de sesión
    header("Location: login_admin.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>
    <h1>Vista Admin</h1>


    <!-- Agrega este código en la página donde deseas tener el botón de logout -->
<form method="POST" class="custom-form">
    <input class="bg-red-600" type="submit" name="logout" value="Cerrar sesión">
</form>

</body>

</html>