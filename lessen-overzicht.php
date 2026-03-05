<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

$sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Lessen Overzicht</title>
<link rel="stylesheet" href="css/lessen-overzicht.css">
</head>

<body>

<h1>Lessen Overzicht</h1>

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
    echo "<tr><td colspan='5'>Geen lessen gevonden</td></tr>";
}
?>

</tbody>
</table>

</body>
</html>

<?php
$conn->close();
?>