<?php
session_start();

if ((isset($_POST['action'])) && ($_POST['action'] === 'supprimerOffre') && isset($_POST['idOffre'])) {
    $id_c = $_SESSION['idCompte']; 
    $idOffre = $_POST['idOffre']; 

    // Récupération des paramètres de connexion à la base de données
    include('../bdd/connection_params.php');

    // Connexion à la base de données
    $pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Suppression de l'offre dans la base de données
    $stmt = $pdo->prepare("DELETE FROM tripskell.offre_pro WHERE idoffre = :idOffre AND id_c = :id_c");
    $stmt->bindParam(':idOffre', $idOffre, PDO::PARAM_INT);
    $stmt->bindParam(':id_c', $id_c, PDO::PARAM_INT);
    $stmt->execute();
}
?>
