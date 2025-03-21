<?php
    // recuperation des parametre de connection a la BdD
    include('../bdd/connection_params.php');

    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

   /* $query = "";

    $stmt = $dbh->prepare($query);

    $stmt->bindParam(":idAvis", $_POST["idAvis"]);

    $stmt->execute();*/

    if($_POST["otp"] == "valide"){
        echo 1;
    }
    else{
        echo 0;
    }