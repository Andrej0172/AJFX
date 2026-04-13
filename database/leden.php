<?php

// URL van de website (wordt gebruikt voor links binnen het project)
define('URLROOT', 'http://localhost/projectp3/AJFX');

// Database gegevens
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "lessen";
?>

<link rel="stylesheet" href="../homepage/styles.css">
<link rel="stylesheet" href="../database/lidcss.css">
    <nav class="navbar">
        <div class="nav-container">
            <div class="logo">
                <span class="logo-text">AJFX</span>
            </div>
            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
            <ul class="nav-menu" id="navMenu">
                <li><a href="../homepage/index.html" class="nav-link active">Home</a></li>
                <li><a href="../lessen-overzicht.php" class="nav-link">Lessen</a></li>
                <li><a href="../reservering_overzicht/reserveringsoverzicht.php" class="nav-link">Reserveringen</a></li>
                <li><a href="../account/login.php" class="nav-link">Account</a></li>
                <li><a href="../medewerker_overzicht/medewerkers.php" class="nav-link">Medewerker overzicht</a></li>
            </ul>
        </div>
    </nav>

<?php
// Maak verbinding met de database
$conn = new mysqli($servername, $username, $password, $dbname);

// Controleer of de databaseverbinding is gelukt
if ($conn->connect_error) {
    die("Connectie mislukt: " . $conn->connect_error);
}

// Variabelen voor meldingen
$melding = "";
$melding_type = "";

// ── Nieuw lid toevoegen ──────────────────────────────────────────────────────

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'toevoegen') {

    $leden    = $conn->real_escape_string(trim($_POST['leden']));
    $lessen   = $conn->real_escape_string(trim($_POST['lessen']));
    $leeftijd = (int) $_POST['leeftijd'];
    $email    = $conn->real_escape_string(trim($_POST['email']));

    if ($leden && $lessen && $leeftijd && $email) {

        $sql_insert = "INSERT INTO ledenoverzicht (leden, lessen, Leeftijd, email)
                       VALUES ('$leden', '$lessen', $leeftijd, '$email')";

        if ($conn->query($sql_insert)) {
            $melding      = "Lid '$leden' succesvol toegevoegd!";
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

// Scenario: Gegevens van een bestaand lid succesvol wijzigen
// Scenario: Wijziging mislukt door ongeldige invoer

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'wijzigen') {

    $lidnummer = (int) $_POST['lidnummer'];
    $leden     = trim($_POST['leden']);
    $lessen    = trim($_POST['lessen']);
    $leeftijd  = trim($_POST['leeftijd']);
    $email     = trim($_POST['email']);

    // Validatie: controleer of alle verplichte velden zijn ingevuld en correct zijn
    $geldig = true;

    if (empty($leden) || empty($lessen) || empty($leeftijd) || empty($email)) {
        $geldig = false;
    }

    // Controleer of leeftijd een geldig getal is tussen 1 en 120
    if (!is_numeric($leeftijd) || (int)$leeftijd < 1 || (int)$leeftijd > 120) {
        $geldig = false;
    }

    // Controleer of e-mailadres geldig is
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $geldig = false;
    }

    if (!$geldig) {
        // Scenario: Wijziging mislukt door ongeldige invoer
        $melding      = "Vul alle verplichte velden correct in.";
        $melding_type = "danger";
    } else {
        // Alle velden zijn geldig — sla de wijzigingen op
        $leden    = $conn->real_escape_string($leden);
        $lessen   = $conn->real_escape_string($lessen);
        $leeftijd = (int) $leeftijd;
        $email    = $conn->real_escape_string($email);

        $sql_update = "UPDATE ledenoverzicht
                       SET leden = '$leden',
                           lessen = '$lessen',
                           Leeftijd = $leeftijd,
                           email = '$email'
                       WHERE lidnummer = $lidnummer";

        if ($conn->query($sql_update)) {
            // Scenario: Gegevens van een bestaand lid succesvol wijzigen
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

// ── Leden ophalen (met of zonder zoekopdracht) ───────────────────────────────

$zoek = "";

if (isset($_GET['zoek'])) {
    $zoek = $conn->real_escape_string($_GET['zoek']);
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email
            FROM ledenoverzicht
            WHERE leden LIKE '%$zoek%'";
} else {
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email FROM ledenoverzicht";
}

$result = $conn->query($sql);
$data = [];

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
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Leden Overzicht</h1>
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nieuwLidModal">
            ➕ Nieuw lid toevoegen
        </button>
    </div>

    <!-- Melding na toevoegen of wijzigen -->
    <?php if ($melding) : ?>
        <div class="alert alert-<?= $melding_type ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($melding) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ── Zoekformulier ──────────────────────────────────────────────────── -->
    <form method="GET" class="mb-4">
        <div class="input-group">
            <input type="text"
                   name="zoek"
                   class="form-control"
                   placeholder="Zoek op achternaam..."
                   value="<?= htmlspecialchars($zoek) ?>">
            <button class="btn btn-primary" type="submit">Zoeken</button>
            <?php if ($zoek) : ?>
                <a href="index.php" class="btn btn-outline-secondary">✕ Wis filter</a>
            <?php endif; ?>
        </div>
    </form>

    <!-- ── Ledentabel ─────────────────────────────────────────────────────── -->
    <table class="table table-striped table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Lid</th>
                <th>Lidnummer</th>
                <th>Les</th>
                <th>Leeftijd</th>
                <th>Email</th>
                <th>Acties</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($data)) : ?>
                <?php foreach ($data as $ledenOverzicht) : ?>
                    <tr>
                        <td><?= htmlspecialchars($ledenOverzicht->leden) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->lidnummer) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->lessen) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->Leeftijd) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->email) ?></td>
                        <td>
                            <!-- Knop om de bewerkmodal te openen voor dit lid -->
                            <!-- De data-attributen vullen het formulier automatisch in via JavaScript -->
                            <button type="button"
                                    class="btn btn-warning btn-sm"
                                    data-bs-toggle="modal"
                                    data-bs-target="#wijzigLidModal"
                                    data-lidnummer="<?= htmlspecialchars($ledenOverzicht->lidnummer) ?>"
                                    data-leden="<?= htmlspecialchars($ledenOverzicht->leden) ?>"
                                    data-lessen="<?= htmlspecialchars($ledenOverzicht->lessen) ?>"
                                    data-leeftijd="<?= htmlspecialchars($ledenOverzicht->Leeftijd) ?>"
                                    data-email="<?= htmlspecialchars($ledenOverzicht->email) ?>">
                                ✏️ Wijzigen
                            </button>
                            <!-- Knop om de verwijderbevestiging te openen -->
                            <button type="button"
                                    class="btn btn-danger btn-sm ms-1"
                                    data-bs-toggle="modal"
                                    data-bs-target="#verwijderLidModal"
                                    data-lidnummer="<?= htmlspecialchars($ledenOverzicht->lidnummer) ?>"
                                    data-leden="<?= htmlspecialchars($ledenOverzicht->leden) ?>">
                                 Verwijderen
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="6" class="text-center text-danger">
                        Geen leden gevonden<?= $zoek ? " met deze achternaam" : "" ?>.
                    </td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

</div>

<!-- ── Modal: Nieuw lid toevoegen ─────────────────────────────────────────── -->
<div class="modal fade" id="nieuwLidModal" tabindex="-1" aria-labelledby="nieuwLidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="nieuwLidModalLabel"> Nieuw lid toevoegen</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="actie" value="toevoegen">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Naam</label>
                        <input type="text" name="leden" class="form-control" placeholder="Voor- en achternaam" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Les</label>
                        <input type="text" name="lessen" class="form-control" placeholder="Bijv. Yoga, Zwemmen..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leeftijd</label>
                        <input type="number" name="leeftijd" class="form-control" placeholder="Leeftijd" min="1" max="120" required>
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

<!-- ── Modal: Bestaand lid wijzigen ───────────────────────────────────────── -->
<div class="modal fade" id="wijzigLidModal" tabindex="-1" aria-labelledby="wijzigLidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title" id="wijzigLidModalLabel"> Lid wijzigen</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" id="wijzigForm" novalidate>
                <!-- Verborgen velden voor actie en lidnummer -->
                <input type="hidden" name="actie" value="wijzigen">
                <input type="hidden" name="lidnummer" id="wijzig_lidnummer">

                <div class="modal-body">

                    <!-- Client-side foutmelding bij ongeldige invoer (zichtbaar vóór verzenden) -->
                    <div id="wijzig_foutmelding" class="alert alert-danger d-none" role="alert">
                        Vul alle verplichte velden correct in.
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Naam <span class="text-danger">*</span></label>
                        <input type="text" name="leden" id="wijzig_leden" class="form-control" placeholder="Voor- en achternaam" required>
                        <div class="invalid-feedback">Naam is verplicht.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Les <span class="text-danger">*</span></label>
                        <input type="text" name="lessen" id="wijzig_lessen" class="form-control" placeholder="Bijv. Yoga, Zwemmen..." required>
                        <div class="invalid-feedback">Les is verplicht.</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Leeftijd <span class="text-danger">*</span></label>
                        <input type="number" name="leeftijd" id="wijzig_leeftijd" class="form-control" placeholder="Leeftijd" min="1" max="120" required>
                        <div class="invalid-feedback">Voer een geldige leeftijd in (1–120).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">E-mailadres <span class="text-danger">*</span></label>
                        <input type="email" name="email" id="wijzig_email" class="form-control" placeholder="naam@voorbeeld.nl" required>
                        <div class="invalid-feedback">Voer een geldig e-mailadres in.</div>
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

<!-- ── Modal: Lid verwijderen (bevestiging) ───────────────────────────────── -->
<div class="modal fade" id="verwijderLidModal" tabindex="-1" aria-labelledby="verwijderLidModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="verwijderLidModalLabel">🗑️ Lid verwijderen</h5>
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

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ── Verwijdermodal vullen met naam van het lid ────────────────────────────
const verwijderModal = document.getElementById('verwijderLidModal');

verwijderModal.addEventListener('show.bs.modal', function (event) {
    const knop = event.relatedTarget;
    const lidnummer = knop.getAttribute('data-lidnummer');
    const leden     = knop.getAttribute('data-leden');

    document.getElementById('verwijder_lidnummer').value = lidnummer;
    document.getElementById('verwijder_naam').textContent = leden;
});

// ── Bewerkmodal vullen met bestaande gegevens ─────────────────────────────
// Wanneer de modal geopend wordt, worden de data-attributen van de knop
// uitgelezen en in de formuliervelden geplaatst.

const wijzigModal = document.getElementById('wijzigLidModal');

wijzigModal.addEventListener('show.bs.modal', function (event) {
    // De knop die de modal heeft geopend
    const knop = event.relatedTarget;

    // Haal de gegevens op uit de data-attributen van de knop
    const lidnummer = knop.getAttribute('data-lidnummer');
    const leden     = knop.getAttribute('data-leden');
    const lessen    = knop.getAttribute('data-lessen');
    const leeftijd  = knop.getAttribute('data-leeftijd');
    const email     = knop.getAttribute('data-email');

    // Vul de formuliervelden in met de opgehaalde gegevens
    document.getElementById('wijzig_lidnummer').value = lidnummer;
    document.getElementById('wijzig_leden').value     = leden;
    document.getElementById('wijzig_lessen').value    = lessen;
    document.getElementById('wijzig_leeftijd').value  = leeftijd;
    document.getElementById('wijzig_email').value     = email;

    // Verberg eventuele oude foutmeldingen en reset validatiestatus
    document.getElementById('wijzig_foutmelding').classList.add('d-none');
    document.getElementById('wijzigForm').classList.remove('was-validated');
});

// ── Client-side validatie vóór het verzenden ──────────────────────────────
// Scenario: Wijziging mislukt door ongeldige invoer
// Als een verplicht veld leeg is of ongeldig, wordt het formulier NIET verstuurd
// en verschijnt er een foutmelding bovenaan het formulier.

document.getElementById('wijzigForm').addEventListener('submit', function (event) {
    const form = this;
    const foutmelding = document.getElementById('wijzig_foutmelding');

    // Controleer geldigheid via de ingebouwde browser-validatie
    if (!form.checkValidity()) {
        // Stop het verzenden van het formulier
        event.preventDefault();
        event.stopPropagation();

        // Toon de foutmelding bovenaan het formulier
        foutmelding.classList.remove('d-none');

        // Activeer Bootstrap's visuele validatiestijlen (rode randen etc.)
        form.classList.add('was-validated');
    } else {
        // Formulier is geldig — verberg eventuele oude foutmelding
        foutmelding.classList.add('d-none');
    }
});
</script>

</body>
</html>