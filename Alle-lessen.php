<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// standaard: alle lessen
$sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Alle lessen</title>
    <link rel="stylesheet" href="css/lessen-overzicht.css">
</head>

<body>

<?php include 'header.html'; ?>

<h1>Alle lessen</h1>

<a href="insert-les.php">
    <button>+ Nieuwe les toevoegen</button>
</a>

<!-- LINK NAAR FILTER -->
<a href="zoek-balk.php">
    <button>Zoek op prijs</button>
</a>

<div class="lessen-container">

<?php
if ($result && $result->num_rows > 0) {

    while ($row = $result->fetch_assoc()) {

        echo "<div class='les-card'>";
        echo "<h3>" . $row["lessen"] . "</h3>";
        echo "<p><b>Trainer:</b> " . $row["trainer"] . "</p>";
        echo "<p><b>Datum:</b> " . $row["datum"] . "</p>";
        echo "<p><b>Tijd:</b> " . $row["tijd"] . "</p>";
        echo "<p><b>Prijs:</b> € " . number_format($row["lesprijs"], 2, ',', '.') . "</p>";
        echo "</div>";
    }

} else {
    echo "<p>Geen lessen gevonden</p>";
}
?>

</div>

</body>
</html>