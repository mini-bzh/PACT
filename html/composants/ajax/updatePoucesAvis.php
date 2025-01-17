<?php
    print_r($_POST);

    // recuperation des parametre de connection a la BdD
    include('../composants/bdd/connection_params.php');
        
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    if($_POST["pouce"] == "like")
    {
        $query =    "UPDATE tripskell._avis SET nbpoucesbleu = nbpoucesbleu + :changement WHERE id_avis = :idAvis";
    }
    else if($_POST["pouce"] == "dislike")
    {
        $query =    "UPDATE tripskell._avis SET nbpoucesrouge = nbpoucesrouge + :changement WHERE id_avis = :idAvis";
    }

    $stmt = $dbh->prepare($query);

    $stmt->bindParam(":changement", $_POST["changement"]);
    $stmt->bindParam(":idAvis", $_POST["idAvis"]);

    $stmt->execute();
?>