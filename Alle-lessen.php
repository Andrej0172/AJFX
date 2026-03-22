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
    <link rel="stylesheet" href="css/alle-lessen.css">
</head>

<body>

<?php include 'header.html'; ?>

<h1>Alle lessen</h1>

<div class="knoppen">
    <li>
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
    </li>
    <li>
    <a href="zoek-balk.php">
        <button class="zoek-btn">Zoek op prijs</button>
    </a>
    </li>
</div>

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