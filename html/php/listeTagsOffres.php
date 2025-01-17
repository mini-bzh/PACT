<?php

include('../composants/bdd/connection_params.php');

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associatif

$stmt = $dbh->query("SELECT * from tripskell._possede;");

$stmt->execute();
$rows = $stmt->fetchAll();

header('Content-Type: application/json');

echo json_encode($rows);

?>