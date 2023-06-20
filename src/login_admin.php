<?php
require '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM user WHERE user = :user AND password_user = :password";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user', $user);
        $stmt->bindParam(':password', $password);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Credenciales válidas, redirigir al usuario a la página de administrador
            header("Location: vista_admin.php");
            exit();
        } else {
            $message = 'Credenciales incorrectas. Por favor, inténtalo de nuevo.';
        }
    } catch (PDOException $e) {
        die("Error al consultar la base de datos: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Inicio de Sesion</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body>
    <div class="header bg-green-600 flex justify-between">
        <div class="    ">
            <a href="index.php"><button type="button" class="text-white bg-black hover:700 font-bold py-2 px-4 rounded">Inicio</button></a>
        </div>
        <div class="flex justify-center mg-lg-20">
            <img class="my-0 transition duration-300 transform hover:scale-110" src="../img/UTTN_princ.png" alt="Logo" style="width: 150px; height: auto;">
        </div>
    </div>
    <h1>Inicio de Sesión</h1>

    <?php if (isset($message)) : ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST" class="custom-form" action="vista_admin.php">

        <label class="content-center">Usuario</label>
        <input type="text" name="user" placeholder="Usuario" required><br><br>

        <label>Contraseña</label>
        <input type="password" name="password" placeholder="Contraseña" required><br><br><br>

        <div class="justify-center">
            <input class="submit-button" type="submit" name="login" value="Iniciar Sesión">
        </div>

    </form>
    <br><br><br>
    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">© Universidad Tecnológica de Tamaulipas Norte - 2023</p>
        </div>
    </footer>

</body>

</html>