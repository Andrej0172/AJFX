<?php
// Database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

$fout = "";
$succes = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lessen = $_POST['lessen'] ?? "";
    $trainer = $_POST['trainer'] ?? "";
    $locatie = $_POST['locatie'] ?? "";
    $datum = $_POST['datum'] ?? "";
    $tijd = $_POST['tijd'] ?? "";
    $lesprijs = $_POST['lesprijs'] ?? "";

    if (
        $lessen == "" ||
        $trainer == "" ||
        $locatie == "" ||
        $datum == "" ||
        $tijd == "" ||
        $lesprijs == ""
    ) {
        $fout = "⚠ Vul eerst alle kolommen in!";
    } else {

        $sql = "INSERT INTO lessenoverzicht 
                (lessen, trainer, locatie, datum, tijd, lesprijs)
                VALUES 
                ('$lessen', '$trainer', '$locatie', '$datum', '$tijd', '$lesprijs')";

        if ($conn->query($sql) === TRUE) {
            $succes = "✔ Les succesvol toegevoegd!";
        } else {
            $fout = "Fout: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="nl">

<head>
    <meta charset="UTF-8">
    <title>Les toevoegen</title>
</head>

<body>

<h1>Nieuwe les toevoegen</h1>

<?php if ($fout != ""): ?>
    <p style="color:red; font-weight:bold;">
        <?= $fout ?>
    </p>
<?php endif; ?>

<?php if ($succes != ""): ?>
    <p style="color:green; font-weight:bold;">
        <?= $succes ?>
    </p>
<?php endif; ?>

<form method="POST">

    <input type="text" name="lessen" placeholder="Les naam"><br><br>

    <input type="text" name="trainer" placeholder="Trainer"><br><br>

    <input type="text" name="locatie" placeholder="Locatie"><br><br>

    <input type="date" name="datum"><br><br>

    <input type="time" name="tijd"><br><br>

    <input type="number" step="0.01" name="lesprijs" placeholder="Lesprijs"><br><br>

    <button type="submit">Opslaan</button>

</form>
<a href="javascript:history.back()" style="text-decoration:none; font-size:24px;">
    ❌
</a>
</body>
</html>