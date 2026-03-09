<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$reserveringen = [];
$fout          = null;

$simuleer_fout = isset($_GET['fout']) && $_GET['fout'] == '1';

if ($simuleer_fout) {
    $fout = "Er is iets misgegaan bij het laden van de reserveringen.";
} else {
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        $fout = "Er is iets misgegaan bij het laden van de reserveringen.";
    } else {
        $sql = "
            SELECT 
                r.Id,
                l.leden        AS lid_naam,
                l.lidnummer,
                lo.lessen      AS les_naam,
                lo.trainer,
                lo.datum,
                lo.tijd,
                lo.locatie
            FROM reserveringen r
            JOIN ledenoverzicht l   ON r.lid_id = l.Id
            JOIN lessenoverzicht lo ON r.les_id = lo.Id
            ORDER BY lo.datum, lo.tijd
        ";

        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $reserveringen[] = $row;
            }
        }

        $conn->close();
    }
}

function initialen(string $naam): string {
    $delen = explode(' ', $naam);
    $eerste = strtoupper(substr($delen[0], 0, 1));
    $laatste = strtoupper(substr(end($delen), 0, 1));
    return $eerste . $laatste;
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservering Overzicht</title>
    <link rel="stylesheet" href="reserveringsoverzicht.css">
</head>
<body>

<header>
    <div class="header-inner">
        <div>
            <p class="header-label">Beheer</p>
            <h1 class="header-titel">Reserveringen</h1>
        </div>
        <div class="header-acties">
            <?php if ($simuleer_fout): ?>
                <a href="reserveringsoverzicht.php" class="knop-secundair">✓ Normaal</a>
            <?php else: ?>
                <a href="reserveringsoverzicht.php?fout=1" class="knop-secundair">⚠ Fout aan</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main>
    <?php if ($fout): ?>
        <!-- Unhappy scenario -->
        <div class="fout-blok">
            <div class="fout-icoon">!</div>
            <p class="fout-tekst"><?= htmlspecialchars($fout) ?></p>
        </div>

    <?php elseif (empty($reserveringen)): ?>
        <p class="aantal-tekst">Geen reserveringen gevonden.</p>

    <?php else: ?>
        <!-- Happy scenario -->
        <p class="aantal-tekst"><?= count($reserveringen) ?> reserveringen</p>

        <div class="tabel-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Lid</th>
                        <th>Lidnummer</th>
                        <th>Les</th>
                        <th>Trainer</th>
                        <th>Datum</th>
                        <th>Tijd</th>
                        <th>Locatie</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reserveringen as $r): ?>
                        <tr>
                            <td>
                                <div class="naam-cel">
                                    <div class="avatar"><?= htmlspecialchars(initialen($r['lid_naam'])) ?></div>
                                    <span class="naam"><?= htmlspecialchars($r['lid_naam']) ?></span>
                                </div>
                            </td>
                            <td><span class="badge"><?= htmlspecialchars($r['lidnummer']) ?></span></td>
                            <td><?= htmlspecialchars($r['les_naam']) ?></td>
                            <td><?= htmlspecialchars($r['trainer']) ?></td>
                            <td><?= htmlspecialchars($r['datum']) ?></td>
                            <td><?= htmlspecialchars($r['tijd']) ?></td>
                            <td><?= htmlspecialchars($r['locatie']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

    <?php endif; ?>
</main>

</body>
</html>