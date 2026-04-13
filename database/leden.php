<?php

define('URLROOT', 'http://localhost/projectp3/AJFX');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "lessen";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

$melding      = "";
$melding_type = "";

// ── Nieuw lid toevoegen ──────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'toevoegen') {

    $naam     = $conn->real_escape_string(trim($_POST['naam']));
    $lessen   = $conn->real_escape_string(trim($_POST['lessen']));
    $leeftijd = (int) $_POST['leeftijd'];
    $email    = $conn->real_escape_string(trim($_POST['email']));

    if ($naam && $lessen && $leeftijd && $email) {

        $res = $conn->query("SELECT MAX(lidnummer) AS max_nr FROM ledenoverzicht");
        $row = $res->fetch_assoc();
        $nieuw_lidnummer = ($row['max_nr'] ?? 0) + 1;

        $sql_insert = "INSERT INTO ledenoverzicht (naam, lidnummer, lessen, leeftijd, email)
                       VALUES ('$naam', $nieuw_lidnummer, '$lessen', $leeftijd, '$email')";

        if ($conn->query($sql_insert)) {
            $melding      = "Lid '$naam' succesvol toegevoegd!";
            $melding_type = "success";
        } else {
            $melding      = "Databasefout: " . $conn->error;
            $melding_type = "danger";
        }

    } else {
        $melding      = "Vul alle velden in.";
        $melding_type = "warning";
    }
}

// ── Bestaand lid wijzigen ────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'wijzigen') {

    $lidnummer = (int) $_POST['lidnummer'];
    $naam      = trim($_POST['naam']);
    $lessen    = trim($_POST['lessen']);
    $leeftijd  = trim($_POST['leeftijd']);
    $email     = trim($_POST['email']);

    $geldig = true;

    if (empty($naam) || empty($lessen) || empty($leeftijd) || empty($email)) {
        $geldig = false;
    }

    if (!is_numeric($leeftijd) || (int)$leeftijd < 1 || (int)$leeftijd > 120) {
        $geldig = false;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $geldig = false;
    }

    if (!$geldig) {
        $melding      = "Vul alle verplichte velden correct in.";
        $melding_type = "danger";
    } else {
        $naam     = $conn->real_escape_string($naam);
        $lessen   = $conn->real_escape_string($lessen);
        $leeftijd = (int) $leeftijd;
        $email    = $conn->real_escape_string($email);

        $sql_update = "UPDATE ledenoverzicht
                       SET naam     = '$naam',
                           lessen   = '$lessen',
                           leeftijd = $leeftijd,
                           email    = '$email'
                       WHERE lidnummer = $lidnummer";

        if ($conn->query($sql_update)) {
            $melding      = "Gegevens succesvol bijgewerkt.";
            $melding_type = "success";
        } else {
            $melding      = "Databasefout bij wijzigen: " . $conn->error;
            $melding_type = "danger";
        }
    }
}

// ── Bestaand lid verwijderen ─────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'verwijderen') {

    $lidnummer = (int) $_POST['lidnummer'];

    if ($lidnummer > 0) {
        $sql_delete = "DELETE FROM ledenoverzicht WHERE lidnummer = $lidnummer";

        if ($conn->query($sql_delete)) {
            $melding      = "Lid succesvol verwijderd.";
            $melding_type = "success";
        } else {
            $melding      = "Databasefout bij verwijderen: " . $conn->error;
            $melding_type = "danger";
        }
    } else {
        $melding      = "Ongeldig lidnummer.";
        $melding_type = "danger";
    }
}

// ── Leden ophalen ────────────────────────────────────────────────────────────

$zoek = "";

if (isset($_GET['zoek'])) {
    $zoek = $conn->real_escape_string($_GET['zoek']);
    $sql  = "SELECT naam, lidnummer, lessen, leeftijd, email
             FROM ledenoverzicht
             WHERE naam LIKE '%$zoek%'";
} else {
    $sql = "SELECT naam, lidnummer, lessen, leeftijd, email FROM ledenoverzicht";
}

$result = $conn->query($sql);
$data   = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        $data[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Leden Overzicht</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../homepage/styles.css">
    <link rel="stylesheet" href="../database/lidcss.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-container">
        <div class="logo">
            <span class="logo-text">AJFX</span>
        </div>
        <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
            <span></span><span></span><span></span>
        </button>
        <ul class="nav-menu" id="navMenu">
            <li><a href="../index.html" class="nav-link active">Home</a></li>
            <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
            <li><a href="../reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
            <li><a href="../account/login.php" class="nav-link">Account</a></li>
            <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
        </ul>
    </div>
</nav>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Leden Overzicht</h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nieuwLidModal">
            ➕ Nieuw lid toevoegen
        </button>
    </div>

    <?php if ($melding) : ?>
        <div class="alert alert-<?= $melding_type ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($melding) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Zoekformulier -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text" name="zoek" class="form-control"
                   placeholder="Zoek op naam..."
                   value="<?= htmlspecialchars($zoek) ?>">
            <button class="btn btn-primary" type="submit">Zoeken</button>
            <?php if ($zoek) : ?>
                <a href="index.php" class="btn btn-outline-secondary">✕ Wis filter</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- Ledentabel -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Naam</th>
                <th>Lidnummer</th>
                <th>Les</th>
                <th>Leeftijd</th>
                <th>Email</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)) : ?>
                <?php foreach ($data as $lid) : ?>
                    <tr>
                        <td><?= htmlspecialchars($lid->naam) ?></td>
                        <td><?= htmlspecialchars($lid->lidnummer) ?></td>
                        <td><?= htmlspecialchars($lid->lessen) ?></td>
                        <td><?= htmlspecialchars($lid->leeftijd) ?></td>
                        <td><?= htmlspecialchars($lid->email) ?></td>
                        <td>
                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#wijzigLidModal"
                                    data-lidnummer="<?= htmlspecialchars($lid->lidnummer) ?>"
                                    data-naam="<?= htmlspecialchars($lid->naam) ?>"
                                    data-lessen="<?= htmlspecialchars($lid->lessen) ?>"
                                    data-leeftijd="<?= htmlspecialchars($lid->leeftijd) ?>"
                                    data-email="<?= htmlspecialchars($lid->email) ?>">
                                ✏️ Wijzigen
                            </button>
                            <button type="button"
                                    class="btn btn-danger btn-sm ms-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#verwijderLidModal"
                                    data-lidnummer="<?= htmlspecialchars($lid->lidnummer) ?>"
                                    data-naam="<?= htmlspecialchars($lid->naam) ?>">
                                🗑️ Verwijderen
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Geen leden gevonden<?= $zoek ? " met deze naam" : "" ?>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!-- Modal: Nieuw lid toevoegen -->
<div class="modal fade" id="nieuwLidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">➕ Nieuw lid toevoegen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="actie" value="toevoegen">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Naam</label>
                        <input type="text" name="naam" class="form-control" placeholder="Voor- en achternaam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Les</label>
                        <input type="text" name="lessen" class="form-control" placeholder="Bijv. Yoga, Zwemmen..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leeftijd</label>
                        <input type="number" name="leeftijd" class="form-control" min="1" max="120" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mailadres</label>
                        <input type="email" name="email" class="form-control" placeholder="naam@voorbeeld.nl" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-success">Lid opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Lid wijzigen -->
<div class="modal fade" id="wijzigLidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title">✏️ Lid wijzigen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="wijzigForm" novalidate>
                <input type="hidden" name="actie" value="wijzigen">
                <input type="hidden" name="lidnummer" id="wijzig_lidnummer">
                <div class="modal-body">
                    <div id="wijzig_foutmelding" class="alert alert-danger d-none" role="alert">
                        Vul alle verplichte velden correct in.
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Naam</label>
                        <input type="text" name="naam" id="wijzig_naam" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Les</label>
                        <input type="text" name="lessen" id="wijzig_lessen" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leeftijd</label>
                        <input type="number" name="leeftijd" id="wijzig_leeftijd" class="form-control" min="1" max="120" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mailadres</label>
                        <input type="email" name="email" id="wijzig_email" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-warning">Opslaan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Lid verwijderen -->
<div class="modal fade" id="verwijderLidModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">🗑️ Lid verwijderen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <p>Weet je zeker dat je <strong id="verwijder_naam"></strong> wilt verwijderen?</p>
                <p class="text-muted small">Deze actie kan niet ongedaan worden gemaakt.</p>
            </div>
            <form method="POST">
                <input type="hidden" name="actie" value="verwijderen">
                <input type="hidden" name="lidnummer" id="verwijder_lidnummer">
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Annuleren</button>
                    <button type="submit" class="btn btn-danger">Ja, verwijderen</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
const verwijderModal = document.getElementById('verwijderLidModal');
verwijderModal.addEventListener('show.bs.modal', function (event) {
    const knop = event.relatedTarget;
    document.getElementById('verwijder_lidnummer').value   = knop.getAttribute('data-lidnummer');
    document.getElementById('verwijder_naam').textContent  = knop.getAttribute('data-naam');
});

const wijzigModal = document.getElementById('wijzigLidModal');
wijzigModal.addEventListener('show.bs.modal', function (event) {
    const knop = event.relatedTarget;
    document.getElementById('wijzig_lidnummer').value  = knop.getAttribute('data-lidnummer');
    document.getElementById('wijzig_naam').value       = knop.getAttribute('data-naam');
    document.getElementById('wijzig_lessen').value     = knop.getAttribute('data-lessen');
    document.getElementById('wijzig_leeftijd').value   = knop.getAttribute('data-leeftijd');
    document.getElementById('wijzig_email').value      = knop.getAttribute('data-email');
    document.getElementById('wijzig_foutmelding').classList.add('d-none');
    document.getElementById('wijzigForm').classList.remove('was-validated');
});

document.getElementById('wijzigForm').addEventListener('submit', function (event) {
    const form = this;
    const foutmelding = document.getElementById('wijzig_foutmelding');
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        foutmelding.classList.remove('d-none');
        form.classList.add('was-validated');
    } else {
        foutmelding.classList.add('d-none');
    }
});
</script>

</body>
</html>