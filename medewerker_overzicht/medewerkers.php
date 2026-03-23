<?php
$host     = 'localhost';
$dbname   = 'medewerker_overzicht';
$username = 'root';
$password = '';

$medewerkers  = [];
$fout         = null;
$succes       = null;
$formulier_fout = null;

$simuleer_fout = isset($_GET['fout']) && $_GET['fout'] == '1';

// Medewerker toevoegen via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam     = trim($_POST['naam'] ?? '');
    $functie  = trim($_POST['functie'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $afdeling = trim($_POST['afdeling'] ?? '');

    if (!$naam || !$functie || !$email || !$afdeling) {
        $formulier_fout = "Vul alle verplichte velden in.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $formulier_fout = "Vul een geldig e-mailadres in.";
    } else {
        try {
            $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $stmt = $pdo->prepare("INSERT INTO medewerkers (naam, functie, email, afdeling) VALUES (?, ?, ?, ?)");
            $stmt->execute([$naam, $functie, $email, $afdeling]);

            header("Location: medewerkers.php?succes=1");
            exit;

        } catch (PDOException $e) {
            $formulier_fout = "Technische storing, medewerker is mogelijk niet opgeslagen.";
        }
    }
}

if (isset($_GET['succes'])) {
    $succes = "Medewerker succesvol toegevoegd.";
}

// Medewerkers ophalen
if ($simuleer_fout) {
    $fout = "Er is iets misgegaan bij het laden van de medewerkers.";
} else {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $pdo->query("SELECT id, naam, functie, email, afdeling FROM medewerkers ORDER BY naam ASC");
        $medewerkers = $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $fout = "Er is iets misgegaan bij het laden van de medewerkers.";
    }
}

function initialen(string $naam): string {
    $delen  = explode(' ', $naam);
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
    <title>Medewerkers</title>
    <link rel="stylesheet" href="medewerkers.css">
</head>
<body>

<header>
    <div class="header-inner">
        <div>
            <p class="header-label">Beheer</p>
            <h1 class="header-titel">Medewerkers</h1>
        </div>
        <div class="header-acties">
            <?php if ($simuleer_fout): ?>
                <a href="medewerkers.php" class="knop-secundair">✓ Normaal</a>
            <?php else: ?>
                <a href="medewerkers.php?fout=1" class="knop-secundair">⚠ Fout aan</a>
            <?php endif; ?>
            <button class="knop-primair" onclick="toggleFormulier()">+ Medewerker toevoegen</button>
        </div>
    </div>
</header>

<main>

    <?php if ($succes): ?>
        <div class="succes-blok">
            <p><?= htmlspecialchars($succes) ?></p>
        </div>
    <?php endif; ?>

    <!-- Toevoegen formulier (verborgen by default) -->
    <div id="formulier-sectie" style="display: <?= $formulier_fout ? 'block' : 'none' ?>;">
        <h2 class="sectie-titel">Medewerker toevoegen</h2>

        <?php if ($formulier_fout): ?>
            <div class="fout-blok">
                <div class="fout-icoon">!</div>
                <div>
                    <p class="fout-tekst"><?= htmlspecialchars($formulier_fout) ?></p>
                </div>
            </div>
        <?php endif; ?>

        <form method="POST" action="medewerkers.php">
            <div class="form-groep">
                <label>Naam *</label>
                <input type="text" name="naam" value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>">
            </div>
            <div class="form-groep">
                <label>Functie *</label>
                <input type="text" name="functie" value="<?= htmlspecialchars($_POST['functie'] ?? '') ?>">
            </div>
            <div class="form-groep">
                <label>E-mailadres *</label>
                <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <div class="form-groep">
                <label>Afdeling *</label>
                <input type="text" name="afdeling" value="<?= htmlspecialchars($_POST['afdeling'] ?? '') ?>">
            </div>
            <div class="form-acties">
                <button type="button" class="knop-secundair" onclick="toggleFormulier()">Annuleren</button>
                <button type="submit" class="knop-primair">Toevoegen</button>
            </div>
        </form>
    </div>

    <!-- Medewerkers overzicht -->
    <?php if ($fout): ?>
        <div class="fout-blok">
            <div class="fout-icoon">!</div>
            <div>
                <p class="fout-tekst"><?= htmlspecialchars($fout) ?></p>
            </div>
        </div>

    <?php else: ?>
        <p class="aantal-tekst"><?= count($medewerkers) ?> medewerkers</p>

        <div class="tabel-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>Medewerker</th>
                        <th>Functie</th>
                        <th>Afdeling</th>
                        <th>E-mailadres</th>
                        <th class="rechts">Acties</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($medewerkers as $medewerker): ?>
                        <tr>
                            <td>
                                <div class="naam-cel">
                                    <div class="avatar"><?= htmlspecialchars(initialen($medewerker['naam'])) ?></div>
                                    <span class="naam"><?= htmlspecialchars($medewerker['naam']) ?></span>
                                </div>
                            </td>
                            <td><?= htmlspecialchars($medewerker['functie']) ?></td>
                            <td><span class="badge"><?= htmlspecialchars($medewerker['afdeling']) ?></span></td>
                            <td>
                                <a class="email-link" href="mailto:<?= htmlspecialchars($medewerker['email']) ?>">
                                    <?= htmlspecialchars($medewerker['email']) ?>
                                </a>
                            </td>
                            <td class="rechts">
                                <a class="knop-secundair" href="medewerker_bewerken.php?id=<?= (int)$medewerker['id'] ?>">Bewerken</a>
                            </td>
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