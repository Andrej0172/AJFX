<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$reserveringen  = [];
$fout           = null;
$succes         = null;
$formulier_fout = null;

$simuleer_fout = isset($_GET['fout']) && $_GET['fout'] == '1';

// Reservering toevoegen via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $lid_id = trim($_POST['lid_id'] ?? '');
    $les_id = trim($_POST['les_id'] ?? '');

    if (!$lid_id || !$les_id) {
        $formulier_fout = "Selecteer een lid en een les.";
    } else {
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            $formulier_fout = "Technische storing, reservering is mogelijk niet opgeslagen.";
        } else {
            $stmt = $conn->prepare("INSERT INTO reserveringen (lid_id, les_id) VALUES (?, ?)");
            $stmt->bind_param("ii", $lid_id, $les_id);
            if ($stmt->execute()) {
                header("Location: reserveringsoverzicht.php?succes=1");
                exit;
            } else {
                $formulier_fout = "Technische storing, reservering is mogelijk niet opgeslagen.";
            }
            $stmt->close();
            $conn->close();
        }
    }
}

if (isset($_GET['succes'])) {
    $succes = "Reservering succesvol toegevoegd.";
}

// Leden en lessen ophalen voor dropdowns
$leden  = [];
$lessen = [];

if (!$simuleer_fout) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if (!$conn->connect_error) {
        $result = $conn->query("SELECT Id, naam AS leden, lidnummer FROM ledenoverzicht ORDER BY naam ASC");
        if ($result) while ($row = $result->fetch_assoc()) $leden[] = $row;

        $result = $conn->query("SELECT Id, lesnaam AS lessen, datum, tijd FROM lessenoverzicht ORDER BY datum, tijd ASC");
        if ($result) while ($row = $result->fetch_assoc()) $lessen[] = $row;

        $conn->close();
    }
}

// Reserveringen ophalen
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
                l.naam       AS lid_naam,
                l.lidnummer,
                lo.lesnaam   AS les_naam,
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
    $delen   = explode(' ', $naam);
    $eerste  = strtoupper(substr($delen[0], 0, 1));
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
            <button class="knop-primair" onclick="toggleFormulier()">+ Nieuwe reservering</button>
        </div>
    </div>
</header>

<main>

    <?php if ($succes): ?>
        <div class="succes-blok">
            <div class="succes-icoon">✓</div>
            <p class="succes-tekst"><?= htmlspecialchars($succes) ?></p>
        </div>
    <?php endif; ?>

    <!-- Formulier -->
    <div id="formulier-sectie" style="display: <?= $formulier_fout ? 'block' : 'none' ?>;">
        <h2 class="sectie-titel">Nieuwe reservering</h2>

        <?php if ($formulier_fout): ?>
            <div class="fout-blok">
                <div class="fout-icoon">!</div>
                <p class="fout-tekst"><?= htmlspecialchars($formulier_fout) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="reserveringsoverzicht.php">
            <div class="form-groep">
                <label>Lid *</label>
                <select name="lid_id">
                    <option value="">— Selecteer een lid —</option>
                    <?php foreach ($leden as $lid): ?>
                        <option value="<?= (int)$lid['Id'] ?>" <?= ($_POST['lid_id'] ?? '') == $lid['Id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lid['leden']) ?> (<?= htmlspecialchars($lid['lidnummer']) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-groep">
                <label>Les *</label>
                <select name="les_id">
                    <option value="">— Selecteer een les —</option>
                    <?php foreach ($lessen as $les): ?>
                        <option value="<?= (int)$les['Id'] ?>" <?= ($_POST['les_id'] ?? '') == $les['Id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($les['lessen']) ?> — <?= htmlspecialchars($les['datum']) ?> <?= htmlspecialchars($les['tijd']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-acties">
                <button type="button" class="knop-secundair" onclick="toggleFormulier()">Annuleren</button>
                <button type="submit" class="knop-primair">Opslaan</button>
            </div>
        </form>
    </div>

    <!-- Overzicht -->
    <?php if ($fout): ?>
        <div class="fout-blok">
            <div class="fout-icoon">!</div>
            <p class="fout-tekst"><?= htmlspecialchars($fout) ?></p>
        </div>

    <?php elseif (empty($reserveringen)): ?>
        <p class="aantal-tekst">Geen reserveringen gevonden.</p>

    <?php else: ?>
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

<script>
function toggleFormulier() {
    const sectie = document.getElementById('formulier-sectie');
    sectie.style.display = sectie.style.display === 'none' ? 'block' : 'none';
}
</script>

</body>
</html>