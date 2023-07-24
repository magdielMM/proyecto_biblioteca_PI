<?php
require '../database/conexion.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login_admin.php");
    exit();
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];

    // Aplicar el método hash a la contraseña
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    try {
        $checkSql = "SELECT COUNT(*) FROM user WHERE user = :user";
        $checkStmt = $dbh->prepare($checkSql);
        $checkStmt->bindParam(':user', $user);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count == 0) {
            $sql = "INSERT INTO user (user, password_user) VALUES (:user, :password)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':user', $user);
            $stmt->bindParam(':password', $hashedPassword);

            if ($stmt->execute()) {
                $message = 'Registro exitoso.';
                $message = 'Se ha creado exitosamente el usuario';
                $url = "http://localhost/biblioteca/src/index_admin.php";
                $tiempoespera = 2;
                header("refresh: $tiempoespera; url=$url");
                exit();
            } else {
                $message = 'Ha ocurrido un error durante el registro.';
            }
        } else {
            $message = 'El usuario ya existe. Intente con otro nombre de usuario.';
        }
    } catch (PDOException $e) {
        die("Error en la base de datos: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro de Administrador</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <header>
        <div class="header-bar">
            <div class="flex justify-center">
                <div class="flex items-start justify-start"><a href="index_admin.php"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="44" height="44" viewBox="0 0 24 24" stroke-width="3" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M5 12l14 0" />
                            <path d="M5 12l6 6" />
                            <path d="M5 12l6 -6" />
                        </svg></a></div>

                <img src="../img/Image.jpeg" alt="Logo" id="logo">

            </div>
        </div>
    </header>
    <h1 class="my-5 text-center"><b>Registro de Administrador</b></h1>

    <?php if (!empty($message)) : ?>
        <h2 class="flex justify-center m-5"><?php echo $message; ?></h2>
    <?php endif; ?>

    <form method="POST" class="max-w-md mx-auto p-8 bg-[#E1DDDA] rounded-lg shadow-lg">

        <label class="block text-gray-700 text-sm font-bold mb-2">Usuario</label>
        <input type="text" name="user" placeholder="Usuario" class="w-full px-3 py-2 placeholder-gray-300 border
         rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br>

        <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
        <input type="password" name="password" placeholder="Contraseña" class="w-full px-3 py-2 placeholder-gray-300 
        border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br><br>

        <div class="flex justify-center">
            <input class="w-full px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700" type="submit" name="register" value="Registrarse">
        </div>

    </form>
    <br><br><br>
    <?php
    include 'footer.php';
    ?>

</body>

</html>
