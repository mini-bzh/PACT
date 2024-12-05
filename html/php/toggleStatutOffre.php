<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include('../php/connection_params.php');

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les paramètres envoyés via POST
    $idOffre = $_POST["idOffre"];


    $stockEnLigne = $dbh->query("SELECT enLigne from tripskell.offre_pro where idoffre = " . $idOffre . ";");
    $stmt = $dbh->prepare("SELECT enLigne from tripskell.offre_pro where idoffre = " . $idOffre . ";");
    $stmt->execute();
    $enLigne = $stmt->fetchAll()[0]["enligne"];

    if ($enLigne) {
        $stmt = $dbh->prepare("UPDATE tripskell.offre_pro SET enligne = false where idoffre = " . $idOffre . ";");
    } else {
        $stmt = $dbh->prepare("UPDATE tripskell.offre_pro SET enligne = true where idoffre = " . $idOffre . ";");
    }

    $stmt->execute();
    $result = $stmt->fetchAll();

    if ($enLigne) {
        echo ("Votre offre est maintenant hors ligne, invisible pour les autres utilisateurs. Vous pouvez la modifier.");
       
    } else {
        echo ("Votre offre est maintenant en ligne, visible par les autres utilisateurs. Mettez la hors ligne pour pouvoir la modifier.");
    }
} else {
    echo "erreur : nous n'avons pas pu changer le statut de votre offre";
}
?>

<?php

?>