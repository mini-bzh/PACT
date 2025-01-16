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


$query =    "SELECT * from tripskell._reponseAvis";
$stmt = $dbh->prepare($query);

$stmt->execute();
$result = $stmt->fetchAll();

echo("resultat reponses");
foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}


