<?php
require '../database/conexion.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login_admin.php");
    exit();
}

$startTime = isset($_POST['start_time']) ? $_POST['start_time'] : '';
$endTime = isset($_POST['end_time']) ? $_POST['end_time'] : '';
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : '';

try {
    if (!empty($startTime) && !empty($endTime)) {
        $sql = "SELECT r.id_registro, r.hora_entrada, r.hora_salida, r.matricula, r.nombre, e.nombre_especialidad, s.nombre_servicio
        FROM registro r
        INNER JOIN especialidades e ON r.id_especialidad = e.id_especialidad
        INNER JOIN servicios s ON r.id_servicio = s.id_servicio
        WHERE r.status = 1 AND r.hora_entrada >= :startTime AND r.hora_salida <= :endTime";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':startTime', $startTime);
        $stmt->bindParam(':endTime', $endTime);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } elseif (!empty($searchTerm)) {
        $searchTerm = '%' . $searchTerm . '%'; // Agregar '%' al inicio y al final para buscar coincidencias parciales
        $searchTerm = htmlspecialchars($searchTerm);
        $sql = "SELECT r.id_registro, r.hora_entrada, r.hora_salida, r.matricula, r.nombre, e.nombre_especialidad, s.nombre_servicio
        FROM registro r
        INNER JOIN especialidades e ON r.id_especialidad = e.id_especialidad
        INNER JOIN servicios s ON r.id_servicio = s.id_servicio
        WHERE r.status = 1 AND (r.matricula LIKE :searchTerm OR r.nombre LIKE :searchTerm 
        OR e.nombre_especialidad LIKE :searchTerm OR s.nombre_servicio LIKE :searchTerm 
        OR r.hora_entrada LIKE :searchTerm OR r.hora_salida LIKE :searchTerm)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':searchTerm', $searchTerm);
        $stmt->execute();
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        $result = []; // Array vacío si no se proporciona ninguna opción de búsqueda
    }
} catch (PDOException $e) {
    die("Error en la consulta de servicios: " . $e->getMessage());
}
$groupedData = array();
foreach ($result as $row) {
    $servicio = $row['nombre_servicio'];
    if (isset($groupedData[$servicio])) {
        $groupedData[$servicio]++;
    } else {
        $groupedData[$servicio] = 1;
    }
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
    <script src="https://unpkg.com/xlsx@0.16.9/dist/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/file-saverjs@latest/FileSaver.min.js"></script>
    <script src="https://unpkg.com/tableexport@latest/dist/js/tableexport.min.js"></script>


</head>

<body>
    <header>
        <div class="header-bar">
            <div class="flex items-start justify-start"><a href="index_admin.php"><svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-left" width="44" height="44" viewBox="0 0 24 24" stroke-width="3" stroke="#ffffff" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M5 12l14 0" />
                        <path d="M5 12l6 6" />
                        <path d="M5 12l6 -6" />
                    </svg></a></div>
            <div class="flex justify-center">
                <img src="../img/Image.jpeg" alt="Logo" id="logo">
            </div>

        </div>
    </header>
    <h1 class="my-5 text-center"><b>Vista de Administrador</b></h1>

    <form method="POST" action="" class="flex justify-center mb-10">
        <label for="start_time" class="rounded bg-stone-300 p-2"><b>De:</b></label>
        <input type="datetime-local" id="start_time" name="start_time" placeholder="<?php echo date('d/m/Y H:i'); ?>" class="ml-2 mr-8 bg-gray-100 rounded" value="<?php echo $startTime; ?>">

        <label for="end_time" class="rounded bg-stone-300 p-2 ml-3"><b>A:</b></label>
        <input type="datetime-local" id="end_time" name="end_time" placeholder="<?php echo date('d/m/Y H:i'); ?>" class="ml-2 bg-gray-100 rounded" value="<?php echo $endTime; ?>">

        <button type="submit" class="ml-5 px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700">Filtrar</button>
    </form>
    <div class="flex justify-center my-5">
        <form method="POST">
            <input type="search" name="search_term" id="search-input" placeholder="Buscador" class="border-black rounded bg-gray-100 p-2 text-black placeholder:text-black ml-3" required value="<?php echo isset($searchTerm) ? substr(htmlspecialchars($searchTerm), 1, -1) : ''; ?>">
            <button type="submit" class="ml-5 px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700">Buscar</button>
        </form>
    </div>
    <?php if (!empty($result)) : ?>
        <div class="flex justify-center mx-10">
            <table id="tabla" class="table-auto bg-black w-full">
                <thead>
                    <tr class="bg-gray-800">
                        <th class="px-4 py-2 text-center text-white">Matrícula</th>
                        <th class="px-4 py-2 text-center text-white">Alumno</th>
                        <th class="px-4 py-2 text-center text-white">Carrera</th>
                        <th class="px-4 py-2 text-center text-white">Servicio</th>
                        <th class="px-4 py-2 text-center text-white">Entrada</th>
                        <th class="px-4 py-2 text-center text-white">Salida</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($result as $row) { ?>
                        <tr class="bg-zinc-200">
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo $row['matricula']; ?></td>
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo $row['nombre']; ?></td>
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo $row['nombre_especialidad']; ?></td>
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo $row['nombre_servicio']; ?></td>
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo date('d-m-Y H:i:s', strtotime($row['hora_entrada'])); ?></td>
                            <td class="text-center px-4 py-2 text-sm text-gray-700"><?php echo date('d-m-Y H:i:s', strtotime($row['hora_salida'])); ?></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>

        </div>
    <?php endif; ?>

    <div class="mt-10 flex justify-center">
        <button type="button" id="btnExportar" class="mb-10 px-4 py-2 text-white font-bold bg-green-600 rounded-lg hover:bg-blue-700">Descargar Reporte</button>
    </div>
    <?php
    include 'footer.php';
    ?>
    <script>
        var inputMatricula = document.querySelector('#search-input');
        inputMatricula.addEventListener('input', borrarMatricula);

        function borrarMatricula() {
            // Verificar si el contenido está vacío
            if (inputMatricula.value === '') {
                // Envía el formulario de búsqueda
                document.querySelector('form').submit();
            }
        }
        const $btnExportar = document.querySelector("#btnExportar"),
        $tabla = document.querySelector("#tabla");

    $btnExportar.addEventListener("click", function () {
        const datos = [];
        const headers = [];
        const bodyRows = [];

        // Tomar los headers de la tabla
        const $headers = $tabla.querySelectorAll("thead th");
        $headers.forEach((th) => headers.push(th.textContent));

        // Tomar las filas de las tablas
        const $rows = $tabla.querySelectorAll("tbody tr");
        $rows.forEach((row) => {
            const rowData = [];
            const $cells = row.querySelectorAll("td");
            $cells.forEach((cell) => rowData.push(cell.textContent));
            bodyRows.push(rowData);
        });

        // Agregar los datos a Excel
        const groupedData = <?php echo json_encode($groupedData); ?>;
        Object.entries(groupedData).forEach(([servicio, cantidad]) => {
            bodyRows.push([servicio, cantidad]);
        });

        // Combinar los headers y filas
        datos.push(headers);
        datos.push(...bodyRows);

        // Exportar en Excel
        const worksheet = XLSX.utils.aoa_to_sheet(datos);
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, "Reporte de registros");
        const wbout = XLSX.write(workbook, { bookType: "xlsx", type: "array" });
        saveAs(new Blob([wbout], { type: "application/octet-stream" }), "Reporte_de_registros.xlsx");
    });
    </script>
</body>

</html>