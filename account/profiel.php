<?php
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE id = ?");
$stmt->execute([$_SESSION['gebruiker_id']]);
$gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mijn Account – AJFX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <style>
        body { background: var(--gray-50, #f9fafb); }
        .page-wrapper { max-width: 560px; margin: 100px auto 4rem; padding: 0 1.5rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .page-header p { color: var(--gray-600); }
        .card { background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); margin-bottom: 1.25rem; }
        .card h2 { font-size: 1rem; font-weight: 700; color: var(--dark); margin-bottom: 1.25rem; padding-bottom: .75rem; border-bottom: 1px solid var(--gray-200); }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: .6rem 0; border-bottom: 1px solid var(--gray-100); }
        .info-row:last-child { border-bottom: none; }
        .info-label { font-size: .85rem; color: var(--gray-600); }
        .info-value { font-weight: 600; font-size: .95rem; color: var(--dark); }
        .acties { display: flex; flex-direction: column; gap: .75rem; }
        .btn-actie { display: flex; align-items: center; gap: .75rem; padding: .85rem 1.25rem; border: 1px solid var(--gray-200); border-radius: var(--radius-md); background: #fff; color: var(--dark); text-decoration: none; font-weight: 600; font-size: .95rem; transition: all .2s; }
        .btn-actie:hover { border-color: var(--primary); color: var(--primary); background: #f8f6ff; }
        .btn-actie.rood { color: #dc2626; }
        .btn-actie.rood:hover { border-color: #dc2626; background: #fef2f2; }
        .btn-actie .icoon { font-size: 1.1rem; }
    </style>
</head>
<body>
<nav class="navbar">
    <div class="nav-container">
        <div class="logo"><a href="../index.html" style="text-decoration:none;"><span class="logo-text">AJFX</span></a></div>
        <ul class="nav-menu">
            <li><a href="../index.html" class="nav-link">Home</a></li>
            <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
            <li><a href="../medewerker_overzicht/reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
            <li><a href="profiel.php" class="nav-link active">Account</a></li>
            <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerkers</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
    <div class="page-header">
        <h1>👤 Mijn Account</h1>
        <p>Welkom, <?= htmlspecialchars($gebruiker['naam']) ?>!</p>
    </div>

    <div class="card">
        <h2>Uw gegevens</h2>
        <div class="info-row">
            <span class="info-label">Naam</span>
            <span class="info-value"><?= htmlspecialchars($gebruiker['naam']) ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">E-mailadres</span>
            <span class="info-value"><?= htmlspecialchars($gebruiker['email']) ?></span>
        </div>
    </div>

    <div class="card">
        <h2>Acties</h2>
        <div class="acties">
            <a href="gegevens_wijzigen.php" class="btn-actie">
                <span class="icoon">✏️</span> Gegevens wijzigen
            </a>
            <a href="wachtwoord_wijzigen.php" class="btn-actie">
                <span class="icoon">🔒</span> Wachtwoord wijzigen
            </a>
            <a href="logout.php" class="btn-actie rood">
                <span class="icoon">🚪</span> Uitloggen
            </a>
        </div>
    </div>
</div>
</body>
</html>