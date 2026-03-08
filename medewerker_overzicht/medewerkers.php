<?php
$host     = 'localhost';
$dbname   = 'medewerker_overzicht';
$username = 'root';
$password = '';

$medewerkers = [];
$fout        = null;

$simuleer_fout = isset($_GET['fout']) && $_GET['fout'] == '1';

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
            <a href="medewerker_toevoegen.php" class="knop-primair">+ Medewerker toevoegen</a>
        </div>
    </div>
</header>

<main>
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

</body>
</html>