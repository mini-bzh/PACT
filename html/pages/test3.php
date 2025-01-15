<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare( "UPDATE tripskell._avis SET luparpro = false WHERE id_avis = 1 OR id_avis = 2 OR id_avis = 3");

$stmt->execute();
$result = $stmt->fetchAll();


$stmt = $dbh->prepare("SELECT * from tripskell._avis");
$stmt->execute();

$result = $stmt->fetchAll();

echo("<br>resultat _avis<br>");

foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}



$idCompte = 1;
$query =    "SELECT COUNT(*) from tripskell._offre JOIN tripskell._avis ON tripskell._offre.idoffre = tripskell._avis.idoffre 
                                            WHERE tripskell._offre.id_c = :idCompte AND luparpro = false";
$stmt = $dbh->prepare($query);


$stmt->bindParam(":idCompte", $idCompte);

$stmt->execute();
echo("resultat count");/*
print_r($stmt->fetch());*/

echo $stmt->fetch()["count"];

