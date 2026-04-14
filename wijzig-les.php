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

<!-- Nieuwe les -->
<div style="margin:20px 0;">
    <a href="insert-les.php">
        <button>+ Nieuwe les toevoegen</button>
    </a>
</div>

<!-- Zoekbalk -->
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

<table border="1">
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
            <td><?= htmlspecialchars($row['trainer']) ?></td>
            <td><?= htmlspecialchars($row['locatie']) ?></td>
            <td><?= datumLeesbaar($row['datum']) ?></td>
            <td><?= substr($row['tijd'], 0, 5) ?></td>

            <!-- ACTIE BLOK -->
            <td>

                <!-- Wijzigen -->
                <a href="wijzig-les.php?les=<?= urlencode($row['lessen']) ?>&datum=<?= $row['datum'] ?>&tijd=<?= $row['tijd'] ?>">
                    <button>Wijzigen</button>
                </a>

                <!-- ======================= -->
                <!-- ANNULEREN BLOK -->
                <!-- ======================= -->
                <a href="annuleer-les.php?les=<?= urlencode($row['lessen']) ?>&datum=<?= $row['datum'] ?>&tijd=<?= $row['tijd'] ?>"
                   onclick="return confirm('Weet je zeker dat je deze les wilt annuleren?');">
                    <button style="background:red;color:white;">Annuleren</button>
                </a>

            </td>
        </tr>
    <?php endforeach; ?>

    </tbody>
</table>

<?php endif; ?>

</main>

</body>
</html>