<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

// connectie maken
$conn = new mysqli($servername, $username, $password, $dbname);

// check connectie
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// data ophalen
$lessen = $_POST['lessen'];
$trainer = $_POST['trainer'];
$locatie = $_POST['locatie'];
$datum = $_POST['datum'];
$tijd = $_POST['tijd'];

// query
$sql = "INSERT INTO lessenoverzicht (lessen, trainer, locatie, datum, tijd)
VALUES ('$lessen', '$trainer', '$locatie', '$datum', '$tijd')";

// uitvoeren
if ($conn->query($sql) === TRUE) {
    header("Location: Alle-lessen.php?succes=1");
exit();
} else {
    echo "Fout: " . $conn->error;
}

$conn->close();
?>

<?php
if (isset($_GET['succes'])) {
    echo "<p style='color:green;'>Les succesvol toegevoegd!</p>";
}
?>