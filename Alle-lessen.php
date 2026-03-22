<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// inputs ophalen
$zoek = $_GET['zoek'] ?? "";
$min = $_GET['min'] ?? "";
$max = $_GET['max'] ?? "";


$sql = "SELECT * FROM lessenoverzicht WHERE 1=1";

// zoekfunctie
if ($zoek != "") {
    $sql .= " AND (lessen LIKE '%$zoek%' 
                OR trainer LIKE '%$zoek%' 
                OR locatie LIKE '%$zoek%')";
}

// prijsfilter
if ($min != "" && $max != "") {
    $sql .= " AND lesprijs BETWEEN $min AND $max";
}

$sql .= " ORDER BY datum, tijd";

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
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
</div>

<!--  ZOEK + PRIJS FILTER -->
<form method="GET">
    <input type="text" name="zoek" placeholder="Zoek les / trainer" value="<?= $zoek ?>">

    <input type="number" step="1" name="min" placeholder="Min prijs" value="<?= $min ?>">
    <input type="number" step="1" name="max" placeholder="Max prijs" value="<?= $max ?>">

    <button type="submit">Filter</button>
</form>

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