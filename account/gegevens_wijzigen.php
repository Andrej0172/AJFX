<?php
session_start();

if (!isset($_SESSION['gebruiker_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'db.php';

$succes = '';
$fout   = '';

$stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE id = ?");
$stmt->execute([$_SESSION['gebruiker_id']]);
$gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $naam  = trim($_POST['naam']  ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($naam) || empty($email)) {
        $fout = 'Vul alle velden in.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $fout = 'Vul een geldig e-mailadres in.';
    } else {
        $check = $pdo->prepare("SELECT id FROM gebruikers WHERE email = ? AND id != ?");
        $check->execute([$email, $_SESSION['gebruiker_id']]);
        if ($check->fetch()) {
            $fout = 'Dit e-mailadres is al in gebruik.';
        } else {
            $stmt = $pdo->prepare("UPDATE gebruikers SET naam = ?, email = ? WHERE id = ?");
            $stmt->execute([$naam, $email, $_SESSION['gebruiker_id']]);
            $_SESSION['gebruiker_naam'] = $naam;
            $succes = 'Gegevens opgeslagen!';

            $stmt = $pdo->prepare("SELECT * FROM gebruikers WHERE id = ?");
            $stmt->execute([$_SESSION['gebruiker_id']]);
            $gebruiker = $stmt->fetch(PDO::FETCH_ASSOC);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gegevens wijzigen – AJFX</title>
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
        .fout   { background: #fef2f2; border: 1px solid #fecaca; color: #dc2626; border-radius: var(--radius-md); padding: .75rem 1rem; margin-bottom: 1.25rem; font-size: .9rem; }
        .succes { background: #f0fdf4; border: 1px solid #bbf7d0; color: #16a34a; border-radius: var(--radius-md); padding: .75rem 1rem; margin-bottom: 1.25rem; font-size: .9rem; }
        .btn-row { display: flex; align-items: center; gap: 1rem; margin-top: 1.5rem; }
        .btn-terug { background: transparent; color: var(--gray-600); border: 2px solid var(--gray-300); padding: 0.72rem 1.5rem; border-radius: var(--radius-md); font-family: var(--font-main); font-size: 1rem; font-weight: 600; text-decoration: none; transition: all .2s; }
        .btn-terug:hover { border-color: var(--primary); color: var(--primary); }
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
            <li><a href="profiel.php" class="nav-link active">Account</a></li>
            <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerkers</a></li>
        </ul>
    </div>
</nav>

<div class="page-wrapper">
    <div class="page-header">
        <h1>✏️ Gegevens wijzigen</h1>
        <p>Pas uw naam en e-mailadres aan</p>
    </div>
    <div class="card">
        <?php if ($succes): ?>
            <div class="succes">✅ <?= htmlspecialchars($succes) ?></div>
        <?php endif; ?>
        <?php if ($fout): ?>
            <div class="fout">⚠️ <?= htmlspecialchars($fout) ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="naam">Naam</label>
                <input type="text" id="naam" name="naam" required
                       value="<?= htmlspecialchars($gebruiker['naam']) ?>">
            </div>
            <div class="form-group">
                <label for="email">E-mailadres</label>
                <input type="email" id="email" name="email" required
                       value="<?= htmlspecialchars($gebruiker['email']) ?>">
            </div>
            <div class="btn-row">
                <button type="submit" class="btn-primary">Opslaan</button>
                <a href="profiel.php" class="btn-terug">← Terug</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>