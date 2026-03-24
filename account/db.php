<?php
// db.php - Databaseverbinding met foutafhandeling

$host     = 'localhost';
$dbname   = 'ajfx';
$username = 'root';
$password = '';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $username,
        $password,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_TIMEOUT            => 5, // time-out na 5 seconden
        ]
    );
} catch (PDOException $e) {
    // Database niet bereikbaar — sla fout op voor andere bestanden
    $pdo = null;
    $db_error = $e->getMessage();
}
?>