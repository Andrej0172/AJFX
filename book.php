<?php
// Configuratiebestand includen (databaseverbinding, instellingen)
include 'config.php';

// Haal het les-ID op uit de URL (bijv. book.php?id=3)
$lesson_id = $_GET['id'] ?? null;

// Stop het script als er geen les-ID is meegegeven
if (!$lesson_id) {
    die("Ongeldige les.");
}

// Haal lesgegevens op uit de database
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

// Stop het script als de les niet bestaat
if (!$lesson) {
    die("Les niet gevonden.");
}

// Verwerking van het formulier als het verzonden is
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Gegevens uit het formulier ophalen
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    // Nieuwe boeking toevoegen aan de database
    $stmt = $pdo->prepare("INSERT INTO bookings (lesson_id, name, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$lesson_id, $name, $email, $phone]);

    // Bevestiging tonen
    echo "<h2>De les '{$lesson['title']}' is succesvol geboekt!</h2>";
    echo "<a href='lessons.php'>Terug naar aanbiedingen</a>";
    exit; // Stop verdere uitvoering
}
?>

<!-- Stijlbestand voor het formulier -->
<link rel="stylesheet" href="book.css">

<!-- Titel van de les -->
<h1>Boek: <?= htmlspecialchars($lesson['title']) ?></h1>

<!-- Boekingsformulier -->
<form method="post">
    Naam: <input type="text" name="name" required><br> <!-- Verplicht veld -->
    E-mail: <input type="email" name="email" required><br> <!-- Verplicht veld -->
    Telefoon: <input type="text" name="phone"><br> <!-- Optioneel veld -->
    <button type="submit">Ok</button> <!-- Verzenden -->
</form>