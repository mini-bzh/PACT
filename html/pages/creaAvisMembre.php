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
        move_uploaded_file($fichier["tmp_name"], "../images/pdp/" . $nom_img[$key_fichier]);
    }
}


$requete = "INSERT INTO tripskell.membre(";
$requete .= "login, ";
$requete .= "adresse_mail, ";
$requete .= "mot_de_passe, ";
$requete .= "pdp, ";
$requete .= "numero_tel, ";
$requete .= "nom,";
$requete .= "codepostal, ";
$requete.= "prenom) ";

$requete .= "VALUES (";
$requete .= ":Login,";
$requete .= ":Adresse_Mail,";
$requete .= ":Mot_de_P,";
$requete .= ":fichier1,";
$requete .= ":Telephone,";
$requete .= ":Nom, ";
$requete .= ":codePostal,";
$requete .= ":Prenom); ";

echo $requete;

$stmt = $dbh->prepare($requete);
$stmt->bindParam(":Login", $_POST["Login"]);
$stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
$stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
$stmt->bindParam(":fichier1", $nom_img['fichier1']);
$stmt->bindParam(":Telephone", $_POST["Telephone"]);
$stmt->bindParam(":Nom", $_POST["Nom"]);
 $stmt->bindParam(":codePostal", $_POST["codePostal"]);  // on ajoute le code postal à la requete
$stmt->bindParam(":Prenom", $_POST["Prenom"]);

print_r($stmt);
$stmt->execute(); // execution de la requete

// on ferme la base de donnée
$dbh = null;

header("Location: /pages/accueil.php"); // on redirige vers la page de l'offre créée
}

?>