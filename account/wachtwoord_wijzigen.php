<?php
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php?melding=niet_ingelogd');
    exit;
}

require_once 'db.php';

$succes = '';
$fout = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $oud = $_POST['oud_wachtwoord'] ?? '';
    $nieuw = $_POST['nieuw_wachtwoord'] ?? '';
    $bevestig = $_POST['bevestig_wachtwoord'] ?? '';

    if (empty($oud) || empty($nieuw) || empty($bevestig)) {
        $fout = 'Vul alle velden in.';
    } elseif (strlen($nieuw) < 6) {
        $fout = 'Nieuw wachtwoord moet minimaal 6 tekens zijn.';
    } elseif ($nieuw !== $bevestig) {
        $fout = 'De nieuwe wachtwoorden komen niet overeen.';
    } else {
        $stmt = $pdo->prepare("SELECT wachtwoord FROM gebruikers WHERE id = ?");
        $stmt->execute([$_SESSION['gebruiker_id']]);
        $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!password_verify($oud, $gebruiker['wachtwoord'])) {
            $fout = 'Huidig wachtwoord is onjuist.';
        } else {
            $nieuwHash = password_hash($nieuw, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE gebruikers SET wachtwoord = ? WHERE id = ?");
            $stmt->execute([$nieuwHash, $_SESSION['gebruiker_id']]);
            $succes = 'Wachtwoord succesvol gewijzigd!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wachtwoord Wijzigen – AJFX</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <style>
        .page-wrapper { max-width: 600px; margin: 0 auto; padding: 2rem 1.5rem 4rem; margin-top: 70px; }
        .page-header { margin-bottom: 2rem; padding-bottom: 1.5rem; border-bottom: 1px solid var(--gray-200); }
        .page-header h1 { font-size: 2rem; color: var(--dark); margin-bottom: 0.25rem; }
        .page-header p { color: var(--gray-600); }
        .card { background: var(--white); border: 1px solid var(--gray-200); border-radius: var(--radius-lg); padding: 2rem; box-shadow: var(--shadow-sm); }
        .form-group { margin-bottom: 1.25rem; }
        .form-group label { display: block; font-weight: 600; font-size: 0.9rem; color: var(--dark); margin-bottom: 0.4rem; }
        .form-group input { width: 100%; padding: 0.75rem 1rem; border: 1px solid var(--gray-300); border-radius: var(--radius-md); font-family: var(--font-main); font-size: 1rem; color: var(--dark); transition: border-color 0.2s; }
        .form-group input:focus { outline: none; border-color: var(--primary); }
        .form-group small { display: block; color: var(--gray-400); font-size: 0.82rem; margin-top: 0.3rem; }
        .error-msg { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .success-msg { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; border-radius: var(--radius-md); padding: 0.75rem 1rem; margin-bottom: 1.25rem; font-size: 0.9rem; }
        .btn-row { display: flex; gap: 1rem; margin-top: 1.5rem; }
        .btn-primary { width: auto; padding: 0.75rem 1.5rem; }
        .btn-back { background: transparent; color: var(--gray-600); border: 2px solid var(--gray-300); padding: 0.75rem 1.5rem; border-radius: var(--radius-md); font-family: var(--font-main); font-size: 1rem; font-weight: 600; cursor: pointer; text-decoration: none; transition: all 0.2s; }
        .btn-back:hover { border-color: var(--primary); color: var(--primary); }
    </style>
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo"><span class="logo-text">AJFX</span></div>
            <ul class="nav-menu">
                <li><a href="../homepage/index.html" class="nav-link">Home</a></li>
                <li><a href="account.php" class="nav-link">Account</a></li>
                <li><a href="logout.php" class="nav-link">Uitloggen</a></li>
            </ul>
        </div>
    </nav>

    <div class="page-wrapper">
        <div class="page-header">
            <h1>🔒 Wachtwoord Wijzigen</h1>
            <p>Kies een nieuw wachtwoord</p>
        </div>
        <div class="card">
            <?php if ($succes): ?><div class="success-msg">✅ <?= htmlspecialchars($succes) ?></div><?php endif; ?>
            <?php if ($fout): ?><div class="error-msg">⚠️ <?= htmlspecialchars($fout) ?></div><?php endif; ?>
            <form method="POST">
                <div class="form-group">
                    <label for="oud_wachtwoord">Huidig Wachtwoord</label>
                    <input type="password" id="oud_wachtwoord" name="oud_wachtwoord" placeholder="••••••••" required>
                </div>
                <div class="form-group">
                    <label for="nieuw_wachtwoord">Nieuw Wachtwoord</label>
                    <input type="password" id="nieuw_wachtwoord" name="nieuw_wachtwoord" placeholder="••••••••" required>
                    <small>Minimaal 6 tekens</small>
                </div>
                <div class="form-group">
                    <label for="bevestig_wachtwoord">Bevestig Nieuw Wachtwoord</label>
                    <input type="password" id="bevestig_wachtwoord" name="bevestig_wachtwoord" placeholder="••••••••" required>
                </div>
                <div class="btn-row">
                    <button type="submit" class="btn-primary">Opslaan</button>
                    <a href="account.php" class="btn-back">← Terug</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
