<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../php/verif_compte_pro.php');

// Creation requete pour recuperer les offres
// du professionnel connecte
$stmt = $dbh->prepare("select * from tripskell.offre_pro where id_c=:id_c;");

// binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
$stmt->bindParam(":id_c", var: $id_c);
$id_c = $_SESSION["idCompte"];

$stmt->execute();   // execution de la requete

// recuperation de la reponse et mise en forme
$contentMesOffres = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestion des offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/pages/gestionOffres.css">
</head>
<body>
    
</body>
</html>