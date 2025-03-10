<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
$driver = "pgsql";

$server = "tripskell.ventsdouest.dev";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

echo "cc";

// Connexion à la base de données

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
echo "cv";

$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif


$query = "SELECT idoffre FROM tripskell._avis WHERE id_avis = 5";
$sth = $dbh->prepare($query);

$sth->execute();
echo "oe";


$result = $sth->fetchAll(PDO::FETCH_ASSOC);

foreach ($result as $row) {
    echo($row["idoffre"]);
    echo "<br><br>";
}