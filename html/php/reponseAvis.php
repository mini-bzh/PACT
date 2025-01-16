<?php
    // Récupération des paramètres de connexion à la base de données
    include('../php/connection_params.php');

    // Connexion à la base de données
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

    $query = "INSERT INTO tripskell._reponseAvis (textereponseavis, id_c) VALUES (:texteReponse, :idPro)";

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(":texteReponse", $_POST["reponseAvis"]);
    $stmt->bindParam("idPro", $_POST["idAvis"]);

    $stmt->execute();
