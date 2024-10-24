<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('/var/www/html/php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associatif

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('/var/www/html/php/verif_compte_pro.php');

if (!empty($_POST)) {
    // Requete pour Update
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
    $requete .= "WHERE idOffre = :idOffre;";

    $stmt = $dbh->prepare($requete);
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

    $titre = $_POST["titre"];
    $resume = $_POST["resume"];
    $description = $_POST["description"];
    $tarif = $_POST["prix-minimal"];
    $heuresDebut = $_POST["heure-debut"];
    $heuresFin = $_POST["heure-fin"];
    $horaires = $heuresDebut . "-" . $heuresFin;
    $accessible = $_POST["choixAccessible"];
    $numero = $_POST["num"];
    $rue = $_POST["nomRue"];
    $ville = $_POST["ville"];
    $codePostal = $_POST["codePostal"];
    $idOffre = $_GET["idOffre"];

    // Exécutez la mise à jour
    $stmt->execute();

    // Redirection vers gestionOffres.php après mise à jour réussie
    header("Location: /pages/gestionOffres.php");
    exit(); // Terminez le script après la redirection
}

// Code pour récupérer l'offre
$idOffre = null;
if (key_exists("idOffre", $_GET)) {
    // reccuperation de id de l offre
    $idOffre = $_GET["idOffre"];

    // reccuperation du contenu de l offre
    $contentOffre = $dbh->query("SELECT * FROM tripskell.offre_pro WHERE idOffre='" . $idOffre . "';")->fetchAll()[0];
}
?>


    $idOffre = null;
    if(key_exists("idOffre", $_GET))
    {
        // reccuperation de id de l offre
        $idOffre =$_GET["idOffre"]; 
        
        // reccuperation du contenu de l offre
        $contentOffre = $dbh->query("select * from tripskell.offre_pro where idoffre='" . $idOffre . "';")->fetchAll()[0];          
    }
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification Offre</title>
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
                    <input type="text" id="prix-minimal" name="prix-minimal" value="<?php echo $contentOffre["tarifminimal"];?>">
                </div>

                <div>
                    <label for="resume">Résumé <span class="required">*</span> :</label>
                    <textarea id="resume" name="resume"  required><?php echo $contentOffre["resume"];?></textarea>
                </div>

                <div>
                    <label for="description">Description détaillée <span class="required">*</span> :</label>
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
                        <input type="time" id="heure-debut" name="heure-debut" value="<?php echo implode(":",explode("h",explode("-",$contentOffre["horaires"])[0])); ?>">
                        <label for="heure-fin">à</label>
                        <input type="time" id="heure-fin" name="heure-fin" value="<?php echo implode(":",explode("h",explode("-",$contentOffre["horaires"])[1])); ?>">
                    </div>
                </div>

                <div class="champsAdresse">
                    <label for="adresse">Adresse <span class="required">*</span> :</label>
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
                    <a href="gestionOffres.php" class="btnAnnuler">
                        <p class="texteLarge boldArchivo">Annuler</p>
                        <?php
                        include '../icones/croixSVG.svg';
                        ?>
                    </a>
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
$dbh = null;