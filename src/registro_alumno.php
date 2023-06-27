<?php
require '../database/conexion.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Obtener los datos del formulario
    $matricula = $_POST['matricula'];
    $nombre = $_POST['nombre'];
    $carrera = $_POST['carrera'];
    $especialidad = $_POST['especialidad'];
    $nivel = $_POST['nivel'];

    try {
        // Verificar si la matrícula ya existe antes de insertarla
        $checkSql = "SELECT COUNT(*) FROM alumno WHERE matricula = :matricula";
        $checkStmt = $dbh->prepare($checkSql);
        $checkStmt->bindParam(':matricula', $matricula);
        $checkStmt->execute();
        $count = $checkStmt->fetchColumn();

        if ($count > 0) {
            $message = 'La matrícula ya está registrada';
        } else {
            // Insertar los datos en la base de datos
            $insertSql = "INSERT INTO alumno (matricula, nombre_alumno, id_carrera, id_especialidad, id_nivel) 
                VALUES (:matricula, :nombre, :carrera, :especialidad, :nivel)";
            $insertStmt = $dbh->prepare($insertSql);
            $insertStmt->bindParam(':matricula', $matricula);
            $insertStmt->bindParam(':nombre', $nombre);
            $insertStmt->bindParam(':carrera', $carrera);
            $insertStmt->bindParam(':especialidad', $especialidad);
            $insertStmt->bindParam(':nivel', $nivel);

            if ($insertStmt->execute()) {
                $message = 'Se ha creado exitosamente el usuario';
                $url = "http://localhost/biblioteca/src/index.php";
                $tiempoespera = 1;
                header("refresh: $tiempoespera; url=$url");
                exit();
            } else {
                $message = 'Ha ocurrido un error al registrar al alumno';
            }
        }
    } catch (PDOException $e) {
        die("Error al guardar el registro: " . $e->getMessage());
    }
}

try {
    $sql = "SELECT id_carrera, nombre_carrera FROM carrera";
    $result = $dbh->query($sql); 
} catch (PDOException $e) {
    die("Error en la consulta de carreras: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro Alumno</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="output.css">
</head>

<body>
    <div class="header bg-green-600 flex justify-between">
        <div>
            <a href="index.php"><button type="button" class="text-white bg-black hover:700 font-bold py-2 px-4 rounded">Inicio</button></a>
        </div>
        <div class="flex justify-center">
            <img class="my-0 transition duration-300 transform hover:scale-110" src="../img/UTTN_princ.png" alt="Logo" style="width: 150px; height: auto;">
        </div>
    </div>
    <h1>Formulario de Alumnos</h1>
    <?php if (!empty($message)) { ?>
        <div class="message"><?php echo $message; ?></div>
    <?php } ?>
    <form method="POST" class="custom-form">
        <label>Matrícula:</label>
        <input type="text" name="matricula" placeholder="Matrícula" required><br><br>

        <label>Nombre completo:</label>
        <input type="text" name="nombre" placeholder="Nombre Completo" required><br><br>
        <?php
        echo '<label>Carrera:</label>';
        echo '<select name="carrera" required id="carreraDropdown">';
        echo '<option value="" disabled selected>Escoge una carrera</option>';
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) { // Use fetch() method to fetch each row
            echo '<option value="' . $row['id_carrera'] . '">' . $row['nombre_carrera'] . '</option>';
        }
        echo '</select>'; ?><br><br>

        <label>Especialidad:</label>
        <select name="especialidad" required id="especialidadDropdown"></select>
        <br><br>
        <label>Nivel de estudio:</label>
        <select name="nivel" required>
            <?php
            $sql = "SELECT id_nivel, nombre_nivel FROM nivel";
            $result = $dbh->query($sql);
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                echo '<option value="' . $row['id_nivel'] . '">' . $row['nombre_nivel'] . '</option>';
            }
            ?>
        </select>
        <br><br>

        <input class="submit-button" type="submit" value="Guardar">
    </form>
    <br><br><br>
    <footer class="footer">
        <div class="footer-content">
            <p class="footer-text">© Universidad Tecnológica de Tamaulipas Norte - 2023</p>
        </div>
    </footer>
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
</body>

</html>