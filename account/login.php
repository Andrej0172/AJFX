<?php
// login.php - Inloggen (happy + unhappy scenario's)

session_start();
require_once 'db.php';

// Al ingelogd? Stuur door naar dashboard
if (isset($_SESSION['gebruiker_id'])) {
    header('Location: ../index.html');
    exit;
}

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email      = trim($_POST['email']      ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    // --- Validatie: velden niet leeg ---
    if (empty($email) || empty($wachtwoord)) {
        $error_message = 'Vul uw e-mailadres en wachtwoord in.';

    } else {

        // --- UNHAPPY: database/authenticatieservice niet bereikbaar ---
        if ($pdo === null) {
            $error_message = 'Inloggen is tijdelijk niet mogelijk door een technische storing. Probeer het later opnieuw.';

        } else {
            try {
                // Zoek gebruiker op via e-mail
                $stmt = $pdo->prepare('SELECT id, naam, wachtwoord FROM gebruikers WHERE email = ?');
                $stmt->execute([$email]);
                $gebruiker = $stmt->fetch();

                if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
                    // --- HAPPY: inloggen gelukt ---
                    session_regenerate_id(true); // Voorkom session fixation
                    $_SESSION['gebruiker_id']   = $gebruiker['id'];
                    $_SESSION['gebruiker_naam'] = $gebruiker['naam'];

                    header('Location: ../index.html');
                    exit;

                } else {
                    // --- UNHAPPY: verkeerde gegevens ---
                    $error_message = 'Ongeldige e-mail of wachtwoord. Controleer uw gegevens en probeer opnieuw.';
                }

            } catch (PDOException $e) {
                // --- UNHAPPY: time-out of serverfout tijdens validatie ---
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
    <title>Inloggen – AJFX</title>
</head>
<body>
    <h1>Inloggen</h1>

    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <form method="POST" action="login.php">
        <label>E-mail:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Wachtwoord:<br>
            <input type="password" name="wachtwoord" required>
        </label><br><br>

        <button type="submit">Inloggen</button>
    </form>

    <p>Nog geen account? <a href="account.php">Maak er een aan</a></p>
</body>
</html>