    <?php

    $host = 'localhost'; 
    $dbName = 'biblioteca_db';
    $user = 'root';
    $password = '';

    try {
        $dbh = new PDO("mysql:host=$host;dbname=$dbName", $user, $password);

        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>
