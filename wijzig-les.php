<?php
// Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Database fout");
}

// veilig ophalen
$les = $_GET['les'] ?? '';
$datum = $_GET['datum'] ?? '';
$tijd = $_GET['tijd'] ?? '';

$dbFout = false;

// query
$sql = "SELECT * FROM lessenoverzicht 
        WHERE lessen = '$les'
        AND datum = '$datum'
        AND tijd = '$tijd'";

$result = $conn->query($sql);

if (!$result) {
    $dbFout = true;
}

$row = $result ? $result->fetch_assoc() : null;
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/wijzig-les.css">
    <title>Les wijzigen</title>
</head>
<body>

<!-- TERUG KNOP -->
<p>
    <a href="lessen-overzicht.php">
        <button>← Terug</button>
    </a>
</p>

<?php if ($dbFout || !$row): ?>

    <p>Les niet gevonden</p>

<?php else: ?>

    <h2>Les wijzigen</h2>

    <form method="POST" action="update-les.php">

        <input type="hidden" name="oude_les" value="<?= htmlspecialchars($les) ?>">
        <input type="hidden" name="oude_datum" value="<?= htmlspecialchars($datum) ?>">
        <input type="hidden" name="oude_tijd" value="<?= htmlspecialchars($tijd) ?>">

        <label>Les</label>
        <input type="text" name="lessen" value="<?= htmlspecialchars($row['lessen']) ?>">

        <label>Trainer</label>
        <input type="text" name="trainer" value="<?= htmlspecialchars($row['trainer']) ?>">

        <label>Locatie</label>
        <input type="text" name="locatie" value="<?= htmlspecialchars($row['locatie']) ?>">

        <label>Datum</label>
        <input type="date" name="datum" value="<?= $row['datum'] ?>">

        <label>Tijd</label>
        <input type="time" name="tijd" value="<?= $row['tijd'] ?>">

        <button type="submit">Opslaan</button>

    </form>

<?php endif; ?>

</body>
</html>