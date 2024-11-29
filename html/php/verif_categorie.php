<?php
    function categorie($idOffre) {
        // recuperation des parametre de connection a la BdD
        include('connection_params.php');

        // connexion a la BdD
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
        
        if(!empty( $dbh->query("select * from tripskell._visite where idoffre=" . $idOffre . ";")->fetchAll() )) {
            return "visite";
        }
        if(!empty( $dbh->query("select * from tripskell._spectacle where idoffre=" . $idOffre . ";")->fetchAll() )) {
            return "spectacle";
        }
        if(!empty( $dbh->query("select * from tripskell._parcAttraction where idoffre=" . $idOffre . ";")->fetchAll() )) {
            return "parc d'attraction";
        } 
        if(!empty( $dbh->query("select * from tripskell._restauration where idoffre=" . $idOffre . ";")->fetchAll() )) {
            return "restauration";
        } 
        if(!empty( $dbh->query("select * from tripskell._activite where idoffre=" . $idOffre . ";")->fetchAll() )) {
            return "activité";
        }
    }
?>