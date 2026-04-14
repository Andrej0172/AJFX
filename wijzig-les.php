<?php
$conn = new mysqli("localhost", "root", "", "lessen");

// data uit URL ophalen
$les = $_GET['les'] ?? '';
$datum = $_GET['datum'] ?? '';
$tijd = $_GET['tijd'] ?? '';

// huidige les ophalen
$sql = "SELECT * FROM lessenoverzicht 
        WHERE lessen = '$les' 
        AND datum = '$datum' 
        AND tijd = '$tijd'";

$result = $conn->query($sql);
$data = $result->fetch_assoc();

// opslaan na submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $lessen = $_POST['lessen'];
    $trainer = $_POST['trainer'];
    $locatie = $_POST['locatie'];
    $datum = $_POST['datum'];
    $tijd = $_POST['tijd'];

    $update = "UPDATE lessenoverzicht SET 
                lessen='$lessen',
                trainer='$trainer',
                locatie='$locatie',
                datum='$datum',
                tijd='$tijd'
                WHERE lessen='$les' 
                AND datum='$datum' 
                AND tijd='$tijd'";

    $conn->query($update);

    header("Location: lessen-overzicht.php");
    exit;
}
?>

<h2>Les wijzigen</h2>

<form method="POST">
    <input type="text" name="lessen" value="<?= $data['lessen'] ?>">
    <input type="text" name="trainer" value="<?= $data['trainer'] ?>">
    <input type="text" name="locatie" value="<?= $data['locatie'] ?>">
    <input type="date" name="datum" value="<?= $data['datum'] ?>">
    <input type="time" name="tijd" value="<?= $data['tijd'] ?>">

    <button type="submit">Opslaan</button>
</form>

<br>

<a href="lessen-overzicht.php">
    <button type="button">Annuleren</button>
</a>