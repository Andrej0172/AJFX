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

    $zoek = $_GET['zoek'];

    $sql = "SELECT * FROM lessenoverzicht 
            WHERE lessen LIKE '%$zoek%' 
            OR trainer LIKE '%$zoek%' 
            OR locatie LIKE '%$zoek%'
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
    <!--header-->
    <?php include 'header.php'; ?>

    <!-- zoek balk -->
    <form method="GET">
        <input type="text" name="zoek" placeholder="Zoek een les...">
        <button type="submit">Zoeken</button>
    </form>



    <table>

        <thead>
            <tr>

                <th>Les</th>
                <th>Trainer</th>
                <th>Locatie</th>
                <th>Datum</th>
                <th>Tijd</th>
            </tr>
        </thead>

        <tbody>

            <?php
            // Controle
            if ($result->num_rows > 0) {

                while ($row = $result->fetch_assoc()) {

                    echo "<tr>";
                    echo "<td>" . $row["lessen"] . "</td>";
                    echo "<td>" . $row["trainer"] . "</td>";
                    echo "<td>" . $row["locatie"] . "</td>";
                    echo "<td>" . $row["datum"] . "</td>";
                    echo "<td>" . $row["tijd"] . "</td>";

                    echo "</tr>";
                }

            } else {

                // unhappy
                echo "<tr><td colspan='5'>Geen les gevonden</td></tr>";
            }



            ?>


        </tbody>
    </table>
    <!--alle lessen-->

    <h1>Alle lessen</h1>

    <div class="lessen-container">

        <?php
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {

            while ($row = $result->fetch_assoc()) {

                echo "<div class='les-card'>";

                echo "<h3>" . $row["lessen"] . "</h3>";
                echo "<p><b>Trainer:</b> " . $row["trainer"] . "</p>";
                echo "<p><b>Datum:</b> " . date("d-m-Y", strtotime($row["datum"])) . "</p>";
                echo "<p><b>Tijd:</b> " . date("H:i", strtotime($row["tijd"])) . "</p>";

                echo "</div>";
            }

        } else {

            echo "<p>Geen les gevonden</p>";

        }
        ?>

    </div>

</body>

</html>

<?php
// Sluit de database verbinding
$conn->close();
?>