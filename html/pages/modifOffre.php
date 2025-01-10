<?php

use Sabberworm\CSS\CSSList\KeyFrame;
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

// Inclusion du script pour vérifier si l'utilisateur a un compte pro
include('../php/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"]))
{
    header("Location: /pages/erreur404.php");
    exit();
}

if (isset($_GET["idOffre"])) {
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // On cherche dans quelle catégorie est l'offre
<<<<<<< Updated upstream
    foreach(['visite', 'restauration', 'spectacle', 'parcattraction', 'activite'] as $nom_cat) 
    {
=======
    foreach(['visite', 'restauration', 'spectacle', 'parcattraction', 'activite'] as $nom_cat) {
>>>>>>> Stashed changes
        
        // Requête pour chercher la catégorie
        $stmt = $dbh->prepare("SELECT idoffre FROM tripskell._" . $nom_cat . " WHERE idOffre = :idOffre;");
        $stmt->execute([ ':idOffre' => $idOffre]);

        // Si l'offre appartient à une catégorie, on envoie la catégorie au JS
<<<<<<< Updated upstream
        if(isset($stmt->fetch()['idoffre']) && empty($_POST)){?>
        <script>
=======
        if(isset($stmt->fetch()['idoffre'])){?>
            <script>
>>>>>>> Stashed changes
                let categorie_offre = '<?php echo $nom_cat; ?>';
            </script>
        <?php

            // Si c'est une visite, on récupère les langues
            $langue_preselec = array();
            if($nom_cat === 'visite') {
                $stmt = $dbh->prepare("SELECT nomlangue FROM tripskell._possedelangue WHERE idOffre = :idOffre;");
                $stmt->execute([ ':idOffre' => $idOffre]);
                $langue_preselec = array_column($stmt->fetchAll(), 'nomlangue');
            }
        }
    }

    // Récupération des langues
    $stmt = $dbh->prepare("SELECT nomlangue FROM tripskell._langue;");
    $stmt->execute();
    $langues = array_column($stmt->fetchAll(), 'nomlangue');

    // Récupération des détails de l'offre à partir de la base de données
    $contentOffre = $dbh->query("SELECT * FROM tripskell.offre_pro WHERE idOffre='" . $idOffre . "';")->fetchAll()[0];
    


    // requete pour avoir la liste des tags
    $stmt = $dbh->prepare("select * from tripskell._tags");
    $stmt->execute();
    $liste_tags = $stmt->fetchAll();

    // requete pour avoir la liste des tags pour préremplir
    $stmt = $dbh->prepare("select nomtag from tripskell._possede where idOffre=:idOffre");
    $stmt->execute(["idOffre" => $_GET["idOffre"]]);
    $liste_tags_preselec = array_column($stmt->fetchAll(),"nomtag");
    
} else {
    die("Error");
}

if (key_exists("idCompte", $_SESSION)) {
<<<<<<< Updated upstream
    // Récupération de id_c de pro_prive
    $idpropriveResult = $dbh->query("select id_c from tripskell.pro_prive where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll();
    
    if (count($idpropriveResult) > 0) {
        $idproprive = $idpropriveResult[0];
    } else {
        // Si aucun résultat n'est trouvé, vous pouvez gérer cette situation ici
        $idproprive = null;  // Par exemple, on définit $idproprive comme null
    }

    if (!isset($idproprive)) {
        // Récupération de id_c de pro_public si pro_prive n'a pas donné de résultat
        $idpropublicResult = $dbh->query("select id_c from tripskell.pro_public where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll();
        
        if (count($idpropublicResult) > 0) {
            $idpropublic = $idpropublicResult[0];
        } else {
            // Si aucun résultat n'est trouvé ici aussi, vous pouvez définir $idpropublic comme null
            $idpropublic = null;
        }
    }
=======
    // Récupération de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];

    // Récupération de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];
>>>>>>> Stashed changes
}

if (!empty($_FILES) ) {
    if (isset($_FILES['carte']) && $_FILES['carte']['size'] > 0) {
        $requete = "UPDATE tripskell.offre_pro SET ";
        $requete .= "carte = :carte ";
        $requete .= "WHERE idOffre = :idOffre;";

        $stmt = $dbh->prepare($requete);

        $stmt->bindParam(":carte", $nom_img_carte);
        $stmt->bindParam(":idOffre", $idOffre);

        $nom_img_carte = time() + $i++ . "." . explode("/", $_FILES['carte']["type"])[1];
        move_uploaded_file($_FILES['carte']["tmp_name"], "../images/imagesCarte/" . $nom_img_carte);
        $idOffre = $_GET["idOffre"];

        $stmt->execute();
    }
    
    if (isset($_FILES['plan']) && $_FILES['plan']['size'] > 0) {
        $requete = "UPDATE tripskell.offre_pro SET ";
        $requete .= "plans = :plans ";
        $requete .= "WHERE idOffre = :idOffre;";
        $stmt = $dbh->prepare($requete);
        
        $stmt->bindParam(":plans", $nom_img_plan);
        $stmt->bindParam(":idOffre", $idOffre);

        $nom_img_plan = time() + $i++ . "." . explode("/", $_FILES['plan']["type"])[1];
        move_uploaded_file($_FILES['plan']["tmp_name"], "../images/imagesPlan/" . $nom_img_plan);
        $idOffre = $_GET["idOffre"];
        
        $stmt->execute();
    }
}


if (!empty($_POST)) {

    $nom_img = null;

    // Traitement de l'image si elle est envoyée
    if (!empty($_FILES['fichier1']) && $_FILES['fichier1']['size'] > 0) {
        $nom_img = time() . "." . explode("/", $_FILES['fichier1']['type'])[1];
<<<<<<< Updated upstream
        move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/imagesOffres/" . $nom_img);
=======
        move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/pdp/" . $nom_img);
>>>>>>> Stashed changes
    }

    // Préparation de la requête de mise à jour de l'offre
    $requete = "UPDATE tripskell.offre_pro SET ";
    $requete .= "titreOffre = :titre, ";
    $requete .= "resume = :resume, ";
    $requete .= "description_detaille = :description, ";
    $requete .= "tarifMinimal = :tarif, ";
    $requete .= "accessibilite = :accessibilite, ";
    $requete .= "numero = :numero, ";
    $requete .= "rue = :rue, ";
    $requete .= "ville = :ville, ";
    $requete .= "codePostal = :codePostal, ";

    $requete .= "gammeprix = :gammeprix,";   // Pour restauration

    $requete .= "duree_v = :duree_v,";   // Pour visite
    $requete .= "guidee = :guidee,"; 

    $requete .= "duree_s = :duree_s,";   // Pour spectacle
    $requete .= "capacite = :capacite,"; 

    $requete .= "nbattraction = :nbattraction,";   // Pour parcattraction
    $requete .= "agemin = :agemin,";

    $requete .= "duree_a = :duree_a,";  // Pour activité
    $requete .= "ageminimum = :ageminimum,"; 
    $requete .= "prestation = :prestation";
    
    // Ajout de la colonne img1 seulement si une image est téléchargée
    if ($nom_img !== null) {
        $requete .= ", img1 = :img1"; 
    }

    $requete .= " WHERE idOffre = :idOffre;";

    // Préparation de la requête
    $stmt = $dbh->prepare($requete);

    // Liaison des paramètres de la requête
    $stmt->bindParam(":titre", $titre);
    $stmt->bindParam(":resume", $resume);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":tarif", $tarif);
    $stmt->bindParam(":accessibilite", $accessible);
    $stmt->bindParam(":numero", $numero);
    $stmt->bindParam(":rue", $rue);
    $stmt->bindParam(":ville", $ville);
    $stmt->bindParam(":codePostal", $codePostal);
    
    $stmt->bindParam(":gammeprix", $gammeprix);

    $stmt->bindParam(":duree_v", $duree_v);
    $stmt->bindParam(":guidee", $guidee);

    $stmt->bindParam(":duree_s", $duree_s);
    $stmt->bindParam(":capacite", $capacite);

    $stmt->bindParam(":nbattraction", $nbattraction);
    $stmt->bindParam(":agemin", $agemin);

    $stmt->bindParam(":duree_a", $duree_a);
    $stmt->bindParam(":ageminimum", $ageminimum);
    $stmt->bindParam(":prestation", $prestation);

    $stmt->bindParam(":idOffre", $idOffre);

    // Lier l'image si elle est présente
    if ($nom_img !== null) {
        $stmt->bindParam(":img1", $nom_img);
    }

    // Récupération des données du formulaire
    $titre = $_POST["titre"];
    $resume = $_POST["resume"];
    $description = $_POST["description"];
    $tarif = $_POST["prix-minimal"];
    $heuresDebut = $_POST["heure-debut"];
    $heuresFin = $_POST["heure-fin"];
    $accessible = $_POST["choixAccessible"];
    $numero = $_POST["num"];
    $rue = $_POST["nomRue"];
    $ville = $_POST["ville"];
    $codePostal = $_POST["codePostal"];

    $gammeprix = $_POST['gammeprix'];

    $duree_v = (!empty($_POST['duree_v']) ? $_POST['duree_v'] : null);
    $guidee = $_POST['guidee'];

    $duree_s = (!empty($_POST['duree_s']) ? $_POST['duree_s'] : null);
    $capacite = (!empty($_POST['capacite']) ? $_POST['capacite'] : null);

    $nbattraction = $_POST['nbAttraction'];
    $agemin = $_POST['ageminimum'];

    $duree_a = (!empty($_POST['duree_a']) ? $_POST['duree_a'] : null);
    $ageminimum = $_POST['agemin'];
    $prestation = $_POST['prestation'];

    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Exécution de la mise à jour
    $stmt->execute();


<<<<<<< Updated upstream
    // Mise à jour des Tags
    $requete = "delete from tripskell.  _possede where idOffre=:idOffre";

    // Préparation de la requête
    $stmt = $dbh->prepare($requete);

    // Exécution de la mise à jour
    $stmt->execute([':idOffre' => $_GET['idOffre']]);

    foreach (array_column($liste_tags, "nomtag") as $tag) {
        if (in_array($tag, array_values($_POST))) {
            // Mise à jour des Tags
            $requete = "insert into tripskell._possede (idOffre, nomtag) values (:idOffre, :nomtag)";

            // Préparation de la requête
            $stmt = $dbh->prepare($requete);

            // Exécution de la mise à jour
            $stmt->execute([':idOffre' => $_GET['idOffre'], ':nomtag' => $tag]);
        }
    }

    

=======
>>>>>>> Stashed changes
    /* -------------------------------- modifs horaires dans l'offre -------------------------------- */


    //récupère les horaires des jours à partir de $_POST, qui avaient été transformées en string avec json
    $jours = ["Lundi" => json_decode($_POST['lundi']),
            "Mardi"=> json_decode($_POST["mardi"]),
            "Mercredi"=> json_decode($_POST["mercredi"]),
            "Jeudi"=> json_decode($_POST["jeudi"]),
            "Vendredi"=> json_decode($_POST["vendredi"]),
            "Samedi"=> json_decode($_POST["samedi"]),
            "Dimanche"=> json_decode($_POST["dimanche"])];


    foreach ($jours as $jour => $horaires)      //pour chaque jour
    {
        // Remplacez les valeurs vides par NULL
        $debMatin = !empty($horaires[0]) ? $horaires[0] : null;
        $finMatin = !empty($horaires[1]) ? $horaires[1] : null;
        $debAprem = !empty($horaires[2]) ? $horaires[2] : null;
        $finAprem = !empty($horaires[3]) ? $horaires[3] : null;


        if($debMatin != null && $finMatin != null)      //si le jour est ouvert
        {
            $query = "SELECT * from tripskell._ouverture where idoffre = :idOffre and id_jour = :id_jour";      //les horaires du jour id_jour pour l'offre idOffre
            $stmt = $dbh->prepare($query);

            $stmt->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
            $stmt->bindValue(':id_jour', $jour, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll();

            foreach ($result as $row)       //remplace les anciennes données par celles renvoyées après la modif de l'offre
            {
                $query =    "UPDATE tripskell._horaire
                            SET horaire_matin_debut = :debMatin, horaire_matin_fin = :finMatin, horaire_aprem_debut = :debAprem, horaire_aprem_fin = :finAprem
                            WHERE id_hor = :id_hor ;";

                $stmt = $dbh->prepare($query);


                // Lier les variables aux paramètres
                $stmt->bindValue(':debMatin', $debMatin, $debMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':finMatin', $finMatin, $finMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':debAprem', $debAprem, $debAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':finAprem', $finAprem, $finAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_STR);

                $stmt->execute();
            }
        }
    }

/* --------------------------------------------------------------------------------------------- */

    if(isset($_POST['lang'])) {
        foreach ($langues as $langue) {
            // permet de savoir si la langue n'est pas deja dans la BDD
            $query = "select nomlangue from tripskell._possedelangue where idOffre='".$idOffre."' and nomlangue='".$langue."';";
            $stmt = $dbh->prepare($query);
            $stmt->execute();            
            $lang_pres = !isset($stmt->fetch()['nomlangue']);
            if(in_array($langue, $_POST['lang']) && $lang_pres)
            {
                $dbh->query("insert into tripskell._possedelangue(nomlangue, idOffre) values ('".$langue."','".$idOffre."');");
            }
            if(!in_array($langue, $_POST['lang']) && !$lang_pres) 
            {
                $dbh->query("delete from tripskell._possedelangue where nomlangue='".$langue."' and idOffre='".$idOffre."';");
            }
        }
    }
    

    
    // Redirection vers gestionOffres.php après la mise à jour réussie
    header("Location: ../pages/gestionOffres.php");
    exit(); // Terminer le script après la redirection pour éviter d'exécuter du code inutile
}

?>
<?php
if (in_array($_SESSION["idCompte"], $idproprive) || in_array($_SESSION["idCompte"], $idpropublic)) {
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification Offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/pages/CreaOffrePro.css">
</head>

<body class=<?php echo "fondPro"; ?>>                        

    <?php include "../composants/header/header.php";        //import navbar
    ?>

    <main>

        <div class="conteneur-formulaire">
            <h1>Modification d'une offre</h1>
            <form name="modification" action="/pages/modifOffre.php?idOffre=<?php echo $idOffre; ?>" method="post"  enctype="multipart/form-data">
                <div class="champs">
                    <label for="titre">Titre :</label>
                    <!-- Champ de saisie pour le titre avec valeur préremplie -->
                    <input type="text" id="titre" name="titre" value="<?php echo $contentOffre["titreoffre"];?>"   required>
                </div>
<<<<<<< Updated upstream
=======
            
>>>>>>> Stashed changes
                 <!-- Champs pour sélectionner les images -->
        <div class="champs">
            <div class="champ_Img">
                <div class = "pdp_champs">
                    <label for="img1">Votre image actuelle :</label>
                    <div class="image-container">
                        <img src="../images/imagesOffres/<?php echo $contentOffre["img1"]?>" alt="Image de l'offre" title="Image de l'offre">
                    </div>
                </div>
                <div class="Input_Img">
                    <label for="fichier1">Modifier l'image de l'offre :</label>
                    <input type="file" id="fichier1" name="fichier1" accept="image/png, image/jpeg" onchange="updateFileName()" >
                    <span id="fileName" class="file-name"></span> <!-- Zone pour afficher le nom -->
                </div>
            </div>
        </div>
        
            <!--------------------- > CATEGORIES < --------------------->

            <!-- ----------------- VISITE ------------------- -->

            <div id="champsVisite">
                <div class="champs">
                    <label for="duree_v">Duree de la visite :</label>
                    <input type="time" id="duree_v" name="duree_v" value="<?php echo substr($contentOffre["duree_v"], 0, 5); ?>"/>
                </div>
                <label>Langue(s) de la visite :</label>
                <div class="parentVisite">
                <?php
                foreach ($langues as $langue) {?>
                    <div>
                        <input type="checkbox" id="lang" name="lang[]" value="<?php echo $langue; ?>" <?php echo in_array($langue, $langue_preselec) ? 'checked' : ''; ?>/>
                        <label for="lang"><?php echo $langue; ?></label>
                    </div>
                <?php }?>
                </div>
                <label>La visite est guidée :<span class="required">*</span> :</label>
                <div class="parentVisite">
                    <div>
                        <input type="radio" id="guidee" name="guidee" value="true" <?php echo $contentOffre["guidee"] ? 'checked' : ''; ?>/>
                        <label for="guidePresent">Oui</label>
                    </div>
                    <div>
                        <input type="radio" id="guidee" name="guidee" value="false" <?php echo !$contentOffre["guidee"] ? 'checked' : ''; ?>/> <!-- a enlever et utilisation de checkbox -->
                        <label for="guidePasPresent">Non</label>
                    </div>
                </div>
            </div>

            <!-- ----------------- RESTAURATION ------------------- -->

            <div id="champsRestauration">
                <div class="champs">
                    <label for="carte">Selectionner la nouvelle carte :</label>
                    <input type="file" id="carte" name="carte">
                </div>
                <div class="champs">
                    <label for="gammeprix">Gamme de Prix :</label>
                    <select id="gammeprix" name="gammeprix">
                        <option value="$"<?php echo $contentOffre["gammeprix"]=='$' ? 'selected' : ''; ?>>$</option>
                        <option value="$$"<?php echo $contentOffre["gammeprix"]=='$$' ? 'selected' : ''; ?>>$$</option>
                        <option value="$$$"<?php echo $contentOffre["gammeprix"]=='$$$' ? 'selected' : ''; ?>>$$$</option>
                    </select>
                </div>
            </div>

            <!-- ----------------- PARC ATTRACTION ------------------- -->

            <div id="champsPA">
                <div class="champs">
                    <label for="nbAttraction">Nombre Attraction :</label>
                    <input type="text" id="nbAttraction" name="nbAttraction" placeholder="Entrez le nombre d'attraction" minlength="1" maxlength="3" value="<?php echo $contentOffre["nbattraction"]; ?>">
                </div>
                <div class="champs">
                    <label for="ageminimum">âge minimum :</label>
                    <input type="text" id="ageminimum" name="ageminimum" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3" value="<?php echo $contentOffre["agemin"]; ?>">
                </div>
                <div class="champs">
                    <label for="plan">Selectionner un plan :</label>
                    <input type="file" id="plan" name="plan">
                </div>
            </div>

            <!-- ----------------- SPECTACLE ------------------- -->

            <div id="champsSpectacle">
                <div class="champs">
                    <label for="duree_s">Duree de la Spectacle :</label>
                    <input type="time" id="duree_s" name="duree_s" value="<?php echo substr($contentOffre["duree_s"], 0, 5); ?>"/>
                </div>
                <div class="champs">
                    <label for="capacite">Capacité :</label>
                    <input type="text" id="capacite" name="capacite" placeholder="Entrez la capacite" value="<?php echo $contentOffre["capacite"]; ?>">
                </div>
            </div>

            <!-- ----------------- ACTIVITE ------------------- -->

            <div id="champsActivite">
                <div>
                    <label for="prestation">Prestation proposer :</label>
                    <textarea id="prestation" name="prestation" placeholder="Écrivez les prestations proposer (> 100 caractères)" maxlength="100"><?php echo $contentOffre["prestation"]; ?></textarea>
                </div>
                <div class="champs">
                    <label for="duree_a">Duree de l'Activité :</label>
                    <input type="time" id="duree_a" name="duree_a" value="<?php echo substr($contentOffre["duree_a"], 0, 5); ?>"/>
                </div>
                <div class="champs">
                    <label for="agemin">âge minimum :</label>
                    <input type="text" id="agemin" name="agemin" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3" value="<?php echo $contentOffre["ageminimum"]; ?>">
                </div>
            </div>

                <!-- ----------------- TAGS ------------------- -->
                <?php
                    
                    $tags_cat = ['Visite','Restauration','PA','Spectacle','Activite'];
                    
                    foreach ($tags_cat as $cat) {
                        
?>
                        <div id="tags<?php echo $cat; ?>">
                            <label>Tags :</label>
                            <div class="tags">
<?php
                            foreach (array_column($liste_tags, "nomtag") as $key => $tag) {
?>
                                <div>
                                    <input type="checkbox" id="<?php echo $tag; ?>" name="<?php echo $tag; ?>" value="<?php echo $tag; ?>" <?php echo in_array($tag,$liste_tags_preselec)?'checked':''; ?>/>
                                    <label for="<?php echo $tag; ?>"><?php echo $tag; ?></label>
                                </div>
<?php
                            }
?>
                            </div>
                        </div>
<?php
                    }
?>

                <div class="champs">
                    <label for="prix-minimal">Prix minimal (euro) :</label>
                    <!-- Champ de saisie pour le prix minimal avec valeur préremplie -->
                    <input type="text" id="prix-minimal" name="prix-minimal" value="<?php echo $contentOffre["tarifminimal"];?>">
                </div>

                <div>
                    <label for="resume">Résumé :</label>
                     <!-- Champ de saisie pour le résumé avec valeur préremplie -->
                    <textarea id="resume" name="resume"  required><?php echo $contentOffre["resume"];?></textarea>
                </div>

                <div>
                    <label for="description">Description détaillée :</label>
                     <!-- Champ de saisie pour la description détaillée avec valeur préremplie -->
                    <textarea id="description" name="description"  required><?php echo $contentOffre["description_detaille"];?></textarea>
                </div>

                <div>
                <label for="horaires">Horaires d'ouverture :</label>
                <?php       //préremplis les champs cachés des jours avec les horaires de la base de données
                    $ouverture = $dbh->query("select * from tripskell._ouverture where idoffre='" . $idOffre . "';")->fetchAll();
                    $tabJours = [];
                    foreach ($ouverture as $key => $value) {
                        $horaire = $dbh -> query("select * from tripskell._horaire as h join tripskell._ouverture as o on h.id_hor=". $ouverture[$key]["id_hor"] ." where o.idOffre='" . $idOffre . "' and o.id_hor=". $ouverture[$key]["id_hor"] ." and o.id_jour='". $ouverture[$key]["id_jour"] ."';")->fetchAll()[0];
                        
                        $tabJours[$horaire['id_jour']] = json_encode([$horaire["horaire_matin_debut"], $horaire["horaire_matin_fin"], 
                        $horaire["horaire_aprem_debut"], $horaire["horaire_aprem_fin"]]);
                    }
                ?>
                        <div class="jours">
                            <button type="button" id="btnL">L</button>
                            <input type="hidden" name="lundi" class="inputJour" <?php
                                if(array_key_exists("Lundi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Lundi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnMa">Ma</button>
                            <input type="hidden" name="mardi" class="inputJour" <?php
                                if(array_key_exists("Mardi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Mardi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnMe">Me</button>
                            <input type="hidden" name="mercredi" class="inputJour"
                            <?php
                                if(array_key_exists("Mercredi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Mercredi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnJ">J</button>
                            <input type="hidden" name="jeudi" class="inputJour"
                            <?php
                                if(array_key_exists("Jeudi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Jeudi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnV">V</button>
                            <input type="hidden" name="vendredi" class="inputJour"
                            <?php
                                if(array_key_exists("Vendredi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Vendredi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnS">S</button>
                            <input type="hidden" name="samedi" class="inputJour"
                            <?php
                                if(array_key_exists("Samedi", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Samedi"]; ?>'
                                    <?php
                                }
                            ?>>
                            <button type="button" id="btnD">D</button>
                            <input type="hidden" name="dimanche" class="inputJour"
                            <?php
                                if(array_key_exists("Dimanche", $tabJours))
                                {
                                    ?>
                                        value='<?php echo $tabJours["Dimanche"]; ?>'
                                    <?php
                                }
                            ?>>
                        </div>
                        <div class="heures" id="heures1">
                            <label for="heure-debut">Le <span id="nomJour1"></span>, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="heure-debut">
                            <label for="heure-fin">à</label>
                            <input type="time" class="heure-fin" name="heure-fin">

                            <h4 id="btnAjoutHoraire">+</h4>

                        </div>

                        <div class="heures" id="heures2">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="heure-debut">
                            <label for="heure-fin">à</label>
                            <input type="time" class="heure-fin" name="heure-fin">
                        </div>
                </div>

                <div class="champsAdresse">
                    <label for="adresse">Adresse :</label>
                     <!-- Champs de saisie pour l'adresse avec valeurs préremplies -->
                    <input type="text" id="num" name="num" value="<?php echo $contentOffre["numero"];?>">
                    <input type="text" id="nomRue" name="nomRue" value="<?php echo $contentOffre["rue"];?>" required>
                    <input type="text" id="ville" name="ville" value="<?php echo $contentOffre["ville"];?>" required>
                    <input type="text" id="codePostal" name="codePostal" value="<?php echo $contentOffre["codepostal"];?>" required>
                </div>
                
                <!--
                <div class="champs">
                    <label for="offre">Type offre :</label>
                    <select id="offre" name="offre">
                        <option value="Standard">Sélectionnez un type d'offre</option>
                        <option value="Standard">Standard</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>
                <div class="champs">
                    <label for="option">Option :</label>
                    <select id="option" name="option">
                        <option value="">Sélectionnez une option</option>
                        <option value="AlaUne">A la une</option>
                        <option value="EnRelief">En relief</option>
                        <option value="AlaUneEtEnRelief">A la une et En relief</option>
                    </select>
                </div>
-->
                <div class="champs">
                    <label for="choixAccessible">Accessibilité aux personnes à mobilité reduite :</label>
                    <select id="choixAccessible" name="choixAccessible">
                        <option value="Accessible" <?php echo ($contentOffre["accessibilite"] == "Accessible") ? 'selected' : ''; ?>>Accessible</option>
                        <option value="PasAccessible" <?php echo ($contentOffre["accessibilite"] == "PasAccessible") ? 'selected' : ''; ?>>Pas Accessible</option>
                    </select>
                </div>

                <!-- <div class="champs">
                    futur data de mise en ligne
                </div> -->

                <div class="zoneBtn">
                     <!-- Bouton pour annuler la modification -->
                    <a href="gestionOffres.php" class="btnAnnuler">
                        <p class="texteLarge boldArchivo">Annuler</p>
                        <?php
                        include '../icones/croixSVG.svg';
                        ?>
                    </a>
                     <!-- Bouton pour soumettre le formulaire -->
                    <button type="submit" href="gestionOffres.php" class="btnConfirmer">
                        <p class="texteLarge boldArchivo">Confirmer</p>
                        <?php
                        include '../icones/okSVG.svg';
                        ?>
                    </button>
                </div>

            </form>
        </div>
        <script src="/js/modifOffre.js"></script>
    </main>

    <?php
    include "../composants/footer/footer.php";
    ?>

</body>

</html>


<script>
function updateFileName() {
    const fileInput = document.getElementById('fichier1'); // Champ de fichier
    const fileName = document.getElementById('fileName'); // Zone où afficher le nom
    const label = document.getElementById('customFileLabel'); // Label du bouton

    if (fileInput.files.length > 0) {
        // Si un fichier est sélectionné, afficher son nom
        fileName.textContent = fileInput.files[0].name;
        label.textContent = "Changer la photo"; // Met à jour le texte du bouton
    } else {
        // Si aucun fichier n'est sélectionné
        fileName.textContent = "";
        label.textContent = "📷 Ajouter une photo de profil"; // Remet le texte original
    }
}
</script>

<?php
} else { // si id_c n'est pas dans pro_prive ou pro_public, on génère une erreur 404.
?>
        <!DOCTYPE html>
        <html lang="fr">
    
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Creation Offre</title>
            <link rel="stylesheet" href="/style/pages/CreaOffrePro.css">
        </head>
    
        <body class="fondPro">
    
            <?
             include "../composants/header/header.php";        //import navbar
            ?>
    
            <main>
                <h1> ERROR 404 </h1>
            </main>
    
            <?php
            include "../composants/footer/footer.php";
            ?>
<?php
    }



/* Fonction pour simuler le clic sur le champ de fichier
function selectFile(imageNumber) {
    document.getElementById(`fileInput${imageNumber}`).click();
}

// Gestionnaire d'événement pour prévisualiser l'image après sélection
document.getElementById("fileInput1").addEventListener("change", function(event) {
    updatePreview(event, "preview1");
});
document.getElementById("fileInput2").addEventListener("change", function(event) {
    updatePreview(event, "preview2");
});*/

    ?>