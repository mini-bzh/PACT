<?php

    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    
    // Récupération des paramètres de connexion à la base de données
    include('../bdd/connection_params.php');

    $request = $_POST["request"];

    // Connexion à la base de données
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif
    
    $stmt = $dbh->query("select count(*) from tripskell.".$request);

    $result = $stmt->fetchAll()[0]['count'];

    print_r($result);