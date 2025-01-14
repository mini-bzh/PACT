<?php
    print_r($_POST);

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
        
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
    $query = "UPDATE tripskell._avis SET luparpro = true WHERE id_avis = :idAvis";

    $stmt = $dbh->prepare($query);

    $stmt->bindParam(":idAvis", $_POST["idAvis"]);

    $stmt->execute();

    echo "avis " . $_POST["idAvis"] . " lu";
?>