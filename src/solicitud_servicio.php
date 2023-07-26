    <?php
    require '../database/conexion.php';

    $message = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtener los datos del formulario
        $matricula = $_POST['matricula'];
        $nombre = $_POST['nombre'];
        $id_carrera = $_POST['id_carrera'];
        $id_especialidad = $_POST['id_especialidad'];
        $id_servicio = $_POST['id_servicio'];
        $horaEntrada = date('Y-m-d H:i:s'); // Obtener la hora actual
        // Hacer un server reuqest para insertar el nuevo registro
        try {
            // Llamar al procedimiento almacenado 'insertar_registro'
            $sql = "CALL insertar_registro(:matricula, :nombre, :id_carrera, :id_especialidad, :id_servicio, :horaEntrada)";
            $stmt = $dbh->prepare($sql);
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_carrera', $id_carrera);
            $stmt->bindParam(':id_especialidad', $id_especialidad);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->bindParam(':horaEntrada', $horaEntrada);

            if ($stmt->execute()) {
                $message = 'Se ha solicitado el servicio exitosamente';
                $url = "http://localhost/biblioteca/src/index.php";
                $tiempoespera = 1;
                header("refresh: $tiempoespera; url=$url");
                exit();
            } else {
                $message = 'Ha ocurrido un error al solicitar el servicio';
            }
        } catch (PDOException $e) {
            die("Error al guardar el registro: " . $e->getMessage());
        }
    }

    try {
        $sql = "CALL obtener_carreras()";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $result_carrera = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "CALL obtener_especialidades()";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $result_especialidad = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $sql = "CALL obtener_servicios()";
        $stmt = $dbh->prepare($sql);
        $stmt->execute();
        $result_servicio = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        die("Error en la consulta: " . $e->getMessage());
    }
    ?>

    <!DOCTYPE html>
    <html>

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Solicitud de Servicio</title>
        <link rel="stylesheet" href="../style/style.css">
        <link rel="stylesheet" href="output.css">
    </head>

    <body>
        <?php include 'header_registros.php'; ?>
        <h1 class="my-5 text-center text-2xl font-bold"><b>Solicitud de Servicio</b></h1>

        <?php if (!empty($message)) : ?>
            <p class="text-center text-red-500 font-bold"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="POST" id="formulario" class="max-w-md mx-auto p-8 bg-[#E1DDDA] rounded-lg shadow-lg">
            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="matricula">Matrícula:</label>
                <input type="text" name="matricula" id="matrícula" placeholder="Matrícula del alumno" required maxlength="10" minlength="10" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre Completo:</label>
                <input type="text" name="nombre" id="nombre" placeholder="Nombre del alumno" required class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="carrera">Carrera:</label>
                <select name="id_carrera" required id="carrera" class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <?php if ($result_carrera !== false) : ?>
                        <option value="" disabled selected>Escoge una carrera</option>
                        <?php foreach ($result_carrera as $carrera) : ?>
                            <option value="<?php echo $carrera['id_carrera']; ?>"><?php echo $carrera['nombre_carrera']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>


            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="especialidad">Especialidad:</label>
                <select name="id_especialidad" required id="especialidad" class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <option value="" disabled selected>Escoge una carrera</option>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-gray-700 text-sm font-bold mb-2" for="id_servicio">Servicio:</label>
                <select name="id_servicio" id="servicio" required class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                    <?php if ($result_servicio !== false) : ?>
                        <option value="" disabled selected>Escoge un servicio</option>
                        <?php foreach ($result_servicio as $row) : ?>
                            <option value="<?php echo $row['id_servicio']; ?>"><?php echo $row['nombre_servicio']; ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>


            <div class="flex justify-center">
                <input type="submit" value="Guardar" class="w-full px-4 py-2 mt-4 text-white font-bold bg-blue-600 rounded-lg hover:bg-blue-700">
            </div>
        </form>
        <br><br><br>
        <?php include 'footer.php'; ?>
    </body>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#carrera').change(function() {
                var carreraId = $(this).val();
                $.ajax({
                    url: 'getEspecialidades.php',
                    method: 'POST',
                    data: {
                        carreraId: carreraId
                    },
                    success: function(data) {
                        $('#especialidad').html(data);
                    }
                });
            });
        });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
    //Sleccionar los elementos
    const inputMatricula = document.querySelector('#matrícula');
    const inputNombre = document.querySelector('#nombre');
    const inputCarrera = document.querySelector('#carrera');
    const inputEspecialidad = document.querySelector('#especialidad');
    const inputServicio = document.querySelector('#servicio');
    inputMatricula.addEventListener('blur', validar);
    inputNombre.addEventListener('blur', validar);
    inputCarrera.addEventListener('blur', validar);
    inputEspecialidad.addEventListener('blur', validar);
    inputServicio.addEventListener('blur', validar);
    

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