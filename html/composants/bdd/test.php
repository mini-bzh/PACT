<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
$driver = "pgsql";

$server = "tripskell.ventsdouest.dev";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

// Connexion à la base de données

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif


$query1 = "SELECT * FROM tripskell._reponseavis";
$query2 = "SELECT * FROM tripskell._compte";
$query4 = "SELECT * FROM tripskell._compte WHERE login = 'RocheJagu'";

$sth = $dbh->prepare($query2);


$sth->execute();

$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    print_r($row);
    //echo $row["secretotp"] == null;
    echo "<br><br>";
}
