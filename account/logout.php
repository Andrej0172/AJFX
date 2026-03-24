<?php
// logout.php - Uitloggen (happy + unhappy scenario's)

session_start();
require_once 'db.php';

// Niet ingelogd? Stuur terug naar loginpagina
if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}

$error_message = '';

try {
    // --- HAPPY: sessie correct beëindigen ---

    // 1. Leeg de sessie-array
    $_SESSION = [];

    // 2. Verwijder het sessie-cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    // 3. Vernietig de sessie op de server
    if (!session_destroy()) {
        // session_destroy() mislukte — interne fout
        throw new Exception('Sessie kon niet worden verwijderd.');
    }

    // --- HAPPY: doorsturen naar inlogpagina ---
    header('Location: login.php');
    exit;

} catch (Exception $e) {
    // --- UNHAPPY: uitloggen mislukt door interne fout ---
    // Toon foutmelding; gebruiker blijft ingelogd
    $error_message = 'Uitloggen is niet volledig gelukt door een technische storing. Probeer het opnieuw.';
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Uitloggen – AJFX</title>
</head>
<body>
    <h1>Uitloggen mislukt</h1>

    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <p>U bent nog steeds ingelogd. <a href="logout.php">Probeer opnieuw uit te loggen</a></p>
    <p>Of ga terug naar het <a href="../index.html">dashboard</a>.</p>
</body>
</html>