<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif


if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.
    
// ici on exploite les fichier image afin de les envoyer dans un dossier du git dans le but de stocker les images reçus
$i = 0;
foreach ($_FILES as $key_fichier => $fichier) { // on parcour les fichiers de la super globale $_FILES

    $nom_img[$key_fichier] = null; // initialistion des noms des images a null

    if ($fichier["size"]!=0) {  // on verifie que le fichier a ete transmit

        // creation du nom de fichier en utilisant time et le type de fichier
        $nom_img[$key_fichier] = time() + $i++ . "." . explode("/", $_FILES[$key_fichier]["type"])[1];

        // deplacement du fichier depuis l'espace temporaire
        move_uploaded_file($fichier["tmp_name"], "../images/imagesAvis/" . $nom_img[$key_fichier]);
    }
}


$requete = "INSERT INTO tripskell.avis(";
$requete .= "commentaire, ";
$requete .= "imageavis, ";
$requete .= "dateexperience, ";
$requete .= "datepublication, ";
$requete .= "id_c, ";
$requete .= "idoffre,";
$requete.= "titreavis) ";

$requete .= "VALUES (";
$requete .= ":commentaire, ";
$requete .= ":imageavis, ";
$requete .= ":dateexperience, ";
$requete .= ":datepublication, ";
$requete .= ":id_c, ";
$requete .= ":idoffre,";
$requete.= ":titreavis) ";

echo $requete;

$stmt = $dbh->prepare($requete);
$stmt->bindParam(":commentaire", $_POST["commentaire"]);
$stmt->bindParam(":imageavis", $_POST["imageavis"]);
$stmt->bindParam(":dateexperience", $_POST["dateexperience"]);
$stmt->bindParam(":datepublication", $nom_img['datepublication']);
$stmt->bindParam(":id_c", $_POST["id_c"]);
$stmt->bindParam(":idoffre", $_POST["idoffre"]);
$stmt->bindParam(":titreavis", $_POST["titreavis"]);  // on ajoute le code postal à la requete

print_r($stmt);
$stmt->execute(); // execution de la requete

// on ferme la base de donnée
$dbh = null;

header("Location: /pages/accueil.php"); // on redirige vers la page de l'offre créée
}

?>