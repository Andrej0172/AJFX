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
    <link rel="stylesheet" href="css/insert-les.css">
    <title>Les toevoegen</title>
</head>

<body>

<div class="container">

    <h1>Nieuwe les toevoegen</h1>

    <form method="POST">

        <input type="text" name="lessen" placeholder="Les naam"><br>

        <input type="text" name="trainer" placeholder="Trainer"><br>

        <input type="text" name="locatie" placeholder="Locatie"><br>

        <input type="date" name="datum"><br>

        <input type="time" name="tijd"><br>

        <input type="number" step="0.01" name="lesprijs" placeholder="Lesprijs"><br>

        <button type="submit">Opslaan</button>

    </form>

    <!-- WARNINGS ONDERAAN -->
    <?php if ($fout != ""): ?>
        <p class="error"><?= $fout ?></p>
    <?php endif; ?>

    <?php if ($succes != ""): ?>
        <p class="success"><?= $succes ?></p>
    <?php endif; ?>

</div>

<a href="javascript:history.back()" class="close">❌</a>

</body>
</html>