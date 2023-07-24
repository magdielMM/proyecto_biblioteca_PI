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
            $sql = "INSERT INTO registro (matricula, nombre, id_carrera, id_especialidad, id_servicio, hora_entrada) 
                VALUES (:matricula, :nombre, :id_carrera, :id_especialidad, :id_servicio, :horaEntrada)";
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
        $sql_carrera = "SELECT id_carrera, nombre_carrera FROM carrera";
        $result_carrera = $dbh->query($sql_carrera);

        $sql_especialidad = "SELECT id_especialidad, nombre_especialidad FROM especialidades";
        $result_especialidad = $dbh->query($sql_especialidad);

        $sql_servicio = "SELECT id_servicio, nombre_servicio FROM servicios";
        $result_servicio = $dbh->query($sql_servicio);
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

    <form method="POST" class="max-w-md mx-auto p-8 bg-[#E1DDDA] rounded-lg shadow-lg">
        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="matricula">Matrícula:</label>
            <input type="text" name="matricula" id="matricula" placeholder="Matrícula del alumno" required maxlength="10" minlength="10" class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="nombre">Nombre:</label>
            <input type="text" name="nombre" id="nombre" placeholder="Nombre del alumno" required class="w-full px-3 py-2 placeholder-gray-300 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="carreraDropdown">Carrera:</label>
            <select name="id_carrera" required id="carreraDropdown" class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                <option value="" disabled selected>Escoge una carrera</option>
                <?php
                $carreras = $result_carrera->fetchAll(PDO::FETCH_ASSOC);
                foreach ($carreras as $carrera) {
                    echo '<option value="' . $carrera['id_carrera'] . '">' . $carrera['nombre_carrera'] . '</option>';
                }
                ?>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="especialidadDropdown">Especialidad:</label>
            <select name="id_especialidad" required id="especialidadDropdown" class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                <option value="" disabled selected>Escoge una carrera</option>
            </select>
        </div>

        <div class="mb-4">
            <label class="block text-gray-700 text-sm font-bold mb-2" for="id_servicio">Servicio:</label>
            <select name="id_servicio" required class="w-full px-3 py-2 border rounded-lg focus:shadow-outline focus:outline-none focus:ring-1 focus:ring-blue-600">
                <?php while ($row = $result_servicio->fetch(PDO::FETCH_ASSOC)) : ?>
                    <option value="<?php echo $row['id_servicio']; ?>"><?php echo $row['nombre_servicio']; ?></option>
                <?php endwhile; ?>
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
            $('#carreraDropdown').change(function() {
                var carreraId = $(this).val();
                $.ajax({
                    url: 'getEspecialidades.php',
                    method: 'POST',
                    data: {
                        carreraId: carreraId
                    },
                    success: function(data) {
                        $('#especialidadDropdown').html(data);
                    }
                });
            });
        });
    </script>

    </html>