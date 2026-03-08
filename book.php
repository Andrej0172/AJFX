<?php
include 'config.php';

$lesson_id = $_GET['id'] ?? null;

if (!$lesson_id) {
    die("Ongeldige les.");
}

// Haal lesgegevens op
$stmt = $pdo->prepare("SELECT * FROM lessons WHERE id = ?");
$stmt->execute([$lesson_id]);
$lesson = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$lesson) {
    die("Les niet gevonden.");
}

// Verwerking van formulier
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $phone = $_POST['phone'] ?? '';

    $stmt = $pdo->prepare("INSERT INTO bookings (lesson_id, name, email, phone) VALUES (?, ?, ?, ?)");
    $stmt->execute([$lesson_id, $name, $email, $phone]);

    echo "<h2>De les '{$lesson['title']}' is succesvol geboekt!</h2>";
    echo "<a href='lessons.php'>Terug naar aanbiedingen</a>";
    exit;
}
?>

<h1>Boek: <?= htmlspecialchars($lesson['title']) ?></h1>

<form method="post">
    Naam: <input type="text" name="name" required><br>
    E-mail: <input type="email" name="email" required><br>
    Telefoon: <input type="text" name="phone"><br>
    <button type="submit">Ok</button>
</form>