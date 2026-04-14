<?php
// Database instellingen
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$dbFout = false;

// Zoekterm ophalen uit URL
$zoek = isset($_GET['zoek']) ? trim($_GET['zoek']) : '';

// Database verbinding maken
$conn = new mysqli($servername, $username, $password, $dbname);

// Check verbinding
if ($conn->connect_error) {
    $dbFout = true;
}

// Query uitvoeren
if (!$dbFout) {

    if ($zoek !== "") {

        $zoekSafe = $conn->real_escape_string(strtolower($zoek));

        $sql = "SELECT * FROM lessenoverzicht 
                WHERE LOWER(lessen)  LIKE '%$zoekSafe%'
                   OR LOWER(trainer) LIKE '%$zoekSafe%'
                   OR LOWER(locatie) LIKE '%$zoekSafe%'
                ORDER BY datum, tijd";

    } else {
        $sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";
    }

    $result = $conn->query($sql);

    if ($result === false) {
        $dbFout = true;
    }
}

// functies
function initialen($naam) {
    $delen = explode(' ', trim($naam));
    $init  = strtoupper(substr($delen[0], 0, 1));

    if (count($delen) > 1) {
        $init .= strtoupper(substr(end($delen), 0, 1));
    }

    return $init;
}

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

    <link rel="stylesheet" href="../css/lessen-overzicht.css">
    <link rel="stylesheet" href="../css/css/lessss.css">
    <link href="../homepage/styles.css" rel="stylesheet">
</head>
<body>

<?php include 'header.html'; ?>

<!-- knop nieuwe les -->
<div style="margin:20px 0;">
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
</div>

<!-- zoekbalk -->
<div class="zoek-wrapper">
    <form method="GET">
        <input 
            type="text" 
            name="zoek" 
            placeholder="Zoek op les, trainer of locatie..." 
            value="<?= htmlspecialchars($zoek) ?>"
            class="zoek-input"
        >

        <button type="submit" class="zoek-btn">Zoeken</button>

        <?php if ($zoek !== ''): ?>
            <a href="?" class="zoek-reset">Wissen</a>
        <?php endif; ?>
    </form>
</div>

<main class="main">

<?php if ($dbFout): ?>

    <div class="foutmelding-wrapper">
        <div class="foutmelding-icon">⚠️</div>
        <div class="foutmelding">Er is iets misgegaan bij het laden van de lessen.</div>
    </div>

<?php else: ?>

<?php
$rows = [];
while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
}
?>

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

                    <td>
                        <a href="wijzig-les.php?id=<?= $row['id'] ?>">
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
if (!$dbFout && isset($conn)) {
    $conn->close();
}
?>