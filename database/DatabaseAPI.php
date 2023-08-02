<?php

class DatabaseAPI {
    private $dbh;

    public function __construct() {
        $db = new Database();
        $this->dbh = $db->getDBH();
    }

    public function insertarSolicitudServicio($matricula, $nombre, $id_carrera, $id_especialidad, $id_servicio, $hora_entrada)
    {
        try {
            // Llamamos al procedimiento almacenado para insertar el nuevo registro
            $stmt = $this->dbh->prepare("CALL insertar_solicitud_servicio(:matricula, :nombre, :id_carrera, :id_especialidad, :id_servicio, :hora_entrada)");
            $stmt->bindParam(':matricula', $matricula);
            $stmt->bindParam(':nombre', $nombre);
            $stmt->bindParam(':id_carrera', $id_carrera);
            $stmt->bindParam(':id_especialidad', $id_especialidad);
            $stmt->bindParam(':id_servicio', $id_servicio);
            $stmt->bindParam(':hora_entrada', $hora_entrada);

            // Ejecutamos el procedimiento almacenado
            $stmt->execute();

            // Si la ejecución es exitosa, devolvemos true
            return true;
        } catch (PDOException $e) {
            // Si ocurre un error, devolvemos el mensaje de error en la variable $message
            if ($e->getCode() === '45000') {
                return "Ya existe una solicitud que no se ha registrado la salida para esta matrícula";
            } else {
                return "Error al insertar el registro: " . $e->getMessage();
            }
        }
    }

    public function obtenerCarreras() {
        try {
            $sql = "CALL obtener_carreras()";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejar el error en caso de que ocurra.
            return false;
        }
    }
    public function obtenerEspecialidades() {
        try {
            $sql = "CALL obtener_especialidades()";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejar el error en caso de que ocurra.
            return false;
        }
    }
    public function obtenerServicios() {
        try {
            $sql = "CALL obtener_servicios()";
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // Manejar el error en caso de que ocurra.
            return false;
        }
    }
    public function obtenerNombreServicio($servicioId) {
        $sql = "CALL ObtenerNombreServicio(:servicioId, @nombreServicio)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':servicioId', $servicioId, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener el resultado del procedimiento almacenado
        $stmt = $this->dbh->query("SELECT @nombreServicio as nombreServicio");
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
        return $resultado['nombreServicio'];
    }

    public function registrarSalida($registroId) {
        $sql = "CALL RegistrarSalida(:registroId)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':registroId', $registroId, PDO::PARAM_INT);
        return $stmt->execute();
    }
    public function obtenerRegistroPorMatricula($matricula) {
        $sql = "CALL BuscarRegistroPorMatricula(:matricula)";
        $stmt = $this->dbh->prepare($sql);
        $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function obtenerPasswordHash($user) {
        try {
            $sql = "CALL GetPasswordHash(:user, @password_hash)";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':user', $user, PDO::PARAM_STR);
            $stmt->execute();

            // Obtener el hash de la contraseña del resultado del procedimiento almacenado
            $stmt = $this->dbh->query("SELECT @password_hash AS password_hash");
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result['password_hash'];
        } catch (PDOException $e) {
            // En caso de error, puedes manejarlo según tus necesidades (lanzar excepciones, loggear el error, etc.)
            die("Error en la base de datos: " . $e->getMessage());
        }
    }
    public function registrarAdministrador($user, $password) {
        // Aplicar el método hash a la contraseña
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        try {
            $checkSql = "SELECT COUNT(*) FROM user WHERE user = :user";
            $checkStmt = $this->dbh->prepare($checkSql);
            $checkStmt->bindParam(':user', $user);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count == 0) {
                $sql = "INSERT INTO user (user, password_user) VALUES (:user, :password)";
                $stmt = $this->dbh->prepare($sql);
                $stmt->bindParam(':user', $user);
                $stmt->bindParam(':password', $hashedPassword);

                if ($stmt->execute()) {
                    return true;
                } else {
                    return false;
                }
            } else {
                return false;
            }
        } catch (PDOException $e) {
            // En caso de error, puedes manejarlo según tus necesidades (lanzar excepciones, loggear el error, etc.)
            die("Error en la base de datos: " . $e->getMessage());
        }
    }

    public function verificarUsuarioExistente($user) {
        try {
            $checkSql = "SELECT COUNT(*) FROM user WHERE user = :user";
            $checkStmt = $this->dbh->prepare($checkSql);
            $checkStmt->bindParam(':user', $user);
            $checkStmt->execute();
            $count = $checkStmt->fetchColumn();

            if ($count == 0) {
                return false;
            } else {
                return true;
            }
        } catch (PDOException $e) {
            // En caso de error, puedes manejarlo según tus necesidades (lanzar excepciones, loggear el error, etc.)
            die("Error en la base de datos: " . $e->getMessage());
        }
    }

    public function getRecords($startTime, $endTime, $searchTerm) {
        try {
            $sql = "CALL GetRecords(:startTime, :endTime, :searchTerm)";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':startTime', $startTime, PDO::PARAM_STR);
            $stmt->bindParam(':endTime', $endTime, PDO::PARAM_STR);
            $stmt->bindParam(':searchTerm', $searchTerm, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            // En caso de error, puedes manejarlo según tus necesidades (lanzar excepciones, loggear el error, etc.)
            die("Error en la consulta de servicios: " . $e->getMessage());
        }
    }
    public function obtenerSolicitudServicioPorMatricula($matricula)
    {
        try {
            // Preparar la llamada al procedimiento almacenado
            $sql = "CALL obtener_solicitud_servicio_por_matricula(:matricula)";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindParam(':matricula', $matricula, PDO::PARAM_STR);
            $stmt->execute();

            // Obtener el resultado del procedimiento almacenado
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

            // Devolver el resultado de la consulta
            return $resultado;
        } catch (PDOException $e) {
            // Manejar el error de la consulta si es necesario
            die("Error en la consulta: " . $e->getMessage());
        }
    }
}
