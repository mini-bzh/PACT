<?php
session_start();

if ((isset($_POST['action'])) && ($_POST['action'] === 'supprimerCompte')) {  // Si on reçoit via POST l'ordre d'executer (depuis suppressionCompte.js)
    $id_c = $_SESSION['idCompte'];

    // Récupération des paramètres de connexion à la base de données
    include('../bdd/connection_params.php');

    // Connexion à la base de données
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $pdo->prepare("DELETE FROM tripskell.membre WHERE id_c = $id_c");
    $stmt->execute();

    $_SESSION['idCompte'] = null; // puis on le déconnecte
}


?>