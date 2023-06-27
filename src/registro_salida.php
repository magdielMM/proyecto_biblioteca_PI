<?php
require '../database/conexion.php';

$message = '';

try {
    $sql = "SELECT id_registro, hora_entrada, hora_salida, matricula, id_servicio FROM registro";
    $result = $dbh->query($sql); // Use the PDO connection object to execute the query
} catch (PDOException $e) {
    die("Error en la consulta de servicios: " . $e->getMessage());
}

if (isset($_POST['marcarSalida'])) {
    $registroId = $_POST['registroId'];
    $fechaHoraSalida = date('Y-m-d H:i:s');

    // Actualizar el campo de hora_salida en la base de datos para el registro correspondiente
    $updateSql = "UPDATE registro SET hora_salida = :horaSalida WHERE id_registro = :registroId";
    $updateStmt = $dbh->prepare($updateSql);
    $updateStmt->bindParam(':horaSalida', $fechaHoraSalida);
    $updateStmt->bindParam(':registroId', $registroId);
    $updateStmt->execute();

    // Redireccionar a la misma página para mostrar la tabla actualizada
    header('Location: registro_salida.php');
    exit();
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro Salida</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <div class="header bg-green-600 flex">
        <div class="">
            <a href="index.php"><button type="button" class="text-white bg-black hover:700 font-bold py-2 px-4 rounded">Inicio</button></a>
        </div>
        <div class="flex justify-center">
            <img class="my-0 transition duration-300 transform hover:scale-110" src="../img/UTTN_princ.png" alt="Logo" style="width: 150px; height: auto;">
        </div>
    </div>
    <h1 class="m-20">Registrar Salida</h1>
    <table class="table-flex">
        <thead class="bg-black font-bolde">
            <tr>
                <th class="px-4 py-2 text-center text-white">Matrícula</th>
                <th class="px-4 py-2 text-center text-white">Servicio</th>
                <th class="px-4 py-2 text-center text-white">Hora Entrada</th>
                <th class="px-4 py-2 text-center text-white">Marcar Salida</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)) { ?>
                <tr class="bg-slate-500">
                    <td class="px-4 py-2"><?php echo $row['matricula']; ?></td>
                    <?php
                    $servicioId = $row['id_servicio'];
                    $servicioSql = "SELECT nombre_servicio FROM servicios WHERE id_servicio = :servicioId";
                    $servicioStmt = $dbh->prepare($servicioSql);
                    $servicioStmt->bindParam(':servicioId', $servicioId);
                    $servicioStmt->execute();
                    $servicioRow = $servicioStmt->fetch(PDO::FETCH_ASSOC);
                    $nombreServicio = $servicioRow['nombre_servicio'];
                    ?>
                    <td class="px-4 py-2"><?php echo $nombreServicio; ?></td>
                    <td class="px-4 py-2"><?php echo $row['hora_entrada']; ?></td>
                    <td class="px-4 py-2">
                        <?php if (!empty($row['hora_salida'])) { ?>
                            <button type="button" class="text-white bg-black hover:700 font-bold py-2 px-4 rounded" onclick="registrarSalida(<?php echo $row['id_registro']; ?>)">Salida</button>
                        <?php } else { ?>
                            <h2>No se puede encontrar la salida</h2>
                        <?php } ?>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">© Universidad Tecnológica de Tamaulipas Norte - 2023</p>
        </div>
    </footer>
    <script>
        function registrarSalida(registroId) {
            // Envía una solicitud AJAX al servidor para guardar la hora de salida
            var xhttp = new XMLHttpRequest();
            xhttp.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    // La solicitud se completó correctamente, puedes realizar acciones adicionales si es necesario
                    // ...
                    // Recarga la página para mostrar la tabla actualizada
                    location.reload();
                }
            };
            xhttp.open("POST", "logica_salida.php", true);
            xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhttp.send("marcarSalida=1&registroId=" + registroId);
        }
    </script>
</body>



</html>