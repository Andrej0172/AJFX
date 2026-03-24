<?php
session_start();

if (isset($_SESSION['gebruiker_id'])) {
    header('Location: profiel.php');
    exit;
}

require_once 'db.php';

$succes = '';
$fout   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam       = trim($_POST['naam']       ?? '');
    $email      = trim($_POST['email']      ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    if (empty($naam) || empty($email) || empty($wachtwoord)) {
        $fout = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fout = 'Voer een geldig e-mailadres in.';
    } elseif (strlen($wachtwoord) < 6) {
        $fout = 'Het wachtwoord moet minimaal 6 tekens bevatten.';
    } elseif ($pdo === null) {
        $fout = 'Account aanmaken is tijdelijk niet mogelijk. Probeer het later opnieuw.';
    } else {
        try {
            $check = $pdo->prepare('SELECT id FROM gebruikers WHERE email = ?');
            $check->execute([$email]);
            if ($check->fetch()) {
                $fout = 'Dit e-mailadres is al in gebruik.';
            } else {
                $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare('INSERT INTO gebruikers (naam, email, wachtwoord) VALUES (?, ?, ?)');
                $stmt->execute([$naam, $email, $hash]);
                $succes = 'Account aangemaakt! U kunt nu inloggen.';
            }
        } catch (PDOException $e) {
            $fout = 'Account aanmaken is tijdelijk niet mogelijk. Probeer het later opnieuw.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account aanmaken – AJFX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <style>
        body { background: var(--gray-50, #f9fafb); }
        .page-wrapper { max-width: 480px; margin: 100px auto 4rem; padding: 0 1.5rem; }
        .page-header { margin-bottom: 2rem; }
        .page-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .page-header p { color: var(--gray-600); }
        .card { background: #fff; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; font-size: 0.9rem; margin-bottom: 0.4rem; color: var(--dark); }
        .form-group input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-size: 1rem; font-family: var(--font-main); box-sizing: border-box; transition: border-color .2s; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        .fout    { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: var(--radius-md); padding: .75rem 1rem; margin-bottom: 1.25rem; font-size: .9rem; }
        .succes  { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; border-radius: var(--radius-md); padding: .75rem 1rem; margin-bottom: 1.25rem; font-size: .9rem; }
        .btn-row { display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; }
        .link-sec { color: var(--gray-600); font-size: .9rem; text-decoration: none; }
        .link-sec:hover { color: var(--primary); }
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
            <li><a href="login.php" class="nav-link active">Account</a></li>
            <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerkers</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
    <div class="page-header">
        <h1>Account aanmaken</h1>
        <p>Maak een gratis account aan</p>
    </div>
    <div class="card">
        <?php if ($succes): ?>
            <div class="succes">✅ <?= htmlspecialchars($succes) ?></div>
            <a href="login.php" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center;">Naar inlogpagina</a>
        <?php else: ?>
            <?php if ($fout): ?>
                <div class="fout">⚠️ <?= htmlspecialchars($fout) ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="naam">Naam</label>
                    <input type="text" id="naam" name="naam" required
                           value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" required
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="wachtwoord">Wachtwoord <small style="font-weight:400;color:var(--gray-600);">(min. 6 tekens)</small></label>
                    <input type="password" id="wachtwoord" name="wachtwoord" required minlength="6">
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn-primary">Account aanmaken</button>
                    <a href="login.php" class="link-sec">Al een account? Inloggen →</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>
</body>
</html>