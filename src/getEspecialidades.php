<?php
require '../database/conexion.php';

if (isset($_POST['carreraId'])) {
    $carreraId = $_POST['carreraId'];

    try {
        // Llamar al procedimiento almacenado 'obtener_especialidades_por_carrera'
        $sql = "CALL obtener_especialidades_por_carrera(:carreraId)";
        $stmt = $dbh->prepare($sql);
        $stmt->bindParam(':carreraId', $carreraId);
        $stmt->execute();

        $especialidades = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($especialidades as $especialidad) {
            echo '<option value="' . $especialidad['id_especialidad'] . '">' . $especialidad['nombre_especialidad'] . '</option>';
        }
    } catch (PDOException $e) {
        die("Error en la consulta de especialidades: " . $e->getMessage());
    }
}
?>
