<?php
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';

try {
    // HAPPY: sessie correct beëindigen
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }

    if (!session_destroy()) {
        throw new Exception('Sessie kon niet worden verwijderd.');
    }

    header('Location: login.php');
    exit;

} catch (Exception $e) {
    // UNHAPPY: uitloggen mislukt
    $error_message = 'Uitloggen is niet volledig gelukt door een technische storing. Probeer het opnieuw.';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Uitloggen – AJFX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <style>
        .page-wrapper { max-width: 500px; margin: 70px auto 4rem; padding: 2rem 1.5rem; }
        .page-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-200); }
        .page-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .btn-row { display: flex; gap: 1rem; margin-top: 1.5rem; }
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
                <li><a href="account.php" class="nav-link active">Account</a></li>
                <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>Uitloggen mislukt</h1>
        </div>
        <div class="card">
            <?php if ($error_message): ?>
                <div class="error-msg">⚠️ <?= htmlspecialchars($error_message) ?></div>
            <?php endif; ?>
            <div class="btn-row">
                <a href="logout.php" class="btn-primary" style="text-decoration:none;">Opnieuw proberen</a>
                <a href="../index.html" class="link-secondary">Terug naar home</a>
            </div>
        </div>
    </div>
</body>
</html>