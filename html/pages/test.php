<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$stmt = $dbh->prepare("select * from tripskell.avis");
$stmt->execute();
$result = $stmt->fetchAll();

$stmt = $dbh->prepare("select titreOffre from tripskell.offre_visiteur where idoffre = 1");
$stmt->execute();
$titreOffre = $stmt->fetchAll()[0]["titreoffre"];

foreach ($result as $row)
{
    print_r($row);
    ?><br><br><?php
}