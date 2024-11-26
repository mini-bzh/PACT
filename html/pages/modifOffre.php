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

if (!empty($_POST)) {
    // Préparation de la requête de mise à jour de l'offre
    $requete = "UPDATE tripskell.offre_pro SET ";
    $requete .= "titreOffre = :titre, ";
    $requete .= "resume = :resume, ";
    $requete .= "description_detaille = :description, ";
    $requete .= "tarifMinimal = :tarif, ";
    $requete .= "horaires = :horaires, ";
    $requete .= "accessibilite = :accessibilite, ";
    $requete .= "numero = :numero, ";
    $requete .= "rue = :rue, ";
    $requete .= "ville = :ville, ";
    $requete .= "codePostal = :codePostal ";
    $requete .= "WHERE idOffre = :idOffre;"; // Condition pour mettre à jour l'offre spécifiée

    // Préparation de la requête
    $stmt = $dbh->prepare($requete);
    
    // Liaison des paramètres de la requête
    $stmt->bindParam(":titre", $titre);
    $stmt->bindParam(":resume", $resume);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":tarif", $tarif);
    $stmt->bindParam(":horaires", $horaires);
    $stmt->bindParam(":accessibilite", $accessible);
    $stmt->bindParam(":numero", $numero);
    $stmt->bindParam(":rue", $rue);
    $stmt->bindParam(":ville", $ville);
    $stmt->bindParam(":codePostal", $codePostal);
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
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Exécution de la mise à jour
    $stmt->execute();

    // Redirection vers gestionOffres.php après la mise à jour réussie
    header("Location: ../pages/gestionOffres.php");
    exit(); // Terminer le script après la redirection pour éviter d'exécuter du code inutile
}
if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];

    // reccuperation de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];
}


// Récupération de l'identifiant de l'offre si présent dans l'URL
$idOffre = null;
if (key_exists("idOffre", $_GET)) {
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Récupération des détails de l'offre à partir de la base de données
    $contentOffre = $dbh->query("SELECT * FROM tripskell.offre_pro WHERE idOffre='" . $idOffre . "';")->fetchAll()[0];
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
            <form name="modification" action="/pages/modifOffre.php?idOffre=<?php echo $idOffre; ?>" method="post">
                <div class="champs">
                    <label for="titre">Titre <span class="required">*</span> :</label>
                    <!-- Champ de saisie pour le titre avec valeur préremplie -->
                    <input type="text" id="titre" name="titre" value="<?php echo $contentOffre["titreoffre"];?>"   required>
                </div>

                <!-- <div class="champs">
                    <label for="categorie">Catégorie <span class="required">*</span> :</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="option1">Option 1</option>
                        <option value="option2">Option 2</option>
                    </select>
                </div>

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
                    <label for="resume">Résumé <span class="required">*</span> :</label>
                     <!-- Champ de saisie pour le résumé avec valeur préremplie -->
                    <textarea id="resume" name="resume"  required><?php echo $contentOffre["resume"];?></textarea>
                </div>

                <div>
                    <label for="description">Description détaillée <span class="required">*</span> :</label>
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
                        <input type="time" id="heure-debut" name="heure-debut" value="<?php echo implode(":",explode("h",explode("-",$contentOffre["horaires"])[0])); ?>">
                        <label for="heure-fin">à</label>
                        <!-- Champ de saisie pour l'heure de fin avec valeur préremplie -->
                        <input type="time" id="heure-fin" name="heure-fin" value="<?php echo implode(":",explode("h",explode("-",$contentOffre["horaires"])[1])); ?>">
                    </div>
                </div>

                <div class="champsAdresse">
                    <label for="adresse">Adresse <span class="required">*</span> :</label>
                     <!-- Champs de saisie pour l'adresse avec valeurs préremplies -->
                    <input type="text" id="num" name="num" value="<?php echo $contentOffre["numero"];?>" required>
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