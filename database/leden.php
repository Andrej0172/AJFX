<?php
define('URLROOT', 'http://localhost/projectp3/AJFX');
// Controller part (example, assuming MVC framework)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

// Database verbinding
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

// Controle verbinding
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// Query
$sql = "SELECT leden, lidnummer, lessen, Leeftijd, email FROM ledenoverzicht";
$result = $conn->query($sql);

$data = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        $data[] = $row;
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
<meta charset="UTF-8">
<title>Leden Overzicht</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

</head>
<body>

<div class="container mt-5">

<h1 class="mb-4">Leden Overzicht</h1>

<div class="row justify-content-center">
<div class="col-10">

<table class="table table-striped table-bordered">

<thead class="table-dark">
<tr>
<th>Lid</th>
<th>Lidnummer</th>
<th>Les</th>
<th>Leeftijd</th>
<th>Email</th>
</tr>
</thead>

<tbody>

<?php if (!empty($data)) : ?>

<?php foreach ($data as $ledenOverzicht) : ?>

<tr>

<td><?= htmlspecialchars($ledenOverzicht->leden); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->lidnummer); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->lessen); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->Leeftijd); ?></td>

<td><?= htmlspecialchars($ledenOverzicht->email); ?></td>




<?php endforeach; ?>

<?php else : ?>

<tr>
<td colspan="6" class="text-center">Geen gegevens gevonden</td>
</tr>

<?php endif; ?>

</tbody>
</table>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">

</body>
</html>