<?php
    // Récupération des paramètres de connexion à la base de données
    include('../php/connection_params.php');

    $idAvis = $_POST["idAvis"];

    // Connexion à la base de données
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

    $stmt = $dbh->prepare("DELETE FROM tripskell._avis WHERE id_avis = " . $idAvis . ";");
    $stmt->execute();
