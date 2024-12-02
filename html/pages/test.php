<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("SELECT add_horaire(
    1,               -- idOffre
    '08:00',         -- deb_matin
    '12:00',         -- fin_matin
    '14:00',         -- deb_aprem
    '18:00',         -- fin_aprem
    'Lundi'          -- jour
);");
$stmt->execute();

echo("fonction exécutée");

$stmt = $dbh->prepare("select * from tripskell._horaire");
$stmt->execute();
$result = $stmt->fetchAll();

foreach ($result as $row)
{
    print_r($row);
    ?><br><br><?php
}

echo("nb results _horaires : " . sizeof($result));

$stmt = $dbh->prepare("select * from tripskell._ouvrture");
$stmt->execute();
$result = $stmt->fetchAll();

foreach ($result as $row)
{
    print_r($row);
    ?><br><br><?php
}

echo("nb results _ouverture : " . sizeof($result));