<?php
// Database configuratie
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$dbFout = false;

$zoek = isset($_GET['zoek']) ? trim($_GET['zoek']) : '';

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    $dbFout = true;
}

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

<div style="margin:20px 0;">
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
</div>

<div class="zoek-wrapper">
    <form method="GET">
        <input type="text" name="zoek" value="<?= htmlspecialchars($zoek) ?>" placeholder="Zoek...">
        <button type="submit">Zoeken</button>
    </form>
</div>

<main class="main">

<?php if ($dbFout): ?>

    <p>Er is een fout opgetreden</p>

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
            <th>Wijzigen</th>
            <th>Annuleren</th>
        </tr>
    </thead>

    <tbody>

    <?php foreach ($rows as $row): ?>
        <tr>
            <td><?= htmlspecialchars($row['lessen']) ?></td>
            <td><?= htmlspecialchars($row['trainer']) ?></td>
            <td><?= htmlspecialchars($row['locatie']) ?></td>
            <td><?= datumLeesbaar($row['datum']) ?></td>
            <td><?= substr($row['tijd'], 0, 5) ?></td>

            <td>
                <a href="wijzig-les.php?les=<?= urlencode($row['lessen']) ?>&datum=<?= $row['datum'] ?>&tijd=<?= $row['tijd'] ?>">
                    <button>Wijzigen</button>
                </a>
            </td>

            <td>
                <a href="annuleer-les.php?les=<?= urlencode($row['lessen']) ?>&datum=<?= $row['datum'] ?>&tijd=<?= $row['tijd'] ?>"
                   onclick="return confirm('Weet je zeker dat je deze les wilt annuleren?');">
                    <button>Annuleren</button>
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