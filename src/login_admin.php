<?php
require '../database/conexion.php';
include 'header_registros.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = $_POST['user'];
    $password = $_POST['password'];

    try {
        // Llamar al procedimiento almacenado para obtener el hash de la contraseña
        $stmt = $dbh->prepare("CALL GetPasswordHash(:user, @password_hash)");
        $stmt->bindParam(':user', $user);
        $stmt->execute();

        // Obtener el hash de la contraseña del resultado del procedimiento almacenado
        $stmt = $dbh->query("SELECT @password_hash AS password_hash");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $hashedPassword = $result['password_hash'];

        // Verificar la contraseña utilizando password_verify() en PHP
        if (password_verify($password, $hashedPassword)) {
            session_start();
            $_SESSION['user'] = $user;
            header("Location: index_admin.php");
            exit();
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
            <input type="text" name="user" id="usuario" placeholder="Usuario" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br>

            <label class="block text-gray-700 text-sm font-bold mb-2">Contraseña</label>
            <input type="password" id="contraseña" name="password" placeholder="Contraseña" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600" required><br><br><br>
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

<script>
    document.addEventListener('DOMContentLoaded', function(){
    //Sleccionar los elementos
    const inputUsuario = document.querySelector('#usuario');
    const inputContra = document.querySelector('#contraseña');
    inputUsuario.addEventListener('blur', validar);
    inputContra.addEventListener('blur', validar)

    function validar(e){

        if (e.target.value.trim() === '') {
            mostrarAlerta(`El campo ${e.target.id} es obligatorio`, e.target.parentElement);
            return;
        } limpiarAlerta(e.target.parentElement);
    }
    function mostrarAlerta(mensaje, referencia) {
        limpiarAlerta(referencia);
        const error = document.createElement('P');
        error.textContent = mensaje;
        error.classList.add('bg-red-600', 'text-white', 'p-2', 'text-center');
        referencia.appendChild(error);
    }

    function limpiarAlerta(referencia){
        const alerta = referencia.querySelector('.bg-red-600');
        if (alerta) {
            alerta.remove();
        }
        console.log('desde limpiar alerta');
    }
});
</script>

</html>