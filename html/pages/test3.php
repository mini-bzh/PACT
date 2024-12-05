<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// $ouverture = $dbh->query("select * from tripskell.pro_privee")->fetchAll();

// echo "ouverture <br>";
// print_r($ouverture);

$stmt = $dbh->prepare("select titreoffre, carte from tripskell.offre_visiteur");
$stmt->execute();
$result = $stmt->fetchAll();

// echo("<br>horaire<br>");
foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("select * from tripskell._ouverture where idoffre = 11");
$stmt->execute();
$result = $stmt->fetchAll();

echo("<br>ouverture<br>");
foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}

$stmt = $dbh->prepare("select * from tripskell._horaire");
$stmt->execute();
$result = $stmt->fetchAll();

echo("<br>horaire<br>");
foreach ($result as $row)
{
    print_r($row);
    echo "<br>";
}



/*$query = "UPDATE tripskell._horaire SET horaire_matin_debut = '12:34', horaire_matin_fin = '13:43', horaire_aprem_debut = '16:16:00', horaire_aprem_fin = '17:20' WHERE id_hor = '36'";

$stmt = $dbh->prepare($query);
$stmt->execute();*/

$lang_pres = is_null($dbh->query("select nomlangue from tripskell._possedelangue where idOffre=1 and nomlangue='Français';")->fetch()["nomlangue"]);

echo("français" . $lang_pres);

$stmt = $dbh->prepare("SELECT * FROM tripskell._possedelangue");
$stmt->execute();


echo("<br>_possedelangue<br>");
print_r($stmt->fetchAll());

