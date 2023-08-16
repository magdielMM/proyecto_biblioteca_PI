<?php
require_once '../database/Database.php';
require_once '../database/DatabaseAPI.php';

session_start();

if (!isset($_SESSION['user'])) {
    header("Location: login_admin.php");
    exit();
}

$startTime = isset($_POST['start_time']) ? date('Y-m-d H:i:s', strtotime($_POST['start_time'])) : null;
$endTime = isset($_POST['end_time']) ? date('Y-m-d H:i:s', strtotime($_POST['end_time'])) : null;
$searchTerm = isset($_POST['search_term']) ? $_POST['search_term'] : null;

try {
    $db = new DatabaseAPI();

    // Llamamos al método correspondiente en la API para obtener los registros
    $result = $db->getRecords($startTime, $endTime, $searchTerm);


    // Obtener los datos agrupados para la descarga en Excel
    $groupedData = array();
    foreach ($result as $row) {
        $servicio = $row['nombre_servicio'];
        if (isset($groupedData[$servicio])) {
            $groupedData[$servicio]++;
        } else {
            $groupedData[$servicio] = 1;
        }
    }

    // Obtener los datos agrupados para la gráfica de pastel de carreras
    $carrerasGroupedData = array();
    foreach ($result as $row) {
        $carrera = $row['nombre_especialidad'];
        if (isset($carrerasGroupedData[$carrera])) {
            $carrerasGroupedData[$carrera]++;
        } else {
            $carrerasGroupedData[$carrera] = 1;
        }
    }
} catch (PDOException $e) {
    die("Error en la consulta de servicios: " . $e->getMessage());
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

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

    <header>
        <div class="header-bar">
            <div class="flex justify-center">
                <a href="index_admin.php"><img src="../img/Image.jpeg" alt="Logo" id="logo"></a>
            </div>
        </div>
    </header>
    <div class="container mx-auto py-8">
        <h1 class="text-2xl font-semibold text-center mb-4">Vista de Administrador</h1>
        <div class="flex justify-center space-x-4 mb-8">
            <form method="POST" action="" class="flex space-x-4">
                <label for="start_time" class="rounded bg-stone-300 p-2"><b>De:</b></label>
                <input type="datetime-local" id="start_time" name="start_time" placeholder="<?php echo date('d/m/Y H:i'); ?>" class="ml-2 mr-8 bg-gray-100 rounded" value="<?php echo $startTime; ?>">

                <label for="end_time" class="rounded bg-stone-300 p-2 ml-3"><b>A:</b></label>
                <input type="datetime-local" id="end_time" name="end_time" placeholder="<?php echo date('d/m/Y H:i'); ?>" class="ml-2 bg-gray-100 rounded" value="<?php echo $endTime; ?>">

                <button type="submit" class="ml-5 px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700">Filtrar</button>
            </form>
            <div class="flex justify-center my-5">
                <form method="POST" onsubmit="return borrarMatricula();" class="flex space-x-4">
                    <input type="search" name="search_term" id="buscador" placeholder="Buscador" class="border-black rounded bg-gray-100 p-2 text-black placeholder:text-black ml-3" required value="<?php echo isset($searchTerm) ? $searchTerm : ''; ?>">
                    <button type="submit" class="ml-5 px-4 py-2 mt-2 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700">Buscar</button>
                </form>
            </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 mt-5">
            <div class="bg-white p-4 shadow-lg rounded-lg">
                <h2 class="text-lg font-semibold mb-4 text-center">Cantidad de Servicios Utilizados</h2>
                <div class="chart-container"></div>
            </div>
            <div class="bg-white p-4 shadow-lg rounded-lg">
                <h2 class="text-lg font-semibold mb-4 text-center">Porcentaje de Carreras en Registros</h2>
                <div class="carreras-chart-container"></div>
            </div>
        </div>
        <?php if (!empty($result)) : ?>
            <div class="bg-white mt-8 shadow-lg rounded-lg overflow-x-auto">
                <table id="tabla" class="min-w-full">
                    <thead>
                        <tr class="bg-gray-800 text-white">
                            <th class="py-3 px-4 text-center">Matrícula</th>
                            <th class="py-3 px-4 text-center">Alumno</th>
                            <th class="py-3 px-4 text-center">Carrera</th>
                            <th class="py-3 px-4 text-center">Servicio</th>
                            <th class="py-3 px-4 text-center">Entrada</th>
                            <th class="py-3 px-4 text-center">Salida</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($result as $row) { ?>
                            <tr class="text-gray-700">
                                <td class="py-2 px-4 text-center"><?php echo $row['matricula']; ?></td>
                                <td class="py-2 px-4 text-center"><?php echo $row['nombre']; ?></td>
                                <td class="py-2 px-4 text-center"><?php echo $row['nombre_especialidad']; ?></td>
                                <td class="py-2 px-4 text-center"><?php echo $row['nombre_servicio']; ?></td>
                                <td class="py-2 px-4 text-center"><?php echo date('d-m-Y H:i:s', strtotime($row['hora_entrada'])); ?></td>
                                <td class="py-2 px-4 text-center"><?php echo date('d-m-Y H:i:s', strtotime($row['hora_salida'])); ?></td>
                            </tr>
                        <?php } ?>
                    </tbody>
                </table>

            </div>
        <?php endif; ?>

        <div class="flex justify-center mt-8">
            <button type="button" id="btnExportar" class="px-4 py-2 bg-green-600 text-white font-semibold rounded hover:bg-green-700">Descargar Reporte</button>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chartjs-plugin-datalabels/2.2.0/chartjs-plugin-datalabels.min.js" integrity="sha512-JPcRR8yFa8mmCsfrw4TNte1ZvF1e3+1SdGMslZvmrzDYxS69J7J49vkFL8u6u8PlPJK+H3voElBtUCzaXj+6ig==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <?php
    include 'footer.php';
    ?>
    <script>
        var inputMatricula = document.querySelector('#buscador');
        inputMatricula.addEventListener('input', borrarMatricula);

        function borrarMatricula() {
            // Verificar si el contenido está vacío
            if (inputMatricula.value === '') {
                // Envía el formulario de búsqueda
                document.getElementById("buscador").value = "";
                val('');
            }
        }

        const $btnExportar = document.querySelector("#btnExportar"),
            $tabla = document.querySelector("#tabla");

        $btnExportar.addEventListener("click", function() {
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
            const wbout = XLSX.write(workbook, {
                bookType: "xlsx",
                type: "array"
            });
            saveAs(new Blob([wbout], {
                type: "application/octet-stream"
            }), "Reporte_de_registros.xlsx");
        });

        document.addEventListener('DOMContentLoaded', function() {
            //Sleccionar los elementos

            function mostrarAlerta(mensaje, referencia) {
                limpiarAlerta(referencia);
                const error = document.createElement('P');
                error.textContent = mensaje;
                error.classList.add('bg-white', 'text-red-500', 'p-2', 'text-center');
                referencia.appendChild(error);
            }

            function limpiarAlerta(referencia) {
                const alerta = referencia.querySelector('.bg-red-600');
                if (alerta) {
                    alerta.remove();
                }
                console.log('desde limpiar alerta');
            }
        });

        // Obtener los datos agrupados para la gráfica de barras
        const serviciosData = <?php echo json_encode($groupedData); ?>;
        const serviciosLabels = Object.keys(serviciosData);
        const serviciosValues = Object.values(serviciosData);

        // Crear un elemento canvas para la gráfica de barras
        const canvas = document.createElement('canvas');
        canvas.id = 'serviciosChart';
        canvas.width = 400;
        canvas.height = 400;
        const chartContainer = document.querySelector('.chart-container'); // Asegúrate de que este selector sea correcto
        chartContainer.appendChild(canvas);

        // Crear la gráfica de barras
        new Chart(canvas, {
            type: 'bar',
            data: {
                labels: serviciosLabels,
                datasets: [{
                    label: 'Servicios',
                    data: serviciosValues,
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(63, 215, 125, 0.92)',
                        'rgba(128, 0, 0, 0.7)',
                        'rgba(0, 128, 0, 0.7)',
                        'rgba(0, 0, 128, 0.7)',
                        'rgba(255, 165, 0, 0.8)',
                        'rgba(0, 255, 255, 0.8)',
                        'rgba(255, 0, 255, 0.8)',
                        'rgba(128, 128, 0, 0.9)',
                        'rgba(0, 128, 128, 0.9)',
                        'rgba(128, 0, 128, 0.9)',
                        'rgba(0, 0, 0, 0.6)',
                        'rgba(255, 0, 0, 0.8)'
                    ]
                }],
            },
            options: {
                plugins: {
                    legend: {
                        labels: {
                            // Set labels to an empty array to hide the legend
                            display: false,
                        },
                    },
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Cantidad de Alumnos',
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Servicios',
                        }
                    }
                }
            },
            datasets: [{
        data: [], // Set an empty array to remove the dataset
    }],
        });


        // Crear un elemento canvas para la gráfica de pastel de carreras
        const carrerasCanvas = document.createElement('canvas');
        carrerasCanvas.id = 'carrerasChart';
        carrerasCanvas.width = 400;
        carrerasCanvas.height = 400;
        const carrerasChartContainer = document.querySelector('.carreras-chart-container'); // Agrega una clase a un contenedor adecuado en tu HTML
        carrerasChartContainer.appendChild(carrerasCanvas);

        // Obtener los datos agrupados para la gráfica de pastel de carreras
        const carrerasData = <?php echo json_encode($carrerasGroupedData); ?>;
        const carrerasLabels = Object.keys(carrerasData);
        const carrerasValues = Object.values(carrerasData);

        // Crear la gráfica de pastel de carreras
        new Chart(carrerasCanvas, {
            type: 'pie',
            data: {
                labels: carrerasLabels,
                datasets: [{
                    label: 'Cantidad de Carreras Utilizadas',
                    data: carrerasValues,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.8)',
                        'rgba(54, 162, 235, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(63, 215, 125, 0.92)',
                        'rgba(128, 0, 0, 0.7)',
                        'rgba(0, 128, 0, 0.7)',
                        'rgba(0, 0, 128, 0.7)',
                        'rgba(255, 165, 0, 0.8)',
                        'rgba(0, 255, 255, 0.8)',
                        'rgba(255, 0, 255, 0.8)',
                        'rgba(128, 128, 0, 0.9)',
                        'rgba(0, 128, 128, 0.9)',
                        'rgba(128, 0, 128, 0.9)',
                        'rgba(0, 0, 0, 0.6)',
                        'rgba(255, 0, 0, 0.8)',
                        'rgba(0, 255, 0, 0.8)',
                        'rgba(0, 0, 255, 0.8)',
                        'rgba(255, 255, 0, 0.9)',
                        'rgba(255, 0, 255, 0.7)',
                        'rgba(0, 255, 255, 0.7)',
                        'rgba(128, 0, 0, 0.6)',
                        'rgba(0, 128, 0, 0.6)',
                        'rgba(0, 0, 128, 0.6)',
                        'rgba(255, 165, 0, 0.7)',
                        'rgba(0, 255, 255, 0.6)',
                        'rgba(255, 0, 255, 0.6)',
                        'rgba(128, 128, 0, 0.8)',
                        'rgba(0, 128, 128, 0.8)',
                        'rgba(128, 0, 128, 0.8)'
                    ],
                }],
            },
            plugins: [ChartDataLabels], // Include the plugin
            options: {
                plugins: {
                    datalabels: {
                        formatter: (value, context) => {
                            let sum = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = (value * 100 / sum).toFixed(2);
                            return percentage + '%';
                        },
                        color: '#fff', // Label color
                    },
                },
            },
        });
    </script>
</body>

</html>