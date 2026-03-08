<?php
// Database gegevens
$servername = "localhost";   
$username = "root";          
$password = "";              
$dbname = "lessen";   

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de verbinding gelukt is
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// database  op halen
$sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";


$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Lessen Overzicht</title>

<!-- link naar css -->
<link rel="stylesheet" href="css/lessen-overzicht.css">
</head>

<body>


<h1>Lessen</h1>


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

    while($row = $result->fetch_assoc()) {

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
    echo "<tr><td colspan='5'>Geen lessen gevonden</td></tr>";
}


    
?>


</tbody>
</table>
<!--alle lessen-->

<h2>Alle lessen</h2>

<div class="lessen-container">

<?php
$result = $conn->query($sql);

while($row = $result->fetch_assoc()) {

echo "<div class='les-card'>";

echo "<h3>".$row["lessen"]."</h3>";
echo "<p><b>Trainer:</b> ".$row["trainer"]."</p>";
echo "<p><b>Datum:</b> ".date("d-m-Y", strtotime($row["datum"]))."</p>";
echo "<p><b>Tijd:</b> ".date("H:i", strtotime($row["tijd"]))."</p>";

echo "</div>";
}
?>

</div>

</body>
</html>

<?php
// Sluit de database verbinding
$conn->close();
?>