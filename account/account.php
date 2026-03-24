<?php
session_start();
require_once 'db.php';

$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam       = trim($_POST['naam']       ?? '');
    $email      = trim($_POST['email']      ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    if (empty($naam) || empty($email) || empty($wachtwoord)) {
        $error_message = 'Vul alle verplichte velden in (naam, e-mail, wachtwoord).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Voer een geldig e-mailadres in.';
    } elseif (strlen($wachtwoord) < 6) {
        $error_message = 'Het wachtwoord moet minimaal 6 tekens bevatten.';
    } else {
        if ($pdo === null) {
            // UNHAPPY: database niet bereikbaar
            $error_message = 'Uw account kon niet worden aangemaakt door een technische storing. Probeer het later opnieuw.';
        } else {
            try {
                $check = $pdo->prepare('SELECT id FROM gebruikers WHERE email = ?');
                $check->execute([$email]);
                if ($check->fetch()) {
                    $error_message = 'Dit e-mailadres is al in gebruik. Probeer in te loggen.';
                } else {
                    // HAPPY: account opslaan
                    $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare('INSERT INTO gebruikers (naam, email, wachtwoord) VALUES (?, ?, ?)');
                    $stmt->execute([$naam, $email, $hash]);
                    $success_message = 'Uw account is succesvol aangemaakt! U kunt nu inloggen.';
                }
            } catch (PDOException $e) {
                // UNHAPPY: databasefout bij opslaan
                $error_message = 'Uw account kon niet worden aangemaakt door een technische storing. Probeer het later opnieuw.';
            }
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
        .page-wrapper { max-width: 500px; margin: 70px auto 4rem; padding: 2rem 1.5rem; }
        .page-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-200); }
        .page-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .page-header p { color: var(--gray-600); }
        .card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; font-size: 0.9rem; color: var(--dark); margin-bottom: 0.4rem; }
        .form-group input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-family: var(--font-main); font-size: 1rem; color: var(--dark); transition: border-color 0.2s; box-sizing: border-box; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .success-msg { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .btn-row { display: flex; gap: 1rem; margin-top: 1.5rem; align-items: center; }
        .link-secondary { color: var(--gray-600); font-size: 0.9rem; text-decoration: none; }
        .link-secondary:hover { color: var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><span class="logo-text">AJFX</span></div>
            <ul class="nav-menu">
                <li><a href="../index.html" class="nav-link">Home</a></li>
                <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
                <li><a href="../medewerker_overzicht/reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
                <li><a href="login.php" class="nav-link active">Account</a></li>
                <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Account aanmaken</h1>
            <p>Maak een gratis account aan om verder te gaan</p>
        </div>
        <div class="card">
            <?php if ($success_message): ?>
                <div class="success-msg">✅ <?= htmlspecialchars($success_message) ?></div>
                <a href="login.php" class="btn-primary" style="display:inline-block;text-decoration:none;text-align:center;">Naar inlogpagina</a>
            <?php else: ?>
                <?php if ($error_message): ?>
                    <div class="error-msg">⚠️ <?= htmlspecialchars($error_message) ?></div>
                <?php endif; ?>
                <form method="POST" action="account.php">
                    <div class="form-group">
                        <label for="naam">Naam</label>
                        <input type="text" id="naam" name="naam" required value="<?= htmlspecialchars($_POST['naam'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="email">E-mailadres</label>
                        <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                    </div>
                    <div class="form-group">
                        <label for="wachtwoord">Wachtwoord</label>
                        <input type="password" id="wachtwoord" name="wachtwoord" required minlength="6">
                    </div>
                    <div class="btn-row">
                        <button type="submit" class="btn-primary">Account aanmaken</button>
                        <a href="login.php" class="link-secondary">Al een account? Inloggen</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>