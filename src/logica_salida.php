<?php
require '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registroId'])) {
    $registroId = $_POST['registroId'];

    try {
        // Llamar al procedimiento almacenado "RegistrarSalida"
        $stmt = $dbh->prepare("CALL RegistrarSalida(:registroId)");
        $stmt->bindParam(':registroId', $registroId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo "Salida registrada correctamente";
        } else {
            echo "Error al registrar la salida";
        }
    } catch (PDOException $e) {
        echo "Error en la consulta: " . $e->getMessage();
    }
} else {
    echo "Solicitud no válida";
}
?>
