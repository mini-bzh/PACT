<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

// Inclusion du script pour vérifier si l'utilisateur a un compte pro
include('../php/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}



if (isset($_GET["idOffre"])) {
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // on cherche dans quelle catégorie est l'offre
    foreach(['visite', 'restauration', 'spectacle', 'parcattraction', 'activite'] as $nom_cat) {
        
        // requete pour chercher la categorie
        $stmt = $dbh->prepare("SELECT idoffre FROM tripskell._" . $nom_cat . " WHERE idOffre = :idOffre;");
        $stmt->execute([ ':idOffre' => $idOffre]);

        // Si l'offre appartient à une catégorie, on envoie la categorie au js
        if(isset($stmt->fetch()['idoffre'])){?>
            <script>
                let categorie_offre = '<?php echo $nom_cat; ?>';
            </script>
        <?php 

            // si c'est une visite on reccupère les langues
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
    
} else {
    die("Error");
}

if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];

    // reccuperation de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];
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

print_r($_POST);


if (!empty($_POST)) {
    
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

    $requete .= "gammeprix = :gammeprix,";   // pour restauration

    $requete .= "duree_v = :duree_v,";   // pour visite
    $requete .= "guidee = :guidee,"; 

    $requete .= "duree_s = :duree_s,";   // pour spectacle
    $requete .= "capacite = :capacite,"; 

    $requete .= "nbattraction = :nbattraction,";   // pour parcattraction
    $requete .= "agemin = :agemin,";

    $requete .= "duree_a = :duree_a,";  // pour activite
    $requete .= "ageminimum = :ageminimum,";
    $requete .= "prestation = :prestation";
    
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
    

    // Récupération des données du formulaire
    $titre = $_POST["titre"];
    $resume = $_POST["resume"];
    $description = $_POST["description"];
    $tarif = $_POST["prix-minimal"];
    $heuresDebut = $_POST["heure-debut"];
    $heuresFin = $_POST["heure-fin"];
    $horaires = $heuresDebut . "-" . $heuresFin; // Formatage des horaires
    $accessible = $_POST["choixAccessible"];
    $numero = $_POST["num"];
    $rue = $_POST["nomRue"];
    $ville = $_POST["ville"];
    $codePostal = $_POST["codePostal"];

    $gammeprix = $_POST['gammeprix'];

    $duree_v = (!empty($_POST['duree_v'])?$_POST['duree_v']:null);
    $guidee = $_POST['guidee'];

    $duree_s =  (!empty($_POST['duree_s'])?$_POST['duree_s']:null);
    $capacite = (!empty($_POST['capacite'])?$_POST['capacite']:null);

    $nbattraction = $_POST['nbAttraction'];
    $agemin = $_POST['ageminimum'];

    $duree_a =  (!empty($_POST['duree_a'])?$_POST['duree_a']:null);
    $ageminimum = $_POST['agemin'];
    $prestation = $_POST['prestation'];

    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Exécution de la mise à jour
    $stmt->execute();

    if(isset($_POST['lang'])) {
        foreach ($langues as $langue) {
            // permet de savoir si la langue n'est pas deja dans la BDD
            $lang_pres = is_null($dbh->query("select nomlangue from tripskell._possedelangue where idOffre=".$idOffre." and nomlangue='".$langue."';")->fetch()["nomlangue"]);
            if(in_array($langue, $_POST['lang']) && $lang_pres) {
                $dbh->query("insert into tripskell._possedelangue(nomlangue, idOffre) values ('".$langue."',".$idOffre.");");
            }
            if(!in_array($langue, $_POST['lang']) && !$lang_pres) {
                $dbh->query("delete from tripskell._possedelangue where nomlangue='".$langue."' and idOffre=".$idOffre.";");
            }
        }
    }
    

    
    // Redirection vers gestionOffres.php après la mise à jour réussie
    //header("Location: ../pages/gestionOffres.php");
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


                 <!-- Champs pour sélectionner les images -->

                 <?php
// Supposons que vous avez des colonnes 'image1', 'image2', 'image3', 'image4' dans `offre_pro`
// pour stocker les chemins d'accès aux images de l'offre. Assurez-vous que ces colonnes existent.
$image1 = $contentOffre["image1"] ?? null;
$image2 = $contentOffre["image2"] ?? null;
$image3 = $contentOffre["image3"] ?? null;
$image4 = $contentOffre["image4"] ?? null;
?>

            <!-- Champs pour sélectionner ou modifier les images avec aperçus préchargés -->
             <!--
            <div class="champs">
                <label for="fichier1">Sélectionner une image 1 :</label>
                <input type="file" id="fichier1" name="fichier1">
            </div>

            <div class="champs">
                <label for="fichier2">Sélectionner une image 2 :</label>
                <?php if ($image2): ?>
                    <img id="preview2" src="<?php echo $image2; ?>" alt="Image 2 actuelle" style="width: 100px; height: auto;">
                <?php endif; ?>
                <input type="file" id="fichier2" name="fichier2" onchange="updatePreview(event, 'preview2')">
            </div>

            <div class="champs">
                <label for="fichier3">Sélectionner une image 3 :</label>
                <?php if ($image3): ?>
                    <img id="preview3" src="<?php echo $image3; ?>" alt="Image 3 actuelle" style="width: 100px; height: auto;">
                <?php endif; ?>
                <input type="file" id="fichier3" name="fichier3" onchange="updatePreview(event, 'preview3')">
            </div>

            <div class="champs">
                <label for="fichier4">Sélectionner une image 4 :</label>
                <?php if ($image4): ?>
                    <img id="preview4" src="<?php echo $image4; ?>" alt="Image 4 actuelle" style="width: 100px; height: auto;">
                <?php endif; ?>
                <input type="file" id="fichier4" name="fichier4" onchange="updatePreview(event, 'preview4')">
            </div>
                -->
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
                <?php } ?>
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

                <!-- ----------------- TAGS ------------------- 
                <div class="champs">
                    <label for="tags">Tags :</label>
                    <select id="tags" name="tags">
                        <option value="">Sélectionnez des tags</option>
                        <option value="tag1">Tag 1</option>
                        <option value="tag2">Tag 2</option>
                    </select>
                </div> -->

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
                    <div class="jours">
                        <button type="button">L</button>
                        <button type="button">Ma</button>
                        <button type="button">Me</button>
                        <button type="button">J</button>
                        <button type="button">V</button>
                        <button type="button">S</button>
                        <button type="button">D</button>
                    </div>
                    <div class="heures">
                        <label for="heure-debut">De</label>
                        <!-- Champ de saisie pour l'heure de début avec valeur préremplie -->
                        <input type="time" id="heure-debut" name="heure-debut" value="">
                        <label for="heure-fin">à</label>
                        <!-- Champ de saisie pour l'heure de fin avec valeur préremplie -->
                        <input type="time" id="heure-fin" name="heure-fin" value="">
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
    ?>


// Fonction pour simuler le clic sur le champ de fichier
function selectFile(imageNumber) {
    document.getElementById(`fileInput${imageNumber}`).click();
}

// Gestionnaire d'événement pour prévisualiser l'image après sélection
document.getElementById("fileInput1").addEventListener("change", function(event) {
    updatePreview(event, "preview1");
});
document.getElementById("fileInput2").addEventListener("change", function(event) {
    updatePreview(event, "preview2");
});

