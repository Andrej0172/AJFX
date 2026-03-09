<?php
session_start();

if (isset($_SESSION['gebruiker_id'])) {
    header('Location: account.php');
    exit;
}

require_once 'db.php';

$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $wachtwoord = $_POST['wachtwoord'] ?? '';

    if (empty($email) || empty($wachtwoord)) {
        $fout = 'Vul alle velden in.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE email = ?");
        $stmt->execute([$email]);
        $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($gebruiker && password_verify($wachtwoord, $gebruiker['wachtwoord'])) {
            $_SESSION['gebruiker_id'] = $gebruiker['id'];
            $_SESSION['gebruiker_naam'] = $gebruiker['naam'];
            header('Location: account.php');
            exit;
        } else {
            $fout = 'Ongeldig e-mailadres of wachtwoord.';
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
        .login-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(to bottom, var(--gray-50), var(--white));
            padding: 2rem;
            margin-top: 70px;
        }
        .login-card {
            background: var(--white);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            padding: 2.5rem;
            width: 100%;
            max-width: 420px;
            box-shadow: var(--shadow-lg);
        }
        .login-card h1 { font-size: 1.75rem; margin-bottom: 0.5rem; color: var(--dark); }
        .login-card p.subtext { color: var(--gray-600); margin-bottom: 2rem; font-size: 0.95rem; }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; font-size: 0.9rem; color: var(--dark); margin-bottom: 0.4rem; }
        .form-group input {
            width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300);
            border-radius: var(--radius-md); font-family: var(--font-main);
            font-size: 1rem; color: var(--dark); transition: border-color 0.2s;
        }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        .error-msg {
            background: #fef2f2; border: 1px solid #fecaca; color: #dc2626;
            border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem;
        }
        .login-card .btn-primary { width: 100%; padding: 0.875rem; font-size: 1rem; }
        .back-link { display: block; text-align: center; margin-top: 1.25rem; color: var(--gray-600); text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { color: var(--primary); }
        .demo-hint {
            background: var(--gray-50); border: 1px solid var(--gray-200);
            border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.85rem; color: var(--gray-600);
        }
        .demo-hint strong { color: var(--dark); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><span class="logo-text">AJFX</span></div>
            <ul class="nav-menu">
                <li><a href="../homepage/index.html" class="nav-link">Home</a></li>
                <li><a href="login.php" class="nav-link active">Account</a></li>
            </ul>
        </div>
    </nav>

    <div class="login-wrapper">
        <div class="login-card">
            <h1>Welkom terug</h1>
            <p class="subtext">Log in op uw AJFX account</p>

            <?php if (isset($_GET['melding']) && $_GET['melding'] === 'niet_ingelogd'): ?>
                <div class="error-msg">🔒 Je moet ingelogd zijn om je accountoverzicht te bekijken.</div>
            <?php endif; ?>

            <div class="demo-hint">
                <strong>Testaccount:</strong> jan@example.com / test123
            </div>

            <?php if ($fout): ?>
                <div class="error-msg">⚠️ <?= htmlspecialchars($fout) ?></div>
            <?php endif; ?>

            <form method="POST">
                <div class="form-group">
                    <label for="email">E-mailadres</label>
                    <input type="email" id="email" name="email" placeholder="jouw@email.nl"
                           value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                </div>
                <div class="form-group">
                    <label for="wachtwoord">Wachtwoord</label>
                    <input type="password" id="wachtwoord" name="wachtwoord" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-primary">Inloggen</button>
            </form>

            <a href="../homepage/index.html" class="back-link">← Terug naar home</a>
        </div>
    </div>
</body>
</html>
