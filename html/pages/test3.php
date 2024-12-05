<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// $ouverture = $dbh->query("select * from tripskell.pro_privee")->fetchAll();

// echo "ouverture <br>";
// print_r($ouverture);

$stmt = $dbh->prepare("select img1, img2, img3, img4 from tripskell._offre");
$stmt->execute();
$result = $stmt->fetchAll();

// echo("<br>horaire<br>");
foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}
