<?php

use FontLib\Table\Type\post;

session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// Inclusion du script pour vérifier si l'utilisateur a un compte pro
include('../composants/verif/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}


// Récupération des langues
$stmt = $dbh->prepare("SELECT nomlangue FROM tripskell._langue;");
$stmt->execute();
$langues = array_column($stmt->fetchAll(), 'nomlangue');

// On va récupérer ici l'identifiant id_c présent dans les vues pro.
if (key_exists("idCompte", $_SESSION)) {
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
}



// $stock = false; // Stock est lié à la popup mais pour cause de soucis j'ai mit la popup de côté
?>
<?php

// requete pour avoir la liste des tags
$stmt = $dbh->prepare("select * from tripskell._tags");
$stmt->execute();
$liste_tags = $stmt->fetchAll();

// requete pour les informations banquaires
$stmt = $dbh->prepare("select coordonnee_bancaire, date_exp, cryptogramme, nom_titulaire_carte, addressmail_pp, mdp_pp, iban from tripskell.pro_prive where id_c=:idCompte");
$stmt->bindParam(':idCompte', $_SESSION["idCompte"]);
$stmt->execute();
$info_banq = $stmt->fetchAll()[0];

if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.

    // ici on exploite les fichier image afin de les envoyer dans un dossier du git dans le but de stocker les images reçus
    $i = 0;
    foreach ($_FILES as $key_fichier => $fichier) { // on parcour les fichiers de la super globale $_FILES

        $nom_img[$key_fichier] = null; // initialistion des noms des images a null

        if ($fichier["size"] != 0) {  // on verifie que le fichier a ete transmit

            // creation du nom de fichier en utilisant time et le type de fichier
            $nom_img[$key_fichier] = time() + $i++ . "." . explode("/", $_FILES[$key_fichier]["type"])[1];

            // deplacement du fichier depuis l'espace temporaire
            if (in_array($key_fichier, ["fichier1", "fichier2", "fichier3", "fichier4"])) {
                move_uploaded_file($fichier["tmp_name"], "../images/imagesOffres/" . $nom_img[$key_fichier]);
            }

            if (trim($key_fichier) == "carte") {
                move_uploaded_file($fichier["tmp_name"], "../images/imagesCarte/" . $nom_img[$key_fichier]);
            }

            if (trim($key_fichier) == "plan") {
                move_uploaded_file($fichier["tmp_name"], "../images/imagesPlan/" . $nom_img[$key_fichier]);
            }
        }
    }

    // on definie ici la requête INSERT. C'est une étape préparatoire avant d'insérer les valeurs dans la vue. 
    // requete va nous servir de variable de stock qui va concatener chaque partie de l'INSERT


    $requete = "INSERT INTO tripskell.offre_pro(";

    $requete .= "numero, ";
    $requete .= "rue, ";
    $requete .= "ville, ";
    $requete .= "codePostal,";

    $requete .= "titreOffre, ";
    $requete .= "resume, ";
    $requete .= "description_detaille, ";
    $requete .= "tarifminimal, ";
    $requete .= "accessibilite, ";

    $requete .= "id_c, ";

    $requete .= "img1, ";
    $requete .= "img2, ";
    $requete .= "img3, ";
    $requete .= "img4, ";

    $requete .= "id_abo,";

    $requete .= "carte,";
    $requete .= "gammeprix,";

    $requete .= "duree_s, ";
    $requete .= "capacite,";

    $requete .= "plans,";
    $requete .= "nbAttraction,";
    $requete .= "agemin,";

    $requete .= "duree_v,";
    $requete .= "guidee,";

    $requete .= "duree_a,";
    $requete .= "ageminimum,";
    $requete .= "prestation,";

    $requete .= "id_option,";

    $requete .= "datedebutsouscription,";
    $requete .= "datefinsouscription";

    $requete .= ")VALUES (";

    $requete .= ":numero,";
    $requete .= ":rue,";
    $requete .= ":ville,";
    $requete .= ":codePostal,";

    $requete .= ":titre,";
    $requete .= ":resume,";
    $requete .= ":description,";
    $requete .= ":tarif,";
    $requete .= ":accessibilite,";

    $requete .= ":id_c, ";

    $requete .= ":img1, ";
    $requete .= ":img2, ";
    $requete .= ":img3, ";
    $requete .= ":img4, ";

    $requete .= ":id_abo,";


    $requete .= ":carte,";
    $requete .= ":gammeprix,";

    $requete .= ":duree_s, ";
    $requete .= ":capacite,";

    $requete .= ":plans,";
    $requete .= ":nbattraction,";
    $requete .= ":agemin,";

    $requete .= ":duree_v,";
    $requete .= ":guidee,";

    $requete .= ":duree_a,";
    $requete .= ":ageminimum,";
    $requete .= ":prestation,";

    $requete .= ":id_option,";

    $requete .= ":datedebutsouscription,";
    $requete .= ":datefinsouscription::DATE + INTERVAL '";
    $requete .= $_POST["date_debut_opt"] !== "" ? $_POST["duree_opt"] : "0";
    $requete .= " week'";

    $requete .= ") returning idOffre;";
    // ici, on va éxecuter l'INSERT tout en assignant les variables correspondants à celle de la Vue
    $stmt = $dbh->prepare($requete);

    $stmt->bindParam(":numero", $_POST["num"]);
    $stmt->bindParam(":rue", $_POST["nomRue"]);
    $stmt->bindParam(":ville", $_POST["ville"]);
    $stmt->bindParam(":codePostal", $_POST["codePostal"]);

    $stmt->bindParam(":titre", $_POST["titre"]);
    $stmt->bindParam(":resume", $_POST["resume"]);
    $stmt->bindParam(":description", $_POST["description"]);
    $stmt->bindParam(":tarif", $tarif);
    $stmt->bindParam(":accessibilite", $_POST["choixAccessible"]);

    $stmt->bindParam(":id_c", $_SESSION["idCompte"]);

    $stmt->bindParam(":img1", $nom_img["fichier1"]);
    $stmt->bindParam(":img2", $nom_img["fichier2"]);
    $stmt->bindParam(":img3", $nom_img["fichier3"]);
    $stmt->bindParam(":img4", $nom_img["fichier4"]);

    $stmt->bindParam(":id_abo", $_POST["offre"]);


    $stmt->bindParam(":carte", $carte);
    $stmt->bindParam(":gammeprix", $gammeprix);

    $stmt->bindParam(":duree_s", $duree_s);
    $stmt->bindParam(":capacite", $capacite);

    $stmt->bindParam(":plans", $plans);
    $stmt->bindParam(":nbattraction", $nbattraction);
    $stmt->bindParam(":agemin", $agemin);

    $stmt->bindParam(":duree_v", $duree_v);
    $stmt->bindParam(":guidee", $guidee);

    $stmt->bindParam(":duree_a", $duree_a);
    $stmt->bindParam(":ageminimum", $ageminimum);
    $stmt->bindParam(":prestation", $prestation);

    $stmt->bindParam(":id_option", $idoption);

    $stmt->bindParam(":datedebutsouscription", $date_option);
    $stmt->bindParam(":datefinsouscription", $date_option);
    //$stmt->bindParam(":inter",$_POST["duree_opt"]);

    $date_option = $_POST["date_debut_opt"] !== "" ? $_POST["date_debut_opt"] : null;


    // On definit des variables a traiter
    $tarif = isset($_POST["prix-minimal"]) ? $_POST["prix-minimal"] : "0";


    // Traitement pour id_option
    if ($_POST["option"] == "AlaUne") {
        $idoption = "A la une";
    } elseif ($_POST["option"] == "EnRelief") {
        $idoption = "En relief";
    } else {
        $idoption = null;
    }

    // Traitement pour les categories
    $carte = $nom_img['carte'];
    $gammeprix   = $_POST["categorie"] == "restauration" ? $_POST["gammeprix"] : null;

    $duree_s     = $_POST["categorie"] == "spectacle" ? $_POST["duree_s"] : null;
    $capacite    = $_POST["categorie"] == "spectacle" ? $_POST["capacite"] : null;

    $plans    = $nom_img['plan'];
    $nbattraction = $_POST["categorie"] == "parcDattraction" ? $_POST["nbAttraction"] : null;
    $agemin   = $_POST["categorie"] == "parcDattraction" ? $_POST["ageminimum"] : null;

    $duree_v     = $_POST["categorie"] == "visite" ? $_POST["duree_v"] : null;
    $guidee      = $_POST["categorie"] == "visite" ? $_POST["guidee"] : null;

    $duree_a    = $_POST["categorie"] == "activite" ? $_POST["duree_a"] : null;
    $ageminimum  = $_POST["categorie"] == "activite" ? $_POST["agemin"] : null;
    $prestation  = $_POST["categorie"] == "activite" ? $_POST["prestation"] : null;


    // on execute tout ce qui a été fait précèdement
    $stmt->execute();

    //$idOffre = $stmt;

    // requete pour avoir l'offre qui vient d'être creé
    $stmt = $dbh->prepare("select max(idOffre) from tripskell.offre_pro");
    $stmt->execute();
    $idOffre = $stmt->fetchAll()[0]["max"];



    //requete pour l'insersion des tags

    $requete = "INSERT INTO tripskell._possede(";
    $requete .= "idOffre, ";
    $requete .= "nomTag";
    $requete .= ") VALUES (";
    $requete .= ":idOffre, ";
    $requete .= ":nomTag";
    $requete .= ");";

    //parcours de tous les tags
    foreach ($liste_tags as $tag) {

        // quand un des tags a été selectionné on le rajoute
        if (isset($_POST[$tag["nomtag"]])) {
            $stmt = $dbh->prepare($requete);
            $stmt->bindparam(":idOffre", $idOffre);
            $stmt->bindparam(":nomTag", $tag["nomtag"]);
            $stmt->execute();
        }
    }

    // requete pour l'insersion des donnees banquaires
    $keysToCheck = ["cb", "DE", "crypto", "TC", "AdM_PP", "MDP_PP", "iban"];


    // Vérifie si au moins un des champs est présent dans $_POST
    $hasFieldPresent = false;

    foreach ($keysToCheck as $key) {
        if (!empty($_POST[$key])) { // Vérifie si le champ est présent (même s'il est vide)
            $hasFieldPresent = true;
            break; // Pas besoin de continuer, on a trouvé un champ présent
        }
    }
    if (!is_null($idproprive) && $hasFieldPresent) {
        $requete = "update tripskell.pro_prive set ";
        $requete .= "coordonnee_bancaire = :coordonnee_bancaire, date_exp = :date_exp, cryptogramme = :cryptogramme, nom_titulaire_carte = :nom_titulaire_carte,";
        $requete .= "addressmail_pp = :addressmail_pp, mdp_pp = :mdp_pp,";
        $requete .= "iban = :iban";
        $requete .= " where id_c = " . $_SESSION["idCompte"] . ";";

        $stmt = $dbh->prepare($requete);

        $stmt->bindParam(":coordonnee_bancaire", $_POST["cb"]);
        $stmt->bindParam(":date_exp", $_POST["DE"]);
        $stmt->bindParam(":cryptogramme", $_POST["crypto"]);
        $stmt->bindParam(":nom_titulaire_carte", $_POST["TC"]);
        $stmt->bindParam(":addressmail_pp", $_POST["AdM_PP"]);
        $stmt->bindParam(":mdp_pp", $_POST["MDP_PP"]);
        $stmt->bindParam(":iban", $_POST["iban"]);

        $stmt->execute();
    }


    /* -------------------------------- ajout horaires dans l'offre -------------------------------- */


    //récupère les horaires des jours à partir de $_POST, qui avaient été transformées en string avec json
    $jours = [
        "Lundi" => [
            "debut-matin" => $_POST['debut-matin-L'],
            "fin-matin" => $_POST['fin-matin-L'],
            "debut-aprem" => $_POST['debut-aprem-L'],
            "fin-aprem" => $_POST['fin-aprem-L']
        ],
        "Mardi" => [
            "debut-matin" => $_POST['debut-matin-Ma'],
            "fin-matin" => $_POST['fin-matin-Ma'],
            "debut-aprem" => $_POST['debut-aprem-Ma'],
            "fin-aprem" => $_POST['fin-aprem-Ma']
        ],
        "Mercredi" => [
            "debut-matin" => $_POST['debut-matin-Me'],
            "fin-matin" => $_POST['fin-matin-Me'],
            "debut-aprem" => $_POST['debut-aprem-Me'],
            "fin-aprem" => $_POST['fin-aprem-Me']
        ],
        "Jeudi" => [
            "debut-matin" => $_POST['debut-matin-J'],
            "fin-matin" => $_POST['fin-matin-J'],
            "debut-aprem" => $_POST['debut-aprem-J'],
            "fin-aprem" => $_POST['fin-aprem-J']
        ],
        "Vendredi" => [
            "debut-matin" => $_POST['debut-matin-V'],
            "fin-matin" => $_POST['fin-matin-V'],
            "debut-aprem" => $_POST['debut-aprem-V'],
            "fin-aprem" => $_POST['fin-aprem-V']
        ],
        "Samedi" => [
            "debut-matin" => $_POST['debut-matin-S'],
            "fin-matin" => $_POST['fin-matin-S'],
            "debut-aprem" => $_POST['debut-aprem-S'],
            "fin-aprem" => $_POST['fin-aprem-S']
        ],
        "Dimanche" => [
            "debut-matin" => $_POST['debut-matin-D'],
            "fin-matin" => $_POST['fin-matin-D'],
            "debut-aprem" => $_POST['debut-aprem-D'],
            "fin-aprem" => $_POST['fin-aprem-D']
        ]
    ];


    foreach ($jours as $jour => $horaires) {

        // Remplace les valeurs vides par NULL
        $debMatin = !empty($horaires["debut-matin"]) ? $horaires["debut-matin"] : null;
        $finMatin = !empty($horaires["fin-matin"]) ? $horaires["fin-matin"] : null;
        $debAprem = !empty($horaires["debut-aprem"]) ? $horaires["debut-aprem"] : null;
        $finAprem = !empty($horaires["fin-aprem"]) ? $horaires["fin-aprem"] : null;

        if ($debMatin !== null && $finMatin !== null) {  // Si le jour est ouvert
            $query = "SELECT tripskell.add_horaire(:idOffre, :debMatin, :finMatin, :debAprem, :finAprem, :jour);";
            $stmt = $dbh->prepare($query);

            // Lier les variables aux paramètres
            $stmt->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
            $stmt->bindValue(':debMatin', $debMatin, PDO::PARAM_STR);
            $stmt->bindValue(':finMatin', $finMatin, PDO::PARAM_STR);
            $stmt->bindValue(':debAprem', $debAprem, $debAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':finAprem', $finAprem, $finAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(':jour', $jour, PDO::PARAM_STR);

            $stmt->execute();
        }
    }



    /* --------------------------------------------------------------------------------------------- */

    // on ferme la base de donnée
    $dbh = null;
    header("Location: /pages/gestionOffres.php"); // on redirige vers la page de l'offre créée
}
?>

<?php
if (!is_null($idproprive) || !is_null($idpropublic)) {
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Creation Offre</title>

        <!-- Favicon -->
        <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="/style/style.css">
        <link rel="stylesheet" href="/style/composants/sidebar.css">
        <link rel="stylesheet" href="/style/pages/Formulaire.css">


    </head>
    <?php include "../composants/header/header.php";        //import navbar
    ?>

    <body class="fondPro">

        <?php
        if (array_key_exists("idOffre", $_SESSION)) {
        ?>
            <p id="idOffre"><?php echo $idOffre; ?></p>
        <?php
        }
        ?>
        <div class=FirstSentence>
            <p>Les champs qui possèdent une </p>
            <div class="Asterisque"> * </div>
            <p>sont obligatoires.</p>
        </div>
        <!-- Formulaire de création d'offre -->

        <form id="formCreaOffre" name="creation" action="/pages/CreaOffrePro.php" method="post" enctype="multipart/form-data">
            <p class="titreFrom">Creation d'une offre</p>
            <div class="InfoPerso">
                <!-- titre -->
                <div class="champs champsValid">
                    <label for="titre">Titre <span class="required">*</span> :</label>
                    <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'offre" maxlength="40" required>
                </div>

                <!-- Champs pour sélectionner les images -->
                <div class="champs champsValid">
                    <div class="PhotoOffre">
                        <img id="previewImage" src="../images/logo/ajoutimage.png"
                            alt="Cliquez pour ajouter une image"
                            style="cursor: pointer;"
                            onclick="document.getElementById('fichier1').click()">
                        <input type="file" id="fichier1" name="fichier1"
                            accept="image/png, image/jpeg"
                            style="display: none;"
                            onchange="updatePreview()"
                            required>
                    </div>
                </div>
            </div>


            <!-- 
                    ------------------------------------   ___ __ _| |_ ___  __ _  ___  _ __(_) ___  ___ --------------------------------------- 
                    ------------------------------------ / __/ _` | __/ _ \/ _` |/ _ \| '__| |/ _ \/ __| ---------------------------------------
                    ------------------------------------| (_| (_| | ||  __/ (_| | (_) | |  | |  __/\__  ---------------------------------------
                    ------------------------------------ \___\__,_|\__\___|\__, |\___/|_|  |_|\___||___/ ---------------------------------------
                    ------------------------------------                    |___/                        ---------------------------------------
                     -->
            <div class="InfoPerso">
                <div class="champs">
                    <label for="categorie">Catégorie <span class="required">*</span> :</label>
                    <select id="categorie" name="categorie" required>
                        <option value="">Sélectionnez une catégorie</option>
                        <option value="activite">Activité</option>
                        <option value="visite">Visite</option>
                        <option value="parcDattraction">Parc d'attraction</option>
                        <option value="spectacle">Spectacle</option>
                        <option value="restauration">Restauration</option>
                    </select>
                </div>

                <!-- prix minimum -->
                <div class="champs champsValid">
                    <label for="prix-minimal">Prix minimal (euro) :</label>
                    <input type="text" id="prix-minimal" name="prix-minimal" placeholder="Entrez le prix minimal (euro)" minlength="1" maxlength="3" required>
                </div>
            </div>

            <!-- ----------------- VISITE ------------------- -->

            <div id="champsVisite">

                <div class="zoneChoixVisite">
                    <div class="champs dureeVisite">
                        <label for="duree_v">Durée de la visite :</label>
                        <input type="time" id="duree_v" name="duree_v" value="<?php echo substr($contentOffre["duree_v"], 0, 5); ?>" />
                    </div>
                    <div class="champsCategorie">
                        <label>Langue(s) de la visite :</label>
                        <div class="parentVisite">
                            <?php
                            foreach ($langues as $langue) { ?>
                                <div>
                                    <label class="toggle-button">
                                        <input type="checkbox" name="<?php echo $tag; ?>" value="<?php echo $langue; ?>" />
                                        <span><?php echo $langue; ?></span>
                                    </label>
                                </div>

                            <?php } ?>
                        </div>
                    </div>

                    <div class="champsCategorie">
                        <label>La visite est guidée <span class="required">*</span> :</label>
                        <div class="parentVisite">
                            <div>
                                <label class="toggle-button">
                                    <input type="radio" id="guidee" name="guidee" value="true" />
                                    <span>Oui</span>
                                </label>
                            </div>
                            <div>
                                <label class="toggle-button">
                                    <input type="radio" id="guidee" name="guidee" value="false" /> <!-- a enlever et utilisation de checkbox -->
                                    <span>Non</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ----------------- RESTAURATION ------------------- -->

            <div id="champsRestauration">
                <!-- Champs pour sélectionner les images -->
                <div class="imgRestorant">
                    <label for="plan">Selectionner une carte :</label>
                    <div class="champs">
                        <div class="carteResto">
                            <img id="previewImageCarte" src="../images/logo/ajoutimage.png"
                                alt="Cliquez pour ajouter une image"
                                style="cursor: pointer;"
                                onclick="document.getElementById('carte').click()">
                            <input type="file" id="carte" name="carte"
                                accept="image/png, image/jpeg"
                                style="display: none;"
                                onchange="updatePreviewCarte()">
                        </div>
                    </div>
                </div>
                <div class="champs champsGammeprix">
                    <label for="gammeprix">Gamme de Prix <span class="required">*</span> :</label>
                    <select id="gammeprix" name="gammeprix">
                        <option value="$">$</option>
                        <option value="$$">$$</option>
                        <option value="$$$">$$$</option>
                    </select>
                </div>
            </div>

            <!-- ----------------- PARC ATTRACTION ------------------- -->

            <div id="champsPA">
                <div class="InfoPerso">
                    <div class="champs">
                        <label for="nbAttraction">Nombre Attraction :</label>
                        <input type="text" id="nbAttraction" name="nbAttraction" placeholder="Entrez le nombre d'attraction" minlength="1" maxlength="3">
                    </div>
                    <div class="champs">
                        <label for="ageminimum">âge minimum :</label>
                        <input type="text" id="ageminimum" name="ageminimum" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3">
                    </div>
                </div>
                <div class="imgAttraction">
                    <label for="plan">Selectionner un plan :</label>
                    <!-- Champs pour sélectionner les images -->
                    <div class="PlanAttraction">
                        <img id="previewImageAttraction" src="../images/logo/ajoutimage.png"
                            alt="Cliquez pour ajouter une image"
                            style="cursor: pointer;"
                            onclick="document.getElementById('plan').click()">
                        <input type="file" id="plan" name="plan"
                            accept="image/png, image/jpeg"
                            style="display: none;"
                            onchange="updatePreviewAttraction()">
                    </div>
                </div>
            </div>

            <!-- ----------------- SPECTACLE ------------------- -->

            <div id="champsSpectacle">
                <div class="InfoPerso">
                    <div class="champs">
                        <label for="duree_s">Durée du spectacle <span class="required">*</span> :</label>
                        <input type="time" id="duree_s" name="duree_s" />
                    </div>
                    <div class="champs">
                        <label for="capacite">Capacité :</label>
                        <input type="text" id="capacite" name="capacite" placeholder="Entrez la capacite">
                    </div>
                </div>
            </div>

            <!-- ----------------- ACTIVITE ------------------- -->

            <div id="champsActivite">
                <div class="texteAreaActivite">
                    <label for="prestation">Prestation(s) proposée(s) <span class="required">*</span> :</label>
                    <textarea id="prestation" name="prestation" placeholder="Écrivez la/les prestation(s) proposée(s) (< 100 caractères)" maxlength="100"></textarea>
                </div>
                <div class="InfoPerso">
                    <div class="champs">
                        <label for="duree_a">Durée de l'Activité <span class="required">*</span> :</label>
                        <input type="time" id="duree_a" name="duree_a" />
                    </div>
                    <div class="champs">
                        <label for="agemin">Âge minimum :</label>
                        <input type="text" id="agemin" name="agemin" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3">
                    </div>
                </div>
            </div>

                    <!---------------------------------------- | |_ __ _  __ _  -------------------------------------
                        -------------------------------------- | __/ _` |/ _` | -------------------------------------
                        -------------------------------------- | || (_| | (_| | -------------------------------------
                        -------------------------------------- \__\__,_|\__,  | -------------------------------------
                        --------------------------------------           |___/  -------------------------------------
                    -->
            <?php
            $tags_cat = ['Visite', 'Restauration', 'PA', 'Spectacle', 'Activite'];

            foreach ($tags_cat as $cat) {

            ?>
                <div id="tags<?php echo $cat; ?>">
                    <div id="inTag">
                        <label>Tags :</label>
                        <div class="tags">
                            <?php
                            foreach (array_column($liste_tags, "nomtag") as $key => $tag) {
                            ?>
                                <label class="toggle-button">
                                    <input type="checkbox" name="<?php echo $tag; ?>" value="<?php echo $tag; ?>" />
                                    <span><?php echo $tag; ?></span>
                                </label>
                            <?php
                            }
                            ?>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>



            <div class="TextAreaOffre">
                <!-- résumé -->
                <div class="champs champsValid">
                    <label for="resume">Résumé <span class="required">*</span> :</label>
                    <textarea id="resume" name="resume" placeholder="Écrivez une description rapide (< 140 caractères)" maxlength="140" required></textarea>
                </div>

                <!-- description détaillé -->
                <div>
                    <label for="description">Description détaillée <span class="required">*</span> :</label>
                    <textarea id="description" name="description" placeholder="Écrivez une description détaillée (< 400 caractères)" maxlength="400" required></textarea>
                </div>
            </div>


            <!-- jours ouvertures et heures d'ouverture -->
            <div class="ChoixJours">
                <label for="horaires">Horaires d'ouverture :</label>
                <div class="jours">
                    <button type="button" id="btnL" class="btnHoraire">L</button>
                    <input type="hidden" name="lundi" class="inputJour" value="<?php ?>">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Lundi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-L" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-L" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-L" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-L" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous êtes fermés le Lundi</label>
                    </div>
                </div>

                <div class="jours">
                    <button type="button" id="btnMa" class="btnHoraire">Ma</button>
                    <input type="hidden" name="mardi" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Mardi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-Ma" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-Ma" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-Ma" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-Ma" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Mardi</label>
                    </div>
                </div>
                <div class="jours">
                    <button type="button" id="btnMe" class="btnHoraire">Me</button>
                    <input type="hidden" name="mercredi" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Mercredi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-Me" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-Me" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-Me" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-Me" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Mercredi</label>
                    </div>
                </div>
                <div class="jours">
                    <button type="button" id="btnJ" class="btnHoraire">J</button>
                    <input type="hidden" name="jeudi" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Jeudi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-J" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-J" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-J" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-J" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Jeudi</label>
                    </div>
                </div>
                <div class="jours">
                    <button type="button" id="btnV" class="btnHoraire">V</button>
                    <input type="hidden" name="vendredi" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Vendredi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-V" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-V" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-V" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-V" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Vendredi</label>
                    </div>
                </div>
                <div class="jours">
                    <button type="button" id="btnS" class="btnHoraire">S</button>
                    <input type="hidden" name="samedi" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Samedi, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-S" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-S" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-S" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-S" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Samedi</label>
                    </div>
                </div>
                <div class="jours">
                    <button type="button" id="btnD" class="btnHoraire">D</button>
                    <input type="hidden" name="dimanche" class="inputJour">
                    <div class="ouvert">
                        <div class="heures1 horairesAfficher">
                            <label for="heure-debut">Le Dimanche, vous êtes ouvert de </label>
                            <input type="time" class="heure-debut" name="debut-matin-D" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-matin-D" step="60">

                            <h4 class="btnAjoutHoraire">+</h4>
                        </div>
                        <div class="heures2 horairesCacher">
                            <label for="heure-debut">et de </label>
                            <input type="time" class="heure-debut" name="debut-aprem-D" step="60">
                            <label for="heure-fin"> à </label>
                            <input type="time" class="heure-fin" name="fin-aprem-D" step="60">
                        </div>
                    </div>
                    <div class="fermer">
                        <label>Vous ête fermés le Dimanche</label>
                    </div>
                </div>
            </div>

            <!-- Adresse -->
            <div class="champs champsValid">
                <div class="champsAdresse">
                    <label for="adresse">Adresse <span class="required">*</span> :</label>
                    <div>
                        <input type="text" id="num" name="num" placeholder="Numéro" minlength="1" maxlength="3" required>
                        <input type="text" id="nomRue" name="nomRue" placeholder="Nom de rue" maxlength="80" required>
                        <input type="text" id="ville" name="ville" placeholder="Ville" maxlength="70" required>
                        <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" required>
                    </div>
                </div>
            </div>

            <div class="InfoPerso">
                <!-- Abonnement -->
                <div class="champs champsValid">
                    <label for="offre">Type offre <span class="required">*</span>:</label>
                    <select id="offre" name="offre" required>
                        <option value="">Sélectionnez un type d'offre</option>
                        <option value="Standard">Standard</option>
                        <option value="Premium">Premium</option>
                    </select>
                </div>

                <!-- accessibilité -->
                <div class="champs champsValid">
                    <label for="choixAccessible">Accessibilité aux personnes à mobilité reduite <span class="required">*</span>:</label>
                    <select id="choixAccessible" name="choixAccessible" required>
                        <option value="">Sélectionnez un choix</option>
                        <option value="Accessible">Accessible</option>
                        <option value="PasAccessible">Pas Accessible</option>
                    </select>
                </div>
            </div>

            <!-- Option -->
            <div class="champsOption">

                <div class="choixOption">
                    <label for="option">Option :</label>
                    <select id="option" name="option">
                        <option value="">Aucune</option>
                        <option value="AlaUne">A la une</option>
                        <option value="EnRelief">En relief</option>
                    </select>
                </div>

                <div id="dateOption">
                    <div class="saisieDate">
                        <div>
                            <label for="date_debut_opt">Date de lancement(l'option débutera à cette date) :</label>
                            <input type="date" id="date_debut_opt" name="date_debut_opt" placeholder="JJ/MM/AAAA" step=7>
                        </div>

                        <div>
                            <label for="duree_opt">Durée de l'option (en semaine) :</label>
                            <input type="number" id="duree_opt" name="duree_opt" value="1" min=1 max=4>
                        </div>
                    </div>
                </div>

            </div>



            <!-- <div class="champs">
                    <label for="prixOffre">Prix de l'offre : <?php // echo 
                                                                ?> </label>
                </div> -->

            <?php
            if (
                !is_null($idproprive) &&  // permet de vérifier l'id_c
                ( // verifie que les donnees banquaires ne sont pas deja dans la BDD
                    // verif info carte
                    (empty($info_banq["coordonnee_bancaire"]) ||
                        empty($info_banq["date_exp"]) ||
                        empty($info_banq["cryptogramme"]) ||
                        empty($info_banq["nom_titulaire_carte"])) &&

                    // verif info paypal
                    (empty($info_banq["addressmail_pp"]) ||
                        empty($info_banq["mdp_pp"])) &&

                    // verif info virement
                    empty($info_banq["iban"])

                )
            ) {
            ?>
                <div id="preventionPaiement">
                    <p>En confirmant la création de l'offre vous serez facturer au prix de l'abonnement et des options que vous aurez choisis.</p>
                    <p>La prise d'un abonnement est obligatoire pour la publication d'une offre sur le site.</p>
                </div>
                <!-- paiement carte bancaire -->
                <h4>Paiement carte bancaire</h4>
                <div class="champs champsValid">
                    <label for="cb">coordonnée bancaire :</label>
                    <input type="text" id="cb" name="cb" placeholder="Entrez vos coordonnées bancaires">
                </div>
                <div class="champs champsValid">
                    <label for="DE">Date expiration :</label>
                    <input type="text" id="DE" name="DE" placeholder="MM/AA">
                </div>
                <div class="champs champsValid">
                    <label for="crypto">Cryptogramme :</label>
                    <input type="text" id="crypto" name="crypto" placeholder="Ex: 123">
                </div>
                <div class="champs champsValid">
                    <label for="TC">Titulaire de la carte :-</label>
                    <input type="text" id="TC" name="TC" placeholder="Prenom NOM">
                </div>

                <!-- paiement paypal -->
                <h4>Paiement par paypal</h4>
                <div class="champs champsValid">
                    <label for="AdM_PP">Adresse mail :</label>
                    <input type="text" id="AdM_PP" name="AdM_PP">
                </div>
                <div class="champs champsValid">
                    <label for="MDP_PP">Mot de Passe :</label>
                    <input type="text" id="MDP_PP" name="MDP_PP">
                </div>

                <!-- paiement prélèvement bancaire -->
                <h4>Paiement par prélèvement bancaire</h4>
                <div class="champs champsValid">
                    <label for="iban">Iban :</label>
                    <input type="text" id="iban" name="iban">
                </div>
            <?php
            }
            ?>


            <!-- Bouton de confirmation d'ajout d'offre ou d'annulation -->

            <button type="submit" href="gestionOffres.php" class="btnConfirmer">
                <p class="texteLarge boldArchivo">Valider</p>
            </button>

            <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
            <script src="/js/CreaOffrePro.js"></script>
            <script>
                function updatePreview() {
                    const input = document.getElementById('fichier1');
                    const previewImage = document.getElementById('previewImage');

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function updatePreviewAttraction() {
                    const input = document.getElementById('plan');
                    const previewImage = document.getElementById('previewImageAttraction');

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }

                function updatePreviewCarte() {
                    const input = document.getElementById('carte');
                    const previewImage = document.getElementById('previewImageCarte');

                    if (input.files && input.files[0]) {
                        const reader = new FileReader();
                        reader.onload = function(e) {
                            previewImage.src = e.target.result;
                        };
                        reader.readAsDataURL(input.files[0]);
                    }
                }
            </script>


        </form>

        <?php
        include "../composants/footer/footer.php";
        ?>
    </body>

    </html>

<?php
}
?>