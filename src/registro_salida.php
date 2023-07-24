<?php
require '../database/conexion.php';

$message = '';
$search_result = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_matricula'])) {
    $search_matricula = $_GET['search_matricula'];
    if (!empty($search_matricula) && strlen($search_matricula) === 10) {
        $searchSql = "SELECT id_registro, hora_entrada, hora_salida, matricula, id_servicio FROM registro WHERE matricula LIKE :matricula AND status = 0";
        $searchStmt = $dbh->prepare($searchSql);
        $searchStmt->bindValue(':matricula', '%' . $search_matricula . '%');
        try {
            $searchStmt->execute();
            $search_result = $searchStmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            die("Error al buscar el registro: " . $e->getMessage());
        }
    } else {
        $message = "Ingrese su matrícula completa para realizar la búsqueda.";
    }
}

try {
    $sql = "SELECT id_registro, hora_entrada, hora_salida, matricula, id_servicio FROM registro WHERE status = 0";
    $result = $dbh->query($sql); // Use the PDO connection object to execute the query
} catch (PDOException $e) {
    die("Error en la consulta de servicios: " . $e->getMessage());
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
    <?php include 'header_registros.php'; ?>
    <h1 class="my-5 text-center text-2xl font-bold"><b>Registro de Salida</b></h1>
    <div class="flex flex-col items-center justify-center mb-5">
        <form method="GET" class="flex flex-col sm:flex-row">
            <label class="rounded #E1DDDA p-2"><b>Busca tu Matrícula:</b></label>
            <input type="search" name="search_matricula" id="search-input" placeholder="Buscar" class="border-black rounded bg-gray-100 p-2 text-black placeholder:text-black mt-3 sm:mt-0 sm:ml-3" maxlength="10" required value="<?php echo isset($search_matricula) ? htmlspecialchars($search_matricula) : ''; ?>">
            <button type="submit" class="px-4 py-2 mt-3 sm:mt-0 sm:ml-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded">Buscar</button>
        </form>
    </div>

    <?php if (!empty($search_result)) : ?>
        <!-- Mostrar tabla de resultados de búsqueda solo si se encontraron registros -->
        <div class="flex items-center justify-center mb-10">
            <table class="w-full m-10">
                <thead class="bg-gray-800">
                    <tr class="bg-">
                        <th class="p-3 text-sm font-semibold tracking-wide text-center text-white">Matrícula</th>
                        <th class="p-3 text-sm font-semibold tracking-wide text-center text-white">Servicio</th>
                        <th class="p-3 text-sm font-semibold tracking-wide text-center text-white">Hora Entrada</th>
                        <th class="p-3 text-sm font-semibold tracking-wide text-center text-white">Marcar Salida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($search_result as $row) : ?>
                        <tr class="bg-white">
                            <td class="text-center p-3 text-sm text-gray-700"><?php echo $row['matricula']; ?></td>
                            <?php
                            $servicioId = $row['id_servicio'];
                            $servicioSql = "SELECT nombre_servicio FROM servicios WHERE id_servicio = :servicioId";
                            $servicioStmt = $dbh->prepare($servicioSql);
                            $servicioStmt->bindParam(':servicioId', $servicioId);
                            $servicioStmt->execute();
                            $servicioRow = $servicioStmt->fetch(PDO::FETCH_ASSOC);
                            $nombreServicio = $servicioRow['nombre_servicio'];
                            ?>
                            <td class="text-center p-3 text-sm text-gray-700"><?php echo $nombreServicio; ?></td>
                            <td class="text-center p-3 text-sm text-gray-700"><?php echo $row['hora_entrada']; ?></td>
                            <td class="text-center p-3 text-sm text-gray-700">
                                <?php if (!empty($row['hora_salida'])) { ?>
                                    <button type="button" class="w-full px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700" onclick="registrarSalida(<?php echo $row['id_registro']; ?>)">Salida</button>
                                <?php } else { ?>
                                    <h2>No se puede encontrar la salida</h2>
                                <?php } ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif (!empty($message)) : ?>
        <!-- Mostrar mensaje de error si la matrícula ingresada no tiene 10 caracteres -->
        <div class="flex items-center justify-center mb-10">
            <h1><?php echo $message; ?></h1>
        </div>
    <?php endif; ?>

    <?php
    include 'footer.php';
    ?>
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
        var inputMatricula = document.querySelector('#search-input');
        inputMatricula.addEventListener('input', borrarMatricula);

        function borrarMatricula() {
            // Verificar si el contenido está vacío
            if (inputMatricula.value === '') {
                // Envía el formulario de búsqueda
                document.querySelector('form').submit();
            }
        }
    </script>
</body>

</html>
