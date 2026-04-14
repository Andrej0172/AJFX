<?php
// Database configuratie
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

// Status voor databasefouten
$dbFout = false;

// Zoekterm uit URL ophalen
$zoek = isset($_GET['zoek']) ? trim($_GET['zoek']) : '';

// Database verbinding maken
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleren of de verbinding is gelukt
if ($conn->connect_error) {
    $dbFout = true;
}

// Query opbouwen en uitvoeren
if (!$dbFout) {

    // Wanneer er een zoekterm is ingevuld
    if ($zoek !== "") {

        // Zoekterm veilig maken en omzetten naar lowercase
        $zoekSafe = $conn->real_escape_string(strtolower($zoek));

        // Zoeken in meerdere kolommen
        $sql = "SELECT * FROM lessenoverzicht 
                WHERE LOWER(lessen)  LIKE '%$zoekSafe%'
                   OR LOWER(trainer) LIKE '%$zoekSafe%'
                   OR LOWER(locatie) LIKE '%$zoekSafe%'
                ORDER BY datum, tijd";

    } else {
        // Geen zoekterm → alle lessen ophalen
        $sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";
    }

    // Query uitvoeren
    $result = $conn->query($sql);

    // Controleren op queryfouten
    if ($result === false) {
        $dbFout = true;
    }
}

// Functie: initialen maken van een naam (bijv. Jan Jansen → JJ)
function initialen($naam) {
    $delen = explode(' ', trim($naam));
    $init  = strtoupper(substr($delen[0], 0, 1));

    if (count($delen) > 1) {
        $init .= strtoupper(substr(end($delen), 0, 1));
    }

    return $init;
}

// Functie: datum omzetten naar leesbaar formaat
function datumLeesbaar($datum) {
    $ts = strtotime($datum);
    return $ts ? date('d M Y', $ts) : $datum;
}
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lessen Overzicht</title>

    <!-- Stylesheets -->
    <link rel="stylesheet" href="../css/lessen-overzicht.css">
    <link rel="stylesheet" href="../css/css/lessss.css">
    <link href="../homepage/styles.css" rel="stylesheet">
</head>
<body>

<?php include 'header.html'; ?>

<!-- Knop: nieuwe les toevoegen -->
<div style="margin:20px 0;">
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
</div>

<!-- Zoekbalk -->
<div class="zoek-wrapper">
    <form method="GET" action="">
        
        <!-- Zoekveld -->
        <input 
            type="text" 
            name="zoek" 
            placeholder="Zoek op les, trainer of locatie..." 
            value="<?= htmlspecialchars($zoek) ?>"
            class="zoek-input"
        >

        <!-- Zoekknop -->
        <button type="submit" class="zoek-btn">🔍 Zoeken</button>

        <!-- Reset zoekopdracht -->
        <?php if ($zoek !== ''): ?>
            <a href="?" class="zoek-reset">✕ Wissen</a>
        <?php endif; ?>
    </form>
</div>

<main class="main">

<?php if ($dbFout): ?>

    <!-- Foutmelding bij databaseproblemen -->
    <div class="foutmelding-wrapper">
        <div class="foutmelding-icon">⚠️</div>
        <div class="foutmelding">Er is iets misgegaan bij het laden van de lessen.</div>
    </div>

<?php else: ?>

<?php
// Resultaten opslaan in array
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
?>

    <!-- Overzichtstabel -->
    <div class="tabel-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Les</th>
                    <th>Trainer</th>
                    <th>Locatie</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                    <th>Actie</th>
                </tr>
            </thead>
            <tbody>

            <?php foreach ($rows as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['lessen']) ?></td>

                    <td>
                        <div class="td-trainer">
                            <div class="avatar"><?= initialen($row['trainer']) ?></div>
                            <?= htmlspecialchars($row['trainer']) ?>
                        </div>
                    </td>

                    <td><?= htmlspecialchars($row['locatie']) ?></td>
                    <td><?= datumLeesbaar($row['datum']) ?></td>
                    <td><?= htmlspecialchars(substr($row['tijd'], 0, 5)) ?></td>

                    <!-- Actie: les wijzigen -->
                    <td>
                        <a href="wijzig-les.php?les=<?= urlencode($row['lessen']) ?>&datum=<?= $row['datum'] ?>&tijd=<?= $row['tijd'] ?>">
                            <button>Wijzigen</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>
    </div>

<?php endif; ?>

</main>

</body>
</html>

<?php
// Databaseverbinding sluiten
if (!$dbFout && isset($conn)) {
    $conn->close();
}
?>