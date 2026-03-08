<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'config.php';

$maintenance = false;
if ($maintenance) {
    echo "<h2 style='text-align:center;color:red;margin-top:50px;'>Deze pagina is tijdelijk niet beschikbaar. Probeer het later opnieuw.</h2>";
    exit;
}

// Haal alle lessen op
$stmt = $pdo->query("SELECT * FROM lessons");
$lessons = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<link rel="stylesheet" href="stylelessen.css">
<title>Aanbiedingen & Lessen - Sportschool</title>
</head>
<body>

<header>
    <h1>Aanbiedingen & Lessen - Sportschool</h1>
</header>

<div class="container">
    <?php foreach ($lessons as $lesson): ?>
        <div class="lesson-card">
            <div class="lesson-title"><?= htmlspecialchars($lesson['title']) ?></div>
            <div class="lesson-price">Prijs: €<?= $lesson['price'] ?></div>
            <div class="lesson-description"><?= htmlspecialchars($lesson['description']) ?></div>
            <a class="book-button" href="book.php?id=<?= $lesson['id'] ?>">Boek deze les</a>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>