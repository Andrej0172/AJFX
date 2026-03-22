<?php
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$dbFout = false;
$zoek   = isset($_GET['zoek']) ? trim($_GET['zoek']) : '';

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) { $dbFout = true; }

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
    if ($result === false) { $dbFout = true; }
}

function initialen($naam) {
    $delen = explode(' ', trim($naam));
    $init  = strtoupper(substr($delen[0], 0, 1));
    if (count($delen) > 1) $init .= strtoupper(substr(end($delen), 0, 1));
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

<nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-text">AJFX</span>
            </div>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="index.html" class="nav-link active">Home</a></li>
                <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
                <li><a href="../medewerker_overzicht/reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
                <li><a href="../account/login.php" class="nav-link">Account</a></li>
                <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
            </ul>
        </div>
    </nav>

    <h2>qq</h2>


    <div class="zoek-wrapper">
    <form method="GET" action="">
        <input 
            type="text" 
            name="zoek" 
            placeholder="Zoek op les, trainer of locatie..." 
            value="<?= htmlspecialchars($zoek) ?>"
            class="zoek-input"
        >
        <button type="submit" class="zoek-btn">🔍 Zoeken</button>
        <?php if ($zoek !== ''): ?>
            <a href="?" class="zoek-reset">✕ Wissen</a>
        <?php endif; ?>
    </form>
</div>

<main class="main">

<?php if ($dbFout): ?>
    <div class="foutmelding-wrapper">
        <div class="foutmelding-icon">⚠️</div>
        <div class="foutmelding">Er is iets misgegaan bij het laden van de lessen.</div>
        <p class="foutmelding-hint">Controleer de databaseverbinding en probeer het opnieuw.</p>
    </div>

<?php else:
    $rows = [];
    while ($row = $result->fetch_assoc()) $rows[] = $row;
    $aantalLessen = count($rows);
?>

    <!-- ── Ingepland tabel ── -->
    <div class="sectie-header">
        <h2>Ingepland</h2>
        <div class="sectie-lijn"></div>
        <?php if ($aantalLessen > 0): ?>
            <span class="sectie-badge"><?= $aantalLessen ?> lessen</span>
        <?php endif; ?>
    </div>

    <div class="tabel-wrapper">
        <table>
            <thead>
                <tr>
                    <th>Les</th><th>Trainer</th><th>Locatie</th><th>Datum</th><th>Tijd</th>
                </tr>
            </thead>
            <tbody>
            <?php if ($aantalLessen > 0): foreach ($rows as $row): ?>
                <tr>
                    <td class="td-les"><?= htmlspecialchars($row['lessen']) ?></td>
                    <td>
                        <div class="td-trainer">
                            <div class="avatar"><?= initialen($row['trainer']) ?></div>
                            <?= htmlspecialchars($row['trainer']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($row['locatie']) ?></td>
                    <td><span class="datum-chip">📅 <?= datumLeesbaar($row['datum']) ?></span></td>
                    <td><span class="tijd-chip">⏱ <?= htmlspecialchars(substr($row['tijd'], 0, 5)) ?></span></td>
                </tr>
            <?php endforeach; else: ?>
                <tr><td colspan="5">
                    <div class="geen-resultaat">
                        <span>🔍</span>
                        Geen lessen gevonden<?= $zoek !== '' ? ' voor "'.htmlspecialchars($zoek).'"' : '' ?>.
                    </div>
                </td></tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ── Alle lessen kaarten ── -->
    <div class="sectie-header">
        <h2>Alle lessen</h2>
        <div class="sectie-lijn"></div>
    </div>

    <div class="lessen-grid">
    <?php if ($aantalLessen > 0): foreach ($rows as $row): ?>
        <div class="les-card">
            <div class="les-card-top"></div>
            <div class="les-card-body">
                <div class="les-card-titel"><?= htmlspecialchars($row['lessen']) ?></div>
                <div class="les-card-meta">
                    <div class="les-card-meta-item">
                        <div class="icon">📅</div>
                        <span class="label"><?= datumLeesbaar($row['datum']) ?></span>
                    </div>
                    <div class="les-card-meta-item">
                        <div class="icon">⏱</div>
                        <span class="label"><?= htmlspecialchars(substr($row['tijd'], 0, 5)) ?></span>
                    </div>
                    <div class="les-card-meta-item">
                        <div class="icon">📍</div>
                        <span class="label"><?= htmlspecialchars($row['locatie']) ?></span>
                    </div>
                </div>
            </div>
            <div class="les-card-trainer">
                <div class="avatar"><?= initialen($row['trainer']) ?></div>
                <strong><?= htmlspecialchars($row['trainer']) ?></strong>
            </div>
        </div>
    <?php endforeach; else: ?>
        <p class="geen-resultaat"><span>📭</span> Geen lessen beschikbaar.</p>
    <?php endif; ?>
    </div>

<?php endif; ?>
</main>
</body>
</html>
<?php if (!$dbFout && isset($conn)) $conn->close(); ?>