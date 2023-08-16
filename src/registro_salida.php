<?php
require_once '../database/Database.php';
require_once '../database/DatabaseAPI.php';
$dbAPI = new DatabaseAPI();
$message = '';
$search_result = [];

// Verificar si se está realizando una búsqueda por matrícula (método GET)

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['search_matricula'])) {
    $search_matricula = $_GET['search_matricula'];
    if (!empty($search_matricula) && strlen($search_matricula) === 10) {
        try {
            // Llamar al método obtenerRegistroPorMatricula de la API
            $search_result = $dbAPI->obtenerRegistroPorMatricula($search_matricula);

            if (empty($search_result)) {
                $message = "No se encontraron registros con la matrícula ingresada.";
            }
        } catch (PDOException $e) {
            die("Error al buscar el registro: " . $e->getMessage());
        }
    } else {
        $message = "Ingrese su matrícula completa para realizar la búsqueda.";
    }
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

    <?php if (empty($search_result) && !empty($message)) : ?>
        <!-- Mostrar mensaje de error de búsqueda -->
        <div class="flex items-center justify-center mb-10">
            <h1 class="bg-red-600 text-white p-2 text-center"><?php echo $message; ?></h1>
        </div>
    <?php elseif (!empty($search_result)) : ?>
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
                        // Llamar al método obtenerNombreServicio de la API
                        $nombreServicio = $dbAPI->obtenerNombreServicio($row['id_servicio']);
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
                    // La solicitud se completó correctamente
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
        document.addEventListener('DOMContentLoaded', function() {
            //Sleccionar los elementos
            const searchInput = document.querySelector('#search-input');
            searchInput.addEventListener('blur', validar);

            function mostrarAlerta(mensaje, referencia) {
                limpiarAlerta(referencia);
                const error = document.createElement('P');
                error.textContent = mensaje;
                error.classList.add('bg-red-600', 'text-white', 'p-2', 'text-center', 'ml-2');
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
        function mostrarMensajeError(mensaje) {
        const errorMessageContainer = document.getElementById('error-message');
        errorMessageContainer.innerHTML = `<h1 class="text-red-500">${mensaje}</h1>`;

        // Ocultar la tabla de resultados si está visible
        const tableContainer = document.querySelector('.table-container');
        if (tableContainer) {
            tableContainer.style.display = 'none';
        }
    }
    </script>
</body>

</html>