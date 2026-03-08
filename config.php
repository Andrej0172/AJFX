<?php
$host = "localhost";
$dbname = "school_db";
$user = "root";       // pas aan naar je database gebruikersnaam
$pass = "";           // pas aan naar je wachtwoord

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connectie mislukt: " . $e->getMessage());
}