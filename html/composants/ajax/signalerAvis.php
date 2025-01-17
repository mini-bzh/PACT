<?php
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Récupération des paramètres de connexion à la base de données
    include('../composants/bdd/connection_params.php');

    $idCompte = $_POST["idCompte"];
    $motifSignalement = $_POST["motifSignalement"];
    $idAvis = $_POST["idAvis"];

    // Connexion à la base de données
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

    $stmt = $dbh->prepare("INSERT INTO tripskell._signaleravis (id_avis, id_c, content) VALUES (". $idAvis . "," . $idCompte . ", '" . $motifSignalement . "');"); //ajout dans la bdd du signalement
    $stmt->execute();
?>