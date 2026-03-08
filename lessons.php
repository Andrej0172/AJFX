<?php
// Fouten tonen voor debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuratiebestand includen (databaseverbinding, instellingen)
include 'config.php';

// Onderhoudsmodus aan/uit
$maintenance = false;
if ($maintenance) {
    // Bericht tonen als de site tijdelijk niet beschikbaar is
    echo "<h2 style='text-align:center;color:red;margin-top:50px;'>Deze pagina is tijdelijk niet beschikbaar. Probeer het later opnieuw.</h2>";
    exit; // Stop verdere uitvoering
}

// Haal alle lessen op uit de database
$stmt = $pdo->query("SELECT * FROM lessons");
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<!-- Koppel het CSS-stijlbestand -->
<link rel="stylesheet" href="stylelessen.css">
<title>Aanbiedingen & Lessen - Sportschool</title>
</head>
<body>

<header>
    <!-- Pagina titel -->
    <h1>Aanbiedingen & Lessen - Sportschool</h1>
</header>

<div class="container">
    <!-- Loop door alle lessen heen en maak voor elke les een kaartje -->
    <?php foreach ($lessons as $lesson): ?>
        <div class="lesson-card">
            <!-- Titel van de les -->
            <div class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></div>
            <!-- Prijs van de les -->
            <div class="lesson-price">Prijs: €<?= $lesson['price'] ?></div>
            <!-- Beschrijving van de les -->
            <div class="lesson-description"><?= htmlspecialchars($lesson['description']) ?></div>
            <!-- Link naar boekingspagina met de les-ID -->
            <a class="book-button" href="book.php?id=<?= $lesson['id'] ?>">Boek deze les</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>