<?php

    // contient fonction affichage_etoiles pour afficher les etoiles
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    $query = "SELECT * FROM TRIPSKELL.offre_visiteur";
    $stmt = $dbh->prepare($query);
    $stmt->execute();

    $result = $stmt->fetchAll();

    echo "<br>résultat offre_visiteur<br>";
    foreach ($result as $row) {
        echo "<br>";
        print_r($row);
        echo "<br>";
    }
