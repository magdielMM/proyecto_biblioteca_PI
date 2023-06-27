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
    $result = $dbh->query($sql); // Use the PDO connection object to execute the query
} catch (PDOException $e) {
    die("Error en la consulta de carreras: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Registro Alumno</title>
    <link rel="stylesheet" href="../style/style.css">
    <link href="https://unpkg.com/tailwindcss@^2/dist/tailwind.min.css" rel="stylesheet">
    
</head>

<body>
    <header class="center">
        <nav class="nav">
            <div class="links_container">
                <div class="container_logo">
                    <a href="index.php">
                        <img src="../img/UTTN_princ.png" class="uttn_logo" alt=" Uttn logo" />
                    </a>
                </div>
                <div class="container_login center"> <!-- ( center )is a helper class that align / center the items  -->
                    <a href="login_admin.php" class="login" style="color: white">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path fill="white" d="M4 15h2v5h12V4H6v5H4V3a1 1 0 0 1 1-1h14a1 1 0 0 1 1 1v18a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-6zm6-4V8l5 4-5 4v-3H2v-2h8z">
                            </path>
                        </svg>
                        Iniciar Sesión</a>
                </div>
            </div>
        </nav>
    </header>
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