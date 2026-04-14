<?php
$conn = new mysqli("localhost", "root", "", "lessen");

$les = $_GET['les'] ?? '';
$datum = $_GET['datum'] ?? '';
$tijd = $_GET['tijd'] ?? '';

$sql = "DELETE FROM lessenoverzicht 
        WHERE lessen='$les' 
        AND datum='$datum' 
        AND tijd='$tijd'";

$conn->query($sql);

header("Location: lessen-overzicht.php");
exit;
?>