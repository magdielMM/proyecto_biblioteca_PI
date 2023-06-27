<?php
require '../database/conexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['registroId'])) {
    $registroId = $_POST['registroId'];
    $horaSalida = date('Y-m-d H:i:s');

    try {
        $updateSql = "UPDATE registro SET hora_salida = :horaSalida WHERE id_registro = :registroId";
        $updateStmt = $dbh->prepare($updateSql);
        $updateStmt->bindParam(':horaSalida', $horaSalida);
        $updateStmt->bindParam(':registroId', $registroId);

        if ($updateStmt->execute()) {
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
