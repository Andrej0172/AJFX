<?php
// =======================
// DATABASE INSTELLINGEN
// =======================
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

// Variabele om fouten bij te houden
$dbFout = false;

// Zoekterm ophalen uit URL (GET)
$zoek = isset($_GET['zoek']) ? trim($_GET['zoek']) : '';

// =======================
// DATABASE VERBINDING
// =======================
$conn = new mysqli($servername, $username, $password, $dbname);

// Check of verbinding gelukt is
if ($conn->connect_error) {
    $dbFout = true;
}

// =======================
// QUERY UITVOEREN
// =======================
if (!$dbFout) {

    // Als er gezocht wordt
    if ($zoek !== "") {

        // Beveilig invoer + lowercase maken
        $zoekSafe = $conn->real_escape_string(strtolower($zoek));

        // Zoek in meerdere kolommen
        $sql = "SELECT * FROM lessenoverzicht 
                WHERE LOWER(lessen)  LIKE '%$zoekSafe%'
                   OR LOWER(trainer) LIKE '%$zoekSafe%'
                   OR LOWER(locatie) LIKE '%$zoekSafe%'
                ORDER BY datum, tijd";

    } else {
        // Geen zoekterm → alles ophalen
        $sql = "SELECT * FROM lessenoverzicht ORDER BY datum, tijd";
    }

    // Query uitvoeren
    $result = $conn->query($sql);

    // Check op fout
    if ($result === false) {
        $dbFout = true;
    }
}

// =======================
// FUNCTIES
// =======================

// Maak initialen van naam (bijv. Jan Jansen → JJ)
function initialen($naam) {
    $delen = explode(' ', trim($naam));
    $init  = strtoupper(substr($delen[0], 0, 1));

    if (count($delen) > 1) {
        $init .= strtoupper(substr(end($delen), 0, 1));
    }

    return $init;
}

// Datum leesbaar maken
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

    <!-- CSS bestanden -->
    <link rel="stylesheet" href="../css/lessen-overzicht.css">
    <link rel="stylesheet" href="../css/css/lessss.css">
    <link href="../homepage/styles.css" rel="stylesheet">
</head>
<body>

<?php include 'header.html'; ?>



        <!-- Mobile menu knop -->
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span>
            <span></span>
            <span></span>
        </button>

        <!-- Navigatie links -->
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

<!-- =======================
     ZOEKBALK
======================= -->
<div class="zoek-wrapper">
    <form method="GET" action="">
        <!-- Zoek input -->
        <input 
            type="text" 
            name="zoek" 
            placeholder="Zoek op les, trainer of locatie..." 
            value="<?= htmlspecialchars($zoek) ?>"
            class="zoek-input"
        >

        <!-- Zoek knop -->
        <button type="submit" class="zoek-btn">🔍 Zoeken</button>

        <!-- Reset knop -->
        <?php if ($zoek !== ''): ?>
            <a href="?" class="zoek-reset">✕ Wissen</a>
        <?php endif; ?>
    </form>
</div>

<main class="main">

<?php if ($dbFout): ?>

    <!-- =======================
         FOUTMELDING
    ======================= -->
    <div class="foutmelding-wrapper">
        <div class="foutmelding-icon">⚠️</div>
        <div class="foutmelding">Er is iets misgegaan bij het laden van de lessen.</div>
        <p class="foutmelding-hint">Controleer de databaseverbinding en probeer het opnieuw.</p>
    </div>

<?php else:

    // Resultaten in array stoppen
    $rows = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }

    // Aantal lessen tellen
    $aantalLessen = count($rows);
?>

    <!-- =======================
         TABEL (INGEPLAND)
    ======================= -->
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
                    <th>Les</th>
                    <th>Trainer</th>
                    <th>Locatie</th>
                    <th>Datum</th>
                    <th>Tijd</th>
                </tr>
            </thead>
            <tbody>

            <?php if ($aantalLessen > 0): foreach ($rows as $row): ?>
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
                </tr>

            <?php endforeach; else: ?>
                <tr>
                    <td colspan="5">Geen lessen gevonden</td>
                </tr>
            <?php endif; ?>

            </tbody>
        </table>
    </div>

    <!-- =======================
         KAARTEN (ALLE LESSEN)
    ======================= -->
    <div class="sectie-header">
        <h2>Alle lessen</h2>
        <div class="sectie-lijn"></div>
    </div>


    <div class="lessen-grid">

    <?php if ($aantalLessen > 0): foreach ($rows as $row): ?>
        <div class="les-card">
            <div class="les-card-top"></div>

            <div class="les-card-body">
                <div class="les-card-titel">
                    <?= htmlspecialchars($row['lessen']) ?>
                </div>

                <div class="les-card-meta">

                    <div class="les-card-meta-item">
                        <div class="icon">📅</div>
                        <span><?= datumLeesbaar($row['datum']) ?></span>
                    </div>

                    <div class="les-card-meta-item">
                        <div class="icon">⏱</div>
                        <span><?= htmlspecialchars(substr($row['tijd'], 0, 5)) ?></span>
                    </div>

                    <div class="les-card-meta-item">
                        <div class="icon">📍</div>
                        <span><?= htmlspecialchars($row['locatie']) ?></span>
                    </div>

                </div>
            </div>

            <div class="les-card-trainer">
                <div class="avatar"><?= initialen($row['trainer']) ?></div>
                <strong><?= htmlspecialchars($row['trainer']) ?></strong>
            </div>
        </div>

    <?php endforeach; else: ?>
        <p>Geen lessen beschikbaar</p>
    <?php endif; ?>

    </div>

<?php endif; ?>
</main>

</body>
</html>

<?php
// =======================
// VERBINDING SLUITEN
// =======================
if (!$dbFout && isset($conn)) {
    $conn->close();
}
?>