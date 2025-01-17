<?php

function isDateInMonth($date, $month, $year) {
    // Convertir la date en un objet DateTime
    $dateTime = new DateTime($date);
    
    // Obtenir le mois et l'année de la date
    $dateMonth = (int) $dateTime->format('m');
    $dateYear = (int) $dateTime->format('Y');

    // Vérifier si le mois et l'année correspondent
    if ($dateMonth === $month && $dateYear === $year) {
        return true;
    }
    return false;
}

?>