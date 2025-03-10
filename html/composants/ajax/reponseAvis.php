<?php
    // Récupération des paramètres de connexion à la base de données
    include('../bdd/connection_params.php');

    // Connexion à la base de données
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

    $query = "INSERT INTO tripskell._reponseAvis (textereponseavis, id_avis, id_c) VALUES (:texteReponse, :idAvis, :idCompte)";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(":texteReponse", $_POST["reponseAvis"]);
    $stmt->bindParam(":idAvis", $_POST["idAvis"]);
    $stmt->bindParam("idCompte", $_POST["idCompte"]);

    $stmt->execute();
