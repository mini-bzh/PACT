<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("select * from tripskell._ouverture where idoffre = 1");
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

$query = "SELECT * from tripskell._ouverture where idoffre = 1";
$stmt = $dbh->prepare($query);

$idOffre = 1;
$stmt->execute();

$result = $stmt->fetchAll();

echo "<br>--------------------------------------<br>";  
foreach ($result as $row) 
{
    $stmt = $dbh->prepare("select * from tripskell._horaire where id_hor = :id_hor");
    $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_STR);
    $stmt->execute();

    
    print_r($stmt->fetchAll());


    /*$query =    "UPDATE tripskell._horaire
                SET horaire_matin_debut = :debMatin, horaire_matin_fin = :finMatin, horaire_aprem_debut = :debAprem, horaire_aprem_fin = :finAprem
                WHERE id_hor = :id_hor";

    $stmt = $dbh->prepare($query);

    $debMatin = '11:11';
    $finMatin = '12:12';
    $debAprem = '13:13';
    $finAprem = '14:14';

    $stmt->bindValue(':debMatin', $debMatin, $debMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':finMatin', $finMatin, $finMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':debAprem', $debAprem, type: $debAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':finAprem', $finAprem, $finAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
    $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_STR);

    $stmt->execute();

    $stmt = $dbh->prepare("select * from tripskell._horaire where id_hor = :id_hor");
    $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_STR);
    $stmt->execute();
    print_r($stmt->fetchAll());*/
}