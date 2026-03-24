<?php
// account.php - Account aanmaken (happy + unhappy scenario's)

session_start();
require_once 'db.php';

$success_message = '';
$error_message   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Haal velden op uit het formulier
    $naam      = trim($_POST['naam']      ?? '');
    $email     = trim($_POST['email']     ?? '');
    $wachtwoord = trim($_POST['wachtwoord'] ?? '');

    // --- Validatie: verplichte velden ---
    if (empty($naam) || empty($email) || empty($wachtwoord)) {
        $error_message = 'Vul alle verplichte velden in (naam, e-mail, wachtwoord).';

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Voer een geldig e-mailadres in.';

    } elseif (strlen($wachtwoord) < 6) {
        $error_message = 'Het wachtwoord moet minimaal 6 tekens bevatten.';

    } else {

        // --- UNHAPPY: database niet bereikbaar ---
        if ($pdo === null) {
            $error_message = 'Uw account kon niet worden aangemaakt door een technische storing. Probeer het later opnieuw.';

        } else {
            try {
                // Controleer of e-mail al bestaat
                $check = $pdo->prepare('SELECT id FROM gebruikers WHERE email = ?');
                $check->execute([$email]);

                if ($check->fetch()) {
                    $error_message = 'Dit e-mailadres is al in gebruik. Probeer in te loggen.';

                } else {
                    // --- HAPPY: account opslaan ---
                    $hash = password_hash($wachtwoord, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare(
                        'INSERT INTO gebruikers (naam, email, wachtwoord) VALUES (?, ?, ?)'
                    );
                    $stmt->execute([$naam, $email, $hash]);

                    $success_message = 'Uw account is succesvol aangemaakt! U kunt nu inloggen.';
                }

            } catch (PDOException $e) {
                // --- UNHAPPY: opslaan mislukt door interne databasefout ---
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
    <title>Account aanmaken – AJFX</title>
</head>
<body>
    <h1>Account aanmaken</h1>

    <?php if ($success_message): ?>
        <p style="color: green;"><?= htmlspecialchars($success_message) ?></p>
        <a href="login.php">Naar inlogpagina</a>
    <?php endif; ?>

    <?php if ($error_message): ?>
        <p style="color: red;"><?= htmlspecialchars($error_message) ?></p>
    <?php endif; ?>

    <?php if (!$success_message): ?>
    <form method="POST" action="account.php">
        <label>Naam:<br>
            <input type="text" name="naam" required>
        </label><br><br>

        <label>E-mail:<br>
            <input type="email" name="email" required>
        </label><br><br>

        <label>Wachtwoord:<br>
            <input type="password" name="wachtwoord" required minlength="6">
        </label><br><br>

        <button type="submit">Account aanmaken</button>
    </form>
    <?php endif; ?>
</body>
</html>