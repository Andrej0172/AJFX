<?php
session_start();
require_once 'db.php';

if (isset($_SESSION['gebruiker_id'])) {
    header('Location: ../index.html');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email      = trim($_POST['email']      ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    if (empty($email) || empty($wachtwoord)) {
        $error_message = 'Vul uw e-mailadres en wachtwoord in.';
    } else {
        if ($pdo === null) {
            // UNHAPPY: database/authenticatieservice niet bereikbaar
            $error_message = 'Inloggen is tijdelijk niet mogelijk door een technische storing. Probeer het later opnieuw.';
        } else {
            try {
                $stmt = $pdo->prepare('SELECT id, naam, wachtwoord FROM gebruikers WHERE email = ?');
                $stmt->execute([$email]);
                $gebruiker = $stmt->fetch();

                if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
                    // HAPPY: inloggen gelukt
                    session_regenerate_id(true);
                    $_SESSION['gebruiker_id']   = $gebruiker['id'];
                    $_SESSION['gebruiker_naam'] = $gebruiker['naam'];
                    header('Location: ../index.html');
                    exit;
                } else {
                    // UNHAPPY: verkeerde gegevens
                    $error_message = 'Ongeldige e-mail of wachtwoord. Controleer uw gegevens en probeer opnieuw.';
                }
            } catch (PDOException $e) {
                // UNHAPPY: time-out of serverfout
                $error_message = 'Inloggen is tijdelijk niet mogelijk door een technische storing. Probeer het later opnieuw.';
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
    <title>Inloggen – AJFX</title>
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
            <h1>Inloggen</h1>
            <p>Log in op uw AJFX account</p>
        </div>
        <div class="card">
            <?php if ($error_message): ?>
                <div class="error-msg">⚠️ <?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
                </div>
                <div class="form-group">
                    <label for="wachtwoord">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" required>
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn-primary">Inloggen</button>
                    <a href="account.php" class="link-secondary">Nog geen account? Registreren</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>