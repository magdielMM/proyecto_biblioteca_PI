<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login_admin.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vista de Administrador</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>
<body>
    <div class="header bg-green-600 h-20 items-center justify-center px-10">
        <img class="h-16 w-auto" src="../img/UTTN_princ.png" alt="Logo" style="width: 150px;" height="auto;">
        <a href="logout.php" class="text-white bg-black hover:bg-gray-700 font-bold py-2 px-4 rounded">Cerrar Sesión</a>
    </div>
    <h1>Vista Admin</h1>
    
</body>
</html>
