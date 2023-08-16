<?php
class Database {
    
private $host = '172.16.0.105';
    private $dbName = 'biblioteca_db';
    private $user = 'uttn_alumno1';
    private $password = 'radiofax6548';
    private $dbh;
    public function __construct() {
        try {
            $this->dbh = new PDO("mysql:host=$this->host;dbname=$this->dbName", $this->user, $this->password);
            $this->dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }

    public function getDBH() {
        return $this->dbh;
    }
}
?>
