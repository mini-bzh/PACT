<?php
    $compteMembre = null;


    if(key_exists("idCompte", $_SESSION)) {
        //
        // Creation requete pour compter le nombre de 
        // fois que l'idCompte(dans $_SESSION) apparait
        // dans la table membre
        // 
        // (une ou zero fois)
        //
        $stmt = $dbh->prepare("select count(*) from (select id_c from tripskell.membre where id_c=:id_c);"); 
        
        // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
        $stmt->bindParam(":id_c", $id_c);
        $id_c = $_SESSION["idCompte"];

        $stmt->execute();   // execution de la requete

        // recuperation de la reponse et mise en forme pour ne
        // garder que le charactere 1 ou 0
        $compteMembre = $stmt->fetchAll()[0]["count"];

        // suppression de la requete
        $stmt = null;
        $id_c = null;
    }