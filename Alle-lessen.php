<?php
// Database gegevens
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// verbinding controle
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// database  op halen
if (isset($_GET['zoek']) && $_GET['zoek'] != "") {



    $zoek = strtolower($_GET['zoek']);

    $sql = "SELECT * FROM lessenoverzicht 
            WHERE LOWER(lessen) LIKE '%$zoek%' 
            OR LOWER(trainer) LIKE '%$zoek%' 
            OR LOWER(locatie) LIKE '%$zoek%'
            ORDER BY datum, tijd";

} else {

    $sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";

}

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Lessen Overzicht</title>

    <!-- css -->
    <link rel="stylesheet" href="css/lessen-overzicht.css">
</head>





<body>

    <h1>Alle lessen</h1>

    <div class="lessen-container">

        <?php
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                echo "<div class='les-card'>";

                echo "<h3>" . $row["lessen"] . "</h3>";
                echo "<p><b>Trainer:</b> " . $row["trainer"] . "</p>";

                echo "</div>";
            }

        } else {

            echo "<p>Geen les gevonden</p>";

        }
        ?>

    </div>

</body>

</html>