<?php

// URL van de website (wordt gebruikt voor links binnen het project)
define('URLROOT', 'http://localhost/projectp3/AJFX');

// Database gegevens
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de databaseverbinding is gelukt
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// Variabele voor de zoekopdracht
$zoek = "";

// Controleer of er iets is ingevoerd in het zoekveld
if(isset($_GET['zoek'])){
    
    // Beveilig de invoer tegen SQL-injectie
    $zoek = $conn->real_escape_string($_GET['zoek']);
    
    // SQL query: zoek leden waarvan de naam overeenkomt met de zoekopdracht
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email 
            FROM ledenoverzicht 
            WHERE leden LIKE '%$zoek%'";
} else {

    // Als er niet gezocht wordt, haal alle leden op
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email FROM ledenoverzicht";
}

// Voer de query uit
$result = $conn->query($sql);

// Maak een lege array om de resultaten in op te slaan
$data = [];

// Controleer of er resultaten zijn
if ($result && $result->num_rows > 0) {

    // Zet elke rij uit de database in de array
    while ($row = $result->fetch_object()) {
        $data[] = $row;
    }
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>

<meta charset="UTF-8">

<title>Leden Overzicht</title>

<!-- Bootstrap CSS voor styling -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">

<h1 class="mb-4">Leden Overzicht</h1>

<!-- 🔎 Zoekformulier -->
<!-- Hiermee kan een gebruiker zoeken op achternaam -->
<form method="GET" class="mb-4">

<div class="input-group">

<!-- Invoerveld voor de zoekopdracht -->
<input type="text" 
       name="zoek" 
       class="form-control" 
       placeholder="Zoek op achternaam..."
       value="<?= htmlspecialchars($zoek) ?>">

<!-- Zoekknop -->
<button class="btn btn-primary" type="submit">
Zoeken
</button>

</div>
</form>

<!-- Tabel waarin de leden worden weergegeven -->
<table class="table table-striped table-bordered">

<thead class="table-dark">

<tr>

<!-- Kolomtitels -->
<th>Lid</th>
<th>Lidnummer</th>
<th>Les</th>
<th>Leeftijd</th>
<th>Email</th>

</tr>

</thead>

<tbody>

<?php if (!empty($data)) : ?>

<!-- Loop door alle gevonden leden -->
<?php foreach ($data as $ledenOverzicht) : ?>

<tr>

<!-- Toon gegevens uit de database -->
<td><?= htmlspecialchars($ledenOverzicht->leden); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->lidnummer); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->lessen); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->Leeftijd); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->email); ?></td>

</tr>

<?php endforeach; ?>

<?php else : ?>

<!-- Als er geen resultaten zijn gevonden -->
<tr>

<td colspan="5" class="text-center text-danger">
Geen leden gevonden met deze achternaam.
</td>

</tr>

<?php endif; ?>

</tbody>
</table>

</div>

</body>
</html>