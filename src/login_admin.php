<?php
require '../database/conexion.php';
include 'header_registros.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];

    try {
        $sql = "SELECT * FROM user WHERE user = :user";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':user', $user);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $hashedPassword = $row['password_user'];

            if (password_verify($password, $hashedPassword)) {
                session_start();
                $_SESSION['user'] = $user;
                header("Location: index_admin.php");
                exit();
            } else {
                $message = 'Credenciales Inválidas. Intente de nuevo.';
            }
        } else {
            $message = 'Credenciales Inválidas. Intente de nuevo.';
        }
    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <h1 class="my-5 text-center"><b>Inicio de Sesión de Administrador</b></h1>

    <?php if (!empty($message)) : ?>
        <h2 class="flex justify-center m-5"><?php echo $message; ?></h2>
    <?php endif; ?>

    <form method="POST" class="max-w-md mx-auto p-8 bg-[#E1DDDA] rounded-lg shadow-lg">
        <div>
            <label class="block text-gray-700 text-sm font-bold mb-2">Usuario</label>
            <input type="text" name="user" placeholder="Usuario" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br>

            <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
            <input type="password" name="password" placeholder="Contraseña" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br><br>
        </div>
        <div class="flex justify-center">
            <input class="w-full px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700" type="submit" name="login" value="Iniciar Sesión">
        </div>

    </form>
    <br><br><br>
    <?php
    include 'footer.php';
    ?>

</body>

</html>