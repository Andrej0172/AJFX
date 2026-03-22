<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

$result = null;

// filter
if (isset($_GET['min']) && isset($_GET['max']) && $_GET['min'] !== '' && $_GET['max'] !== '') {

    $min = $_GET['min'];
    $max = $_GET['max'];

    $stmt = $conn->prepare("
        SELECT * FROM lessenoverzicht
        WHERE lesprijs BETWEEN ? AND ?
        ORDER BY datum, tijd
    ");

    $stmt->bind_param("dd", $min, $max);
    $stmt->execute();

    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Zoek op prijs</title>
    <link rel="stylesheet" href="css/lessen-overzicht.css">
</head>

<body>

<?php include 'header.html'; ?>

<h1>Zoek lessen op prijs</h1>

<form method="GET">
    Laagste prijs: <input type="number" name="min" step="0.01"><br><br>
    Hoogste prijs: <input type="number" name="max" step="0.01"><br><br>

    <input type="submit" value="Zoeken">
</form>

<br>

<a href="alle-lessen.php">
    <button>← Terug naar alle lessen</button>
</a>

<div class="lessen-container">

<?php
if ($result !== null) {

    if ($result->num_rows > 0) {

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
        echo "<p>Er zijn geen lessen gevonden die overeenkomen met jouw zoekopdracht.</p>";
    }
}
?>

</div>

</body>
</html>