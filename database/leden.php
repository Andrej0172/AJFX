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

// Controleer of het formulier verstuurd is via POST en of de actie 'toevoegen' is
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['actie']) && $_POST['actie'] === 'toevoegen') {

    // Haal de ingevulde waarden op en beveilig ze tegen SQL-injectie
    $leden    = $conn->real_escape_string(trim($_POST['leden']));
    $lessen   = $conn->real_escape_string(trim($_POST['lessen']));
    $leeftijd = (int) $_POST['leeftijd']; // Cast naar integer voor veiligheid
    $email    = $conn->real_escape_string(trim($_POST['email']));

    // Controleer of alle velden zijn ingevuld
    if ($leden && $lessen && $leeftijd && $email) {

        // lidnummer wordt NIET meegegeven — dit is AUTO_INCREMENT in de database
        $sql_insert = "INSERT INTO ledenoverzicht (leden, lessen, Leeftijd, email)
                       VALUES ('$leden', '$lessen', $leeftijd, '$email')";

        // Voer de query uit en controleer of het gelukt is
        if ($conn->query($sql_insert)) {
            $melding      = "Lid '$leden' succesvol toegevoegd!";
            $melding_type = "success";
        } else {
            $melding      = "Databasefout: " . $conn->error;
            $melding_type = "danger";
        }

    } else {
        // Niet alle velden zijn ingevuld
        $melding      = "Vul alle velden in.";
        $melding_type = "warning";
    }
}

// ── Leden ophalen (met of zonder zoekopdracht) ───────────────────────────────

// Variabele voor de zoekopdracht
$zoek = "";

// Controleer of er iets is ingevoerd in het zoekveld
if (isset($_GET['zoek'])) {

    // Beveilig de invoer tegen SQL-injectie
    $zoek = $conn->real_escape_string($_GET['zoek']);

    // SQL query: zoek leden waarvan de naam overeenkomt met de zoekopdracht
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email
            FROM ledenoverzicht
            WHERE leden LIKE '%$zoek%'";
} else {

    // Als er niet gezocht wordt, haal alle leden op
    $sql = "SELECT leden, lidnummer, lessen, Leeftijd, email FROM ledenoverzicht";
}

// Voer de query uit
$result = $conn->query($sql);

// Maak een lege array om de resultaten in op te slaan
$data = [];

// Controleer of er resultaten zijn en sla ze op in de array
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_object()) {
        $data[] = $row;
    }
}

// Sluit de databaseverbinding
$conn->close();
?>

<!DOCTYPE html>
<html lang="nl">
<head>
    <meta charset="UTF-8">
    <title>Leden Overzicht</title>

    <!-- Bootstrap CSS voor styling -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Leden Overzicht</h1>

        <!-- Knop om de popup (modal) te openen voor nieuw lid toevoegen -->
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#nieuwLidModal">
            ➕ Nieuw lid toevoegen
        </button>
    </div>

    <!-- Toon melding na het toevoegen van een lid (succes, fout of waarschuwing) -->
    <?php if ($melding) : ?>
        <div class="alert alert-<?= $melding_type ?> alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($melding) ?>
            <!-- Sluitknop voor de melding -->
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- ── Zoekformulier ──────────────────────────────────────────────────── -->
    <!-- Hiermee kan een gebruiker zoeken op achternaam via GET -->
    <form method="GET" class="mb-4">
        <div class="input-group">

            <!-- Invoerveld voor de zoekopdracht -->
            <input type="text"
                   name="zoek"
                   class="form-control"
                   placeholder="Zoek op achternaam..."
                   value="<?= htmlspecialchars($zoek) ?>">

            <!-- Zoekknop -->
            <button class="btn btn-primary" type="submit">Zoeken</button>

            <!-- Toon de "Wis filter" knop alleen als er een actief zoekfilter is -->
            <?php if ($zoek) : ?>
                <a href="index.php" class="btn btn-outline-secondary">✕ Wis filter</a>
            <?php endif; ?>

        </div>
    </form>

    <!-- ── Ledentabel ─────────────────────────────────────────────────────── -->
    <table class="table table-striped table-bordered">

        <thead class="table-dark">
            <tr>
                <!-- Kolomtitels van de tabel -->
                <th>Lid</th>
                <th>Lidnummer</th>
                <th>Les</th>
                <th>Leeftijd</th>
                <th>Email</th>
            </tr>
        </thead>

        <tbody>

            <?php if (!empty($data)) : ?>

                <!-- Loop door alle gevonden leden en toon ze in de tabel -->
                <?php foreach ($data as $ledenOverzicht) : ?>
                    <tr>
                        <!-- Toon gegevens en bescherm tegen XSS met htmlspecialchars -->
                        <td><?= htmlspecialchars($ledenOverzicht->leden) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->lidnummer) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->lessen) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->Leeftijd) ?></td>
                        <td><?= htmlspecialchars($ledenOverzicht->email) ?></td>
                    </tr>
                <?php endforeach; ?>

            <?php else : ?>

                <!-- Toon een melding als er geen leden gevonden zijn -->
                <tr>
                    <td colspan="5" class="text-center text-danger">
                        Geen leden gevonden<?= $zoek ? " met deze achternaam" : "" ?>.
                    </td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

</div>

<!--  Modal: Nieuw lid toevoegen -->
<!-- Dit is de popup die verschijnt als je op de knop "Nieuw lid toevoegen" klikt -->
<div class="modal fade" id="nieuwLidModal" tabindex="-1" aria-labelledby="nieuwLidModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">

            <!-- Bovenkant van de popup met titel en sluitknop -->
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="nieuwLidModalLabel">➕ Nieuw lid toevoegen</h5>
                <!-- Sluitknop rechtsboven in de popup -->
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Formulier binnen de popup -->
            <!-- Wordt verstuurd via POST naar dezelfde pagina -->
            <form method="POST">

                <!-- Verborgen veld om aan te geven welke actie uitgevoerd moet worden -->
                <input type="hidden" name="actie" value="toevoegen">

                <div class="modal-body">

                    <!-- Veld voor de naam van het lid -->
                    <div class="mb-3">
                        <label class="form-label">Naam</label>
                        <input type="text"
                               name="leden"
                               class="form-control"
                               placeholder="Voor- en achternaam"
                               required>
                    </div>

                    <!-- Veld voor de les waar het lid aan deelneemt -->
                    <div class="mb-3">
                        <label class="form-label">Les</label>
                        <input type="text"
                               name="lessen"
                               class="form-control"
                               placeholder="Bijv. Yoga, Zwemmen..."
                               required>
                    </div>

                    <!-- Veld voor de leeftijd van het lid -->
                    <div class="mb-3">
                        <label class="form-label">Leeftijd</label>
                        <input type="number"
                               name="leeftijd"
                               class="form-control"
                               placeholder="Leeftijd"
                               min="1"
                               max="120"
                               required>
                    </div>

                    <!-- Veld voor het e-mailadres van het lid -->
                    <div class="mb-3">
                        <label class="form-label">E-mailadres</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               placeholder="naam@voorbeeld.nl"
                               required>
                    </div>

                    <!-- Lidnummer wordt automatisch ingevuld door de database (AUTO_INCREMENT) -->
                    <!-- Daarom staat er geen veld voor lidnummer in dit formulier -->

                </div>

                <!-- Onderkant van de popup met knoppen -->
                <div class="modal-footer">

                    <!-- Knop om de popup te sluiten zonder op te slaan -->
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        Annuleren
                    </button>

                    <!-- Knop om het formulier te versturen -->
                    <button type="submit" class="btn btn-success">
                        Lid opslaan
                    </button>

                </div>

            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS — nodig voor de modal en de sluitknop van de alert -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>