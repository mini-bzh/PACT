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
$requete .= "cadreexperience, ";
$requete .= "id_c, ";
$requete .= "idoffre,";
$requete.= "titreavis) ";

$requete .= "VALUES (";
$requete .= ":commentaire, ";
$requete .= ":imageavis, ";
$requete .= ":dateexperience, ";
$requete .= ":datepublication, ";
$requete .= ":cadreexperience, ";
$requete .= ":id_c, ";
$requete .= ":idoffre,";
$requete.= ":titreavis);";

echo $requete;
print_r($_POST);

$datePublication = date("d/m/Y");

/*echo("commentaire : " . strlen($_POST["commentaire"]));
echo("imageavis : " . strlen($nom_img["fichier1"]));
echo("dateexperience : " . strlen($_POST["dateExperience"]));
echo("datepublication : " . ($datePublication));
echo("cadreexperience : " . strlen($_POST["contexte"]));*/

print_r($_SESSION);
echo("id_c : " . strlen($_SESSION["idCompte"]));
echo("idoffre : " . ($_GET["idOffre"]));
//echo("titreavis : " . ($_POST["titre"]));


$nul = null;

$stmt = $dbh->prepare($requete);
$stmt->bindParam(":commentaire", $_POST["commentaire"]);    
$stmt->bindParam(":imageavis", $nom_img["fichier1"]);
$stmt->bindParam(":dateexperience", $_POST["dateExperience"]);
$stmt->bindParam(":datepublication", $datePublication);
$stmt->bindParam(":cadreexperience", $_POST["contexte"]);
$stmt->bindParam(":id_c", $_SESSION["idCompte"]);
$stmt->bindParam(":idoffre", $_GET["idOffre"]);
$stmt->bindParam(":titreavis", $_POST["titre"]);

print_r($stmt);
$stmt->execute(); // execution de la requete

// on ferme la base de donnée
$dbh = null;

header("Location: /pages/detailOffre.php?idOffre=" . $_GET["idOffre"]); // on redirige vers la page de l'offre créée
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un avis</title>

    <!-- Favicon -->
    <link rel="icon" href="/icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/pages/style.css">
    <link rel="stylesheet" href="../style/pages/CreaCompteMembre.css">

    <link rel="stylesheet" href="../style/pages/creaAvis.css">
</head>
<?php include "../composants/header/header.php";        //import navbar
        ?>
<body class="fondVisiteur">

    <form name="creation" action="/pages/creaAvis.php?idOffre=<?php echo $_GET["idOffre"]?>" method="post" enctype="multipart/form-data">
        <div id="conteneurTitreForm">
            <h3>Ajouter un avis</h3>
            <div>
                <p>Les champs qui possède une <span class="Asterisque"> * </span> sont obligatoires.</p> 
            </div>
        </div>

        <div class="champs">
            <label for="titre">Titre<span class="required">*</span> :</label>
            <input type="text" id="titre" name="titre" placeholder="Entrez le titre de votre avis (max 20 caractères)" maxlength="20" required>
        </div>
        <!--<div class="champs">
        <label for="note">Note <span class="required">*</span> :</label>

            <div id="conteneurNote">
                <input type="text" id="note" name="note" placeholder="Note" required>
                <p>/5</p>
                <img src="../icones/etoilePleineSVG.svg" alt="étoile">
            </div>
        </div>   -->

        <div class="champs">
            <label for="commentaire">Commentaire <span class="required">*</span> :</label>
            <textarea type="text" maxlength="200" id="commentaire" name="commentaire" placeholder="Qu'avez-vous pensé de <?php 
                $stmt = $dbh->prepare("select titreOffre from tripskell.offre_visiteur where idoffre = ".$_GET["idOffre"]);
                $stmt->execute();
                $titreOffre = $stmt->fetchAll()[0]["titreoffre"];
                echo $titreOffre;
            ?> ? (max 200 caractères)" required></textarea>
        </div>
        <div id="conteneurContexteDate">
            <div class="champs">
                <label for="contexte">Contexte de la visite <span class="required">*</span> :</label>
                <input type="hidden" name="contexte" id="inputContexte">
                <div id="menuContexte">
                    <div class="conteneurSVGtexte">
                        <img src="../icones/chevronUpSVG.svg" alt="chevron haut">
                        <p>Séléctionner un contexte</p>
                    </div>
                    <div id="conteneurOptionsContexte">
                        <p>en solo</p>
                        <p>en famille</p>
                        <p>entre amis</p>
                        <p>affaires</p>
                    </div>
                </div>
            </div>
            
            <div class="champs">
                <label for="dateExperience">Date de la visite<span class="required">*</span> :</label>
                <input type="date" name="dateExperience" id="dateExperience" required>
            </div>
        </div>
        <div class="champs" id="selectPhoto">
            <label for="fichier1" id="customFileLabel">Ajouter une photo</label>
            <input type="file" id="fichier1" name="fichier1" accept="image/png, image/jpeg" onchange="updateFileName()" >
            <span id="fileName" class="file-name"></span> <!-- Zone pour afficher le nom -->
        </div>

        <div id="conteneurConfirmation">
            <input type="checkbox" name="certifAvis" required>
            <label for="certifAvis">En publiant cet avis, je certifie qu'il reflète ma propre opinion et mon expérience, que je n'ai aucun lien
                (professionnel ou personnel) avec le professionnel de tourisme de cette offre, et que je n'ai reçu aucune compensation financière
                ou autre de sa part pour rédiger cet avis.
            </label>
        </div>
        <div class="zoneBtn">
            <a href="/pages/detailOffre.php?idOffre=<?php echo $_GET['idOffre']?>" class="btnAnnuler">
                <p class="texteLarge boldArchivo">Annuler</p>
                <?php
                include '../icones/croixSVG.svg';
                ?>
            </a>

            <button type="submit" href="#" class="btnConfirmer">
                    <p class="texteLarge boldArchivo">Confirmer</p>
            <?php
                    include '../icones/okSVG.svg';
            ?>
            </button>
        </div>
    </form>

</body>
<script src="../js/creaAvis.js"></script>
</html>