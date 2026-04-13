<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$reserveringen  = [];
$fout           = null;
$succes         = null;
$formulier_fout = null;
$filter_fout    = null;
$periode_stats  = null;

$simuleer_fout = isset($_GET['fout']) && $_GET['fout'] == '1';

$filter_actief = false;
$startdatum    = '';
$einddatum     = '';

if (isset($_GET['startdatum'], $_GET['einddatum']) && $_GET['startdatum'] !== '' && $_GET['einddatum'] !== '') {
    $startdatum    = $_GET['startdatum'];
    $einddatum     = $_GET['einddatum'];
    $filter_actief = true;
}

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
                $qs = http_build_query(array_filter([
                    'succes'     => '1',
                    'startdatum' => $startdatum,
                    'einddatum'  => $einddatum,
                ]));
                header("Location: reserveringsoverzicht.php?" . $qs);
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
        $result = $conn->query("SELECT Id, naam, lidnummer FROM ledenoverzicht ORDER BY naam ASC");
        if ($result) while ($row = $result->fetch_assoc()) $leden[] = $row;

        $result = $conn->query("SELECT Id, lesnaam, datum, tijd FROM lessenoverzicht ORDER BY datum, tijd ASC");
        if ($result) while ($row = $result->fetch_assoc()) $lessen[] = $row;

        $conn->close();
    }
}

// Reserveringen ophalen
if ($simuleer_fout) {
    $filter_fout = "Het overzicht kon niet worden geladen.";
} elseif ($filter_actief) {
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $filter_fout = "Het overzicht kon niet worden geladen.";
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
            JOIN ledenoverzicht  l  ON r.lid_id = l.Id
            JOIN lessenoverzicht lo ON r.les_id = lo.Id
            WHERE lo.datum BETWEEN ? AND ?
            ORDER BY lo.datum, lo.tijd
        ";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $startdatum, $einddatum);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) $reserveringen[] = $row;
            }
            $per_dag = [];
            foreach ($reserveringen as $r) {
                $per_dag[$r['datum']] = ($per_dag[$r['datum']] ?? 0) + 1;
            }
            ksort($per_dag);
            $periode_stats = ['totaal' => count($reserveringen), 'per_dag' => $per_dag];
        } else {
            $filter_fout = "Het overzicht kon niet worden geladen.";
        }
        $stmt->close();
        $conn->close();
    }
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
            JOIN ledenoverzicht  l  ON r.lid_id = l.Id
            JOIN lessenoverzicht lo ON r.les_id = lo.Id
            ORDER BY lo.datum, lo.tijd
        ";
        $result = $conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) $reserveringen[] = $row;
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

function formatDatum(string $datum): string {
    return date('d-m-Y', strtotime($datum));
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservering Overzicht</title>
    <link rel="stylesheet" href="reserveringsoverzicht.css">
    <link rel="stylesheet" href="../homepage/styles.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <span class="logo-text">AJFX</span>
        </div>
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-menu" id="navMenu">
            <li><a href="../index.html" class="nav-link active">Home</a></li>
            <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
            <li><a href="../reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
            <li><a href="../account/login.php" class="nav-link">Account</a></li>
            <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
        </ul>
    </div>
</nav>

<main>

    <?php if ($succes): ?>
        <div class="succes-blok">
            <div class="succes-icoon">✓</div>
            <p class="succes-tekst"><?= htmlspecialchars($succes) ?></p>
        </div>
    <?php endif; ?>

    <!-- Nieuw reserveringsformulier -->
    <div id="formulier-sectie" style="display: <?= $formulier_fout ? 'block' : 'none' ?>;">
        <h2 class="sectie-titel">Nieuwe reservering</h2>

        <?php if ($formulier_fout): ?>
            <div class="fout-blok">
                <div class="fout-icoon">!</div>
                <p class="fout-tekst"><?= htmlspecialchars($formulier_fout) ?></p>
            </div>
        <?php endif; ?>

        <form method="POST" action="reserveringsoverzicht.php">
            <?php if ($startdatum): ?>
                <input type="hidden" name="startdatum" value="<?= htmlspecialchars($startdatum) ?>">
            <?php endif; ?>
            <?php if ($einddatum): ?>
                <input type="hidden" name="einddatum" value="<?= htmlspecialchars($einddatum) ?>">
            <?php endif; ?>
            <div class="form-groep">
                <label>Lid *</label>
                <select name="lid_id">
                    <option value="">— Selecteer een lid —</option>
                    <?php foreach ($leden as $lid): ?>
                        <option value="<?= (int)$lid['Id'] ?>" <?= ($_POST['lid_id'] ?? '') == $lid['Id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($lid['naam']) ?> (<?= htmlspecialchars($lid['lidnummer']) ?>)
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
                            <?= htmlspecialchars($les['lesnaam']) ?> — <?= htmlspecialchars($les['datum']) ?> <?= htmlspecialchars($les['tijd']) ?>
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

    <!-- Periodefilter -->
    <div class="filter-sectie">
        <h2 class="sectie-titel">Overzicht per periode</h2>
        <form method="GET" action="reserveringsoverzicht.php" class="filter-form">
            <div class="form-groep">
                <label for="startdatum">Startdatum</label>
                <input type="date" id="startdatum" name="startdatum" value="<?= htmlspecialchars($startdatum) ?>" required>
            </div>
            <div class="form-groep">
                <label for="einddatum">Einddatum</label>
                <input type="date" id="einddatum" name="einddatum" value="<?= htmlspecialchars($einddatum) ?>" required>
            </div>
            <div class="form-acties">
                <button type="submit" class="knop-primair">Toon overzicht</button>
                <?php if ($filter_actief): ?>
                    <a href="reserveringsoverzicht.php" class="knop-secundair">Wis filter</a>
                <?php endif; ?>
            </div>
        </form>
    </div>

    <!-- Periode statistieken -->
    <?php if ($filter_actief && $filter_fout): ?>
        <div class="fout-blok">
            <div class="fout-icoon">!</div>
            <p class="fout-tekst"><?= htmlspecialchars($filter_fout) ?></p>
        </div>

    <?php elseif ($filter_actief && $periode_stats !== null): ?>
        <div class="periode-stats">
            <div class="stat-kaart">
                <p class="stat-label">Totaal reserveringen</p>
                <p class="stat-waarde"><?= $periode_stats['totaal'] ?></p>
                <p class="stat-sub"><?= htmlspecialchars(formatDatum($startdatum)) ?> – <?= htmlspecialchars(formatDatum($einddatum)) ?></p>
            </div>

            <?php if (!empty($periode_stats['per_dag'])): ?>
                <div class="uitsplitsing">
                    <h3 class="sectie-titel">Uitsplitsing per dag</h3>
                    <div class="dag-rijen">
                        <?php foreach ($periode_stats['per_dag'] as $dag => $aantal): ?>
                            <div class="dag-rij">
                                <span class="dag-datum"><?= htmlspecialchars(formatDatum($dag)) ?></span>
                                <div class="dag-balk-wrapper">
                                    <div class="dag-balk" style="width: <?= min(100, ($aantal / max($periode_stats['per_dag'])) * 100) ?>%"></div>
                                </div>
                                <span class="dag-aantal"><?= $aantal ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <?php if (empty($reserveringen)): ?>
            <p class="aantal-tekst">Geen reserveringen gevonden voor deze periode.</p>
        <?php endif; ?>

    <?php endif; ?>

    <!-- Overzichtstabel -->
    <?php if (!$filter_actief && $fout): ?>
        <div class="fout-blok">
            <div class="fout-icoon">!</div>
            <p class="fout-tekst"><?= htmlspecialchars($fout) ?></p>
        </div>

    <?php elseif (!$filter_actief && empty($reserveringen)): ?>
        <p class="aantal-tekst">Geen reserveringen gevonden.</p>

    <?php elseif (!empty($reserveringen)): ?>
        <p class="aantal-tekst"><?= count($reserveringen) ?> reserveringen<?= $filter_actief ? ' in geselecteerde periode' : '' ?></p>

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
                            <td><?= htmlspecialchars(formatDatum($r['datum'])) ?></td>
                            <td><?= htmlspecialchars(substr($r['tijd'], 0, 5)) ?></td>
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