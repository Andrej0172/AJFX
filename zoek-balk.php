<?php

function filterLessen($conn, $zoek, $min, $max)
{
    $sql = "SELECT * FROM lessenoverzicht WHERE 1=1";

    // zoekfunctie
    if ($zoek != "") {
        $zoek = strtolower($zoek);
        $sql .= " AND (LOWER(lessen) LIKE '%$zoek%' 
                    OR LOWER(trainer) LIKE '%$zoek%' 
                    OR LOWER(locatie) LIKE '%$zoek%')";
    }

    // prijs filter
    if ($min != "" && $max != "") {
        $sql .= " AND lesprijs BETWEEN $min AND $max";
    }

    $sql .= " ORDER BY datum, tijd";

    return $conn->query($sql);
}
?>