<?php
require '../database/conexion.php';

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
</head>

<body>
    <header class="header">
        <a href="index.php"><button type="button" class="rounded-button">Inicio</button></a>
        <div class="center-image">
            <img src="../img/UTTN_princ.png" alt="logo" width="200px">
        </div>
        <div class="logo">
            <a href="login_admin.php"><img src="../img/icons-person-90.png" alt="Login logo"></a>
        </div>
    </header>
    <h1>Formulario de Alumnos</h1>
    <form method="POST" class="custom-form">
        <label>Matrícula:</label>
        <input type="text" name="matricula" required><br><br>

        <label>Nombre completo:</label>
        <input type="text" name="nombre" required><br><br>

        <?php
        echo '<label>Carrera:</label>';
        echo '<select name="carrera" required id="carreraDropdown">';
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

</body>

</html>