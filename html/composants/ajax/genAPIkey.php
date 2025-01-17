<?php

session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat



if (array_key_exists("idCompte", $_SESSION)) {
    $idCompte = $_SESSION['idCompte'];
}

// Vérifier si la clé API a été envoyée
if (isset($_POST['apiKey'])) {
    
    $apiKey = $_POST['apiKey']; // on récupère la clé API

    // Requête qui permet l'envoie de la clé API dans la base
    $stmt = $dbh->prepare("UPDATE tripskell._compte set clefAPI = '" . $apiKey . "' where id_c  = " . $idCompte . " ;");
    $stmt->execute();

    // Réponse de succès
    echo 'Clé API enregistrée avec succès.';
} else {
    // Si la clé API n'est pas présente, afficher une erreur
    echo 'Erreur: Aucune clé API reçue.';
}
