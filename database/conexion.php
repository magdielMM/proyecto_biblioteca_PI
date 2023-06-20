    <?php
    // Database credentials
    $host = 'localhost'; // Assuming the database is hosted locally
    $dbName = 'biblioteca_db';
    $user = 'root';
    $password = '';

    // Attempt to establish the database connection
    try {
        $dbh = new PDO("mysql:host=$host;dbname=$dbName", $user, $password);
        // Set PDO error mode to exception
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        //echo "Connected to the database successfully.";
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>
