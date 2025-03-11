<?php

/* On récupère les 10 dernières offres créées */
$stmt = $dbh->prepare("SELECT idoffre FROM tripskell.offre_visiteur ORDER BY datepublication DESC LIMIT 10;");

$stmt->execute();
$nouvellesOffresId = $stmt->fetchAll(PDO::FETCH_COLUMN);

?>