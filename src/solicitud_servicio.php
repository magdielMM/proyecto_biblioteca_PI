<?php
require '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $matricula = $_POST['matricula'];
    $servicio = $_POST['servicio'];
    $horaEntrada = date('Y-m-d H:i:s'); // Obtener la hora actual

    try {
        $sql = "INSERT INTO registro (hora_entrada, matricula, id_servicio) 
                VALUES (:horaEntrada, :matricula, :servicio)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':horaEntrada', $horaEntrada);
        $stmt->bindParam(':matricula', $matricula);
        $stmt->bindParam(':servicio', $servicio);
        $stmt->execute();

        if ($stmt->execute()) {
            $message = 'Se ha solicitado el servicio exitosamente';
            $url = "http://localhost/biblioteca/src/index.php";
            $tiempoespera = 1;
            header("refresh: $tiempoespera; url=$url");
            exit(); // Agrega esta línea para evitar que el resto del código se ejecute después de la redirección
        } else {
            $message = 'Ha ocurrido un error al solictar el servicio';
        }
        exit();
    } catch (PDOException $e) {
        die("Error al guardar el registro: " . $e->getMessage());
    }
}

try {
    $sql = "SELECT id_servicio, nombre_servicio FROM servicios";
    $result = $dbh->query($sql); // Use the PDO connection object to execute the query
} catch (PDOException $e) {
    die("Error en la consulta de servicios: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Solicitud de Servicio</title>
    <link rel="stylesheet" href="../style/style.css">
</head>

<body class="">
    <div class="header bg-green-600 flex justify-between">
        <div class="    ">
            <a href="index.php"><button type="button" class="text-white bg-black hover:700 font-bold py-2 px-4 rounded">Inicio</button></a>
        </div>
        <div class="flex justify-center">
            <img class="my-0 transition duration-300 transform hover:scale-110" src="../img/UTTN_princ.png" alt="Logo" style="width: 150px; height: auto;">
        </div>
    </div>
    <h1>Solicitud de Servicio</h1>
    <form method="POST" class="custom-form">
        <label>Matrícula:</label>
        <input type="text" name="matricula" placeholder="Matrícula" required><br><br>

        <?php
        echo '<label>Servicio:</label>';
        echo '<select name="servicio" required id="servicioDropdown">';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { // Use fetch() method to fetch each row
            echo '<option value="' . $row['id_servicio'] . '">' . $row['nombre_servicio'] . '</option>';
        }
        echo '</select>'; ?><br><br>

        <input class="submit-button" type="submit" value="Guardar">
    </form>
    <br><br><br>
    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">© Universidad Tecnológica de Tamaulipas Norte - 2023</p>
        </div>
    </footer>

</body>

</html>
