<?php
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php?melding=niet_ingelogd');
    exit;
}

require_once 'db.php';

$stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE id = ?");
$stmt->execute([$_SESSION['gebruiker_id']]);
$gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$gebruiker) {
    session_destroy();
    header('Location: login.php');
    exit;
}

$stmt = $pdo->prepare("
    SELECT r.datum, l.naam AS les_naam, l.dag, l.tijd
    FROM reserveringen r
    JOIN lessen l ON r.les_id = l.id
    WHERE r.gebruiker_id = ?
    ORDER BY r.datum ASC
");
$stmt->execute([$_SESSION['gebruiker_id']]);
$reserveringen = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accountoverzicht – AJFX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <style>
        .account-wrapper { max-width: 900px; margin: 0 auto; padding: 2rem 1.5rem 4rem; margin-top: 70px; }
        .account-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-200); }
        .account-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .account-header p { color: var(--gray-600); }
        .card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 1.5rem; margin-bottom: 1.5rem; }
        .card-title { font-size: 1.1rem; font-weight: 700; color: var(--dark); margin-bottom: 1.25rem; display: flex; align-items: center; gap: 0.5rem; }
        .info-row { display: flex; justify-content: space-between; align-items: center; padding: 0.6rem 0; border-bottom: 1px solid var(--gray-100); font-size: 0.95rem; }
        .info-row:last-child { border-bottom: none; }
        .info-row .label { color: var(--gray-600); }
        .info-row .value { font-weight: 600; color: var(--dark); }
        .badge { display: inline-block; padding: 0.25rem 0.75rem; border-radius: 999px; font-size: 0.8rem; font-weight: 700; }
        .badge-premium { background: #fef3c7; color: #d97706; }
        .badge-basis   { background: #f0fdf4; color: #16a34a; }
        .badge-pro     { background: #eff6ff; color: #2563eb; }
        .les-item { display: flex; justify-content: space-between; align-items: center; padding: 0.75rem; background: var(--gray-50); border-radius: var(--radius-md); margin-bottom: 0.6rem; font-size: 0.95rem; }
        .les-item:last-child { margin-bottom: 0; }
        .les-naam { font-weight: 600; color: var(--dark); }
        .les-detail { color: var(--gray-600); font-size: 0.85rem; }
        .les-datum { font-size: 0.85rem; color: var(--gray-600); text-align: right; }
        .geen-reserveringen { text-align: center; color: var(--gray-400); padding: 1rem; font-size: 0.95rem; }
        .action-buttons { display: flex; gap: 1rem; flex-wrap: wrap; margin-top: 1rem; }
        .btn-outline { background: transparent; color: var(--primary); border: 2px solid var(--primary); padding: 0.625rem 1.25rem; border-radius: var(--radius-md); font-family: var(--font-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-outline:hover { background: var(--primary); color: white; }
        .btn-danger { background: transparent; color: #dc2626; border: 2px solid #dc2626; padding: 0.625rem 1.25rem; border-radius: var(--radius-md); font-family: var(--font-main); font-size: 0.9rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-danger:hover { background: #dc2626; color: white; }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><span class="logo-text">AJFX</span></div>
            <ul class="nav-menu">
                <li><a href="../homepage/index.html" class="nav-link">Home</a></li>
                <li><a href="account.php" class="nav-link active">Account</a></li>
                <li><a href="logout.php" class="nav-link">Uitloggen</a></li>
            </ul>
        </div>
    </nav>

    <div class="account-wrapper">
        <div class="account-header">
            <h1>Welkom, <?= htmlspecialchars($gebruiker['naam']) ?>!</h1>
            <p>Hier vindt u uw persoonlijke gegevens en reserveringen.</p>
        </div>

        <div class="card">
            <div class="card-title">👤 Persoonlijke Gegevens</div>
            <div class="info-row"><span class="label">Naam</span><span class="value"><?= htmlspecialchars($gebruiker['naam']) ?></span></div>
            <div class="info-row"><span class="label">E-mailadres</span><span class="value"><?= htmlspecialchars($gebruiker['email']) ?></span></div>
            <div class="info-row"><span class="label">Lid sinds</span><span class="value"><?= date('d-m-Y', strtotime($gebruiker['aangemaakt_op'])) ?></span></div>
            <div class="info-row">
                <span class="label">Lidmaatschap</span>
                <span class="value">
                    <?php
                        $lid = $gebruiker['lidmaatschap'];
                        $class = match($lid) { 'Premium' => 'badge-premium', 'Pro' => 'badge-pro', default => 'badge-basis' };
                    ?>
                    <span class="badge <?= $class ?>"><?= htmlspecialchars($lid) ?></span>
                </span>
            </div>
        </div>

        <div class="card">
            <div class="card-title">🎫 Mijn Reserveringen</div>
            <?php if (empty($reserveringen)): ?>
                <p class="geen-reserveringen">U heeft nog geen reserveringen.</p>
            <?php else: ?>
                <?php foreach ($reserveringen as $res): ?>
                    <div class="les-item">
                        <div>
                            <div class="les-naam"><?= htmlspecialchars($res['les_naam']) ?></div>
                            <div class="les-detail"><?= htmlspecialchars($res['dag']) ?> om <?= substr($res['tijd'], 0, 5) ?></div>
                        </div>
                        <div class="les-datum"><?= date('d-m-Y', strtotime($res['datum'])) ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>

        <div class="card">
            <div class="card-title">⚙️ Instellingen</div>
            <div class="action-buttons">
                <a href="gegevens_wijzigen.php" class="btn-outline">✏️ Gegevens Wijzigen</a>
                <a href="wachtwoord_wijzigen.php" class="btn-outline">🔒 Wachtwoord Wijzigen</a>
                <a href="logout.php" class="btn-danger">🚪 Uitloggen</a>
            </div>
        </div>
    </div>
</body>
</html>
