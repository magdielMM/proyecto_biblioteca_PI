<?php
require '../database/conexion.php';

header("Content-Type: application/json"); // Indicamos que la respuesta será en formato JSON

// Verificamos si la solicitud es de tipo POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $id_carrera = $_POST['id_carrera'];
    $id_especialidad = $_POST['id_especialidad'];
    $id_servicio = $_POST['id_servicio'];
    $horaEntrada = date('Y-m-d H:i:s'); // Obtener la hora actual

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
            // Si la inserción es exitosa, enviamos una respuesta de éxito en formato JSON
            echo json_encode(['success' => true, 'message' => 'Se ha solicitado el servicio exitosamente']);
        } else {
            // Si hubo un error, enviamos una respuesta de error en formato JSON
            echo json_encode(['success' => false, 'message' => 'Ha ocurrido un error al solicitar el servicio']);
        }
    } catch (PDOException $e) {
        // Si hubo una excepción, enviamos una respuesta de error en formato JSON
        echo json_encode(['success' => false, 'message' => 'Error al guardar el registro: ' . $e->getMessage()]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['carreraId'])) {
    // Conexión a la base de datos
    require '../database/conexion.php';

    $carreraId = $_POST['carreraId'];

    // Obtener las especialidades para la carrera seleccionada desde la base de datos
    $sql_especialidad = "SELECT id_especialidad, nombre_especialidad FROM especialidades WHERE id_carrera = :carreraId";
    $stmt = $dbh->prepare($sql_especialidad);
    $stmt->bindParam(':carreraId', $carreraId);
    $stmt->execute();

    $especialidades = array();
    while ($especialidad = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $especialidades[] = $especialidad;
    }

    // Devolver la respuesta en formato JSON
    header('Content-Type: application/json');
    echo json_encode($especialidades);
}
?>
