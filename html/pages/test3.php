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

$query = "SELECT * from tripskell._ouverture where idoffre = 11";
$stmt = $dbh->prepare($query);

$stmt->execute();

$result = $stmt->fetchAll();

echo "<br>--------------------------------------<br>";  
foreach ($result as $row) 
{
    $stmt = $dbh->prepare("select * from tripskell._horaire where id_hor = :id_hor");
    $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_STR);
    $stmt->execute();

    
    print_r($stmt->fetchAll());
}

/*$query = "UPDATE tripskell._horaire SET horaire_matin_debut = '12:34', horaire_matin_fin = '13:43', horaire_aprem_debut = '16:16:00', horaire_aprem_fin = '17:20' WHERE id_hor = '36'";

$stmt = $dbh->prepare($query);
$stmt->execute();*/


$stmt = $dbh->prepare("SELECT idoffre, titreoffre FROM tripskell._offre");
$stmt->execute();


echo("<br>offres<br>");
print_r($stmt->fetchAll());

