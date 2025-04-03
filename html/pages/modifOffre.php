<?php

use Sabberworm\CSS\CSSList\KeyFrame;

session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../composants/bdd/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif

// Inclusion du script pour vérifier si l'utilisateur a un compte pro
include('../composants/verif/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}

if (isset($_GET["idOffre"])) {
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // On cherche dans quelle catégorie est l'offre
    foreach (['visite', 'restauration', 'spectacle', 'parcattraction', 'activite'] as $nom_cat) {

        // Requête pour chercher la catégorie
        $stmt = $dbh->prepare("SELECT idoffre FROM tripskell._" . $nom_cat . " WHERE idOffre = :idOffre;");
        $stmt->execute([':idOffre' => $idOffre]);

        // Si l'offre appartient à une catégorie, on envoie la catégorie au JS
        if (isset($stmt->fetch()['idoffre']) && empty($_POST)) { ?>
            <script>
                let categorie_offre = '<?php echo $nom_cat; ?>';
            </script>
<?php

            // Si c'est une visite, on récupère les langues
            $langue_preselec = array();
            if ($nom_cat === 'visite') {
                $stmt = $dbh->prepare("SELECT nomlangue FROM tripskell._possedelangue WHERE idOffre = :idOffre;");
                $stmt->execute([':idOffre' => $idOffre]);
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
    $liste_tags_preselec = array_column($stmt->fetchAll(), "nomtag");
} else {
    die("Error");
}

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

if (!empty($_FILES)) {
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
        move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/imagesOffres/" . $nom_img);
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
    $requete .= "ageminimum = :ageminimum,";
    $requete .= "duree_a = :duree_a,";  // Pour activité
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
    $nbattraction = (!empty($_POST['nbAttraction']) ? $_POST['nbAttraction'] : null);
    $agemin = (!empty($_POST['agemin']) ? $_POST['agemin'] : null);
    $duree_a = (!empty($_POST['duree_a']) ? $_POST['duree_a'] : null);
    $ageminimum = (!empty($_POST['ageminimum']) ? $_POST['ageminimum'] : null);
    $prestation = $_POST['prestation'];
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Exécution de la mise à jour
    $stmt->execute();


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

        // Vérification que les horaires sont valides (matin et après-midi)
        if ($debMatin != null && $finMatin != null) {
            // Vérifier si l'horaire existe déjà pour ce jour et cette offre
            $query = "SELECT * FROM tripskell._ouverture WHERE idoffre = :idOffre AND id_jour = :id_jour";
            $stmt = $dbh->prepare($query);
            $stmt->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
            $stmt->bindValue(':id_jour', $jour, PDO::PARAM_STR);
            $stmt->execute();

            $result = $stmt->fetchAll();

            if (count($result) > 0) {
                // Horaire existe, mettre à jour les données
                foreach ($result as $row) {
                    $updateQuery = "UPDATE tripskell._horaire 
                                    SET horaire_matin_debut = :debMatin, horaire_matin_fin = :finMatin,
                                        horaire_aprem_debut = :debAprem, horaire_aprem_fin = :finAprem
                                    WHERE id_hor = :id_hor";

                    $stmt = $dbh->prepare($updateQuery);

                    // Lier les valeurs aux paramètres
                    $stmt->bindValue(':debMatin', $debMatin, $debMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmt->bindValue(':finMatin', $finMatin, $finMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmt->bindValue(':debAprem', $debAprem, $debAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmt->bindValue(':finAprem', $finAprem, $finAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
                    $stmt->bindValue(':id_hor', $row["id_hor"], PDO::PARAM_INT);

                    $stmt->execute();
                }
            } else if ($result) {
                $idHor = $result['id_hor'];

                // Suppression de l'horaire correspondant
                $deleteQuery = "DELETE FROM tripskell._horaire WHERE id_hor = :id_hor";
                $stmt = $dbh->prepare($deleteQuery);
                $stmt->bindValue(':id_hor', $idHor, PDO::PARAM_INT);
                $stmt->execute();
            }else {
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
    }


    /* --------------------------------------------------------------------------------------------- */

    if (isset($_POST['lang'])) {
        foreach ($langues as $langue) {
            // permet de savoir si la langue n'est pas deja dans la BDD
            $query = "select nomlangue from tripskell._possedelangue where idOffre='" . $idOffre . "' and nomlangue='" . $langue . "';";
            $stmt = $dbh->prepare($query);
            $stmt->execute();
            $lang_pres = !isset($stmt->fetch()['nomlangue']);
            if (in_array($langue, $_POST['lang']) && $lang_pres) {
                $dbh->query("insert into tripskell._possedelangue(nomlangue, idOffre) values ('" . $langue . "','" . $idOffre . "');");
            }
            if (!in_array($langue, $_POST['lang']) && !$lang_pres) {
                $dbh->query("delete from tripskell._possedelangue where nomlangue='" . $langue . "' and idOffre='" . $idOffre . "';");
            }
        }
    }



    // Redirection vers gestionOffres.php après la mise à jour réussie
    header("Location: ../pages/gestionOffres.php");
    exit(); // Terminer le script après la redirection pour éviter d'exécuter du code inutile
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
        <title>Modification Offre</title>

        <!-- Favicon -->
        <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="../style/pages/Formulaire.css">
    </head>

    <body class=<?php echo "fondPro"; ?>>

        <?php include "../composants/header/header.php";        //import navbar
        ?>


        <div class="conteneur-formulaire">
            <form name="modification" action="/pages/modifOffre.php?idOffre=<?php echo $idOffre; ?>" method="post" enctype="multipart/form-data">

                <div class="InfoPerso">
                    <div class="champs">
                        <label for="titre">Titre :</label>
                        <!-- Champ de saisie pour le titre avec valeur préremplie -->
                        <input type="text" id="titre" name="titre" value="<?php echo $contentOffre["titreoffre"]; ?>" required>
                    </div>

                    <div class="champs">
                        <div class="PhotoOffre">
                            <img id="previewImage"
                                src="../images/imagesOffres/<?php echo htmlspecialchars($contentOffre['img1']); ?>"
                                alt="Cliquez pour ajouter une image"
                                onclick="document.getElementById('fichier1').click()">
                            <input type="file" id="fichier1" name="fichier1"
                                accept="image/png, image/jpeg"
                                style="display: none;"
                                onchange="updatePreview()">
                        </div>
                    </div>

                </div>

                <!--------------------- > CATEGORIES < --------------------->

                <!-- ----------------- VISITE ------------------- -->

                <div id="champsVisite">
                    <div class="zoneChoixVisite">
                        <div class="champs dureeVisite">
                            <label for="duree_v">Duree de la visite :</label>
                            <input type="time" id="duree_v" name="duree_v" value="<?php if ($contentOffre["duree_v"] != null) {
                                                                                        echo substr($contentOffre["duree_v"], 0, 5);
                                                                                    } ?>" />
                        </div>
                        <div class="champsCategorie">
                            <label>Langue(s) de la visite :</label>
                            <div class="parentVisite">
                                <?php
                                foreach ($langues as $langue) { ?>
                                    <label class="toggle-button">
                                        <input type="checkbox" id="lang" name="lang[]" value="<?php echo $langue; ?>" <?php echo in_array($langue, $langue_preselec) ? 'checked' : ''; ?> />
                                        <span><?php echo $langue; ?></span>
                                    </label>
                                <?php } ?>
                            </div>
                        </div>

                        <div class="champsCategorie">
                            <label>La visite est guidée <span class="required">*</span> :</label>
                            <div class="parentVisite">
                                <label class="toggle-button">
                                    <input type="radio" id="guidee" name="guidee" value="true" <?php echo $contentOffre["guidee"] ? 'checked' : ''; ?> />
                                    <span>Oui</span>
                                </label>
                                <label class="toggle-button">
                                    <input type="radio" id="guidee" name="guidee" value="false" <?php echo !$contentOffre["guidee"] ? 'checked' : ''; ?> /> <!-- a enlever et utilisation de checkbox -->
                                    <span>Non</span>
                                </label>
                            </div>
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
                            <option value="$" <?php echo $contentOffre["gammeprix"] == '$' ? 'selected' : ''; ?>>$</option>
                            <option value="$$" <?php echo $contentOffre["gammeprix"] == '$$' ? 'selected' : ''; ?>>$$</option>
                            <option value="$$$" <?php echo $contentOffre["gammeprix"] == '$$$' ? 'selected' : ''; ?>>$$$</option>
                        </select>
                    </div>
                </div>

                <!-- ----------------- PARC ATTRACTION ------------------- -->
                <div id="champsPA">
                    <div class="InfoPerso">
                        <div class="champs">
                            <label for="nbAttraction">Nombre Attraction :</label>
                            <input type="text" id="nbAttraction" name="nbAttraction" placeholder="Entrez le nombre d'attraction" minlength="1" maxlength="3" value="<?php echo $contentOffre["nbattraction"]; ?>">
                        </div>
                        <div class="champs">
                            <label for="ageminimum">âge minimum :</label>
                            <input type="text" id="ageminimum" name="ageminimum" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3" value="<?php echo $contentOffre["ageminimum"]; ?>">
                        </div>
                    </div>
                    <div id="champsPA">
                        <div class="champs">
                            <label for="plan">Selectionner un plan :</label>
                            <input type="file" id="plan" name="plan">
                        </div>
                    </div>
                </div>

                <!-- ----------------- SPECTACLE ------------------- -->

                <div id="champsSpectacle">
                    <div class="champs">
                        <label for="duree_s">Duree de la Spectacle :</label>
                        <input type="time" id="duree_s" name="duree_s" value="<?php if ($contentOffre["duree_s"] != null) {
                                                                                    echo substr($contentOffre["duree_s"], 0, 5);
                                                                                } ?>" />
                    </div>
                    <div class="champs">
                        <label for="capacite">Capacité :</label>
                        <input type="text" id="capacite" name="capacite" placeholder="Entrez la capacite" value="<?php echo $contentOffre["capacite"]; ?>">
                    </div>
                </div>

                <!-- ----------------- ACTIVITE ------------------- -->

                <div id="champsActivite">
                    <div class="texteAreaActivite">
                        <label for="prestation">Prestation proposée :</label>
                        <textarea id="prestation" name="prestation" placeholder="Écrivez les prestations proposer (> 100 caractères)" maxlength="100"><?php echo $contentOffre["prestation"]; ?></textarea>
                    </div>
                    <div class="InfoPerso">
                        <div class="champs">
                            <label for="duree_a">Duree de l'Activité :</label>
                            <input type="time" id="duree_a" name="duree_a" value="<?php if ($contentOffre["duree_a"] != null) {
                                                                                        echo substr($contentOffre["duree_a"], 0, 5);
                                                                                    } ?>" />
                        </div>
                        <div class="champs">
                            <label for="agemin">âge minimum :</label>
                            <input type="text" id="agemin" name="agemin" placeholder="Entrez l'âge minimum" minlength="1" maxlength="3" value="<?php echo $contentOffre["agemin"]; ?>">
                        </div>
                    </div>
                </div>


                <!-- ----------------- TAGS ------------------- -->
                <?php


                $tags_cat = ['Visite', 'Restauration', 'PA', 'Spectacle', 'Activite'];

                foreach ($tags_cat as $cat) {

                ?>
                    <div id="tags<?php echo $cat; ?>" class="listeTags">
                        <label>Tags :</label>
                        <div class="tags">
                            <?php
                            foreach (array_column($liste_tags, "nomtag") as $key => $tag) {
                            ?>
                                <label class="toggle-button">
                                    <input type="checkbox" id="<?php echo $tag; ?>" name="<?php echo $tag; ?>" value="<?php echo $tag; ?>" <?php echo in_array($tag, $liste_tags_preselec) ? 'checked' : ''; ?> />
                                    <span><?php echo $tag; ?></span>
                                </label>
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
                    <input type="text" id="prix-minimal" name="prix-minimal" value="<?php echo $contentOffre["tarifminimal"]; ?>">
                </div>

                <div class="TextAreaOffre">
                    <div>
                        <label for="resume">Résumé :</label>
                        <!-- Champ de saisie pour le résumé avec valeur préremplie -->
                        <textarea id="resume" name="resume" required><?php echo $contentOffre["resume"]; ?></textarea>
                    </div>

                    <div>
                        <label for="description">Description détaillée :</label>
                        <!-- Champ de saisie pour la description détaillée avec valeur préremplie -->
                        <textarea id="description" name="description" required><?php echo $contentOffre["description_detaille"]; ?></textarea>
                    </div>
                </div>


                <!-- jours ouvertures et heures d'ouverture -->
                <label class="labelHoraire">Horaires d'ouverture :</label>
                <div class="ChoixJours">
                    <?php       //préremplis les champs cachés des jours avec les horaires de la base de données
                    $ouverture = $dbh->query("select * from tripskell._ouverture where idoffre='" . $idOffre . "';")->fetchAll();
                    $tabJours = [];
                    foreach ($ouverture as $key => $value) {
                        $horaire = $dbh->query("select * from tripskell._horaire as h join tripskell._ouverture as o on h.id_hor=" . $ouverture[$key]["id_hor"] . " where o.idOffre='" . $idOffre . "' and o.id_hor=" . $ouverture[$key]["id_hor"] . " and o.id_jour='" . $ouverture[$key]["id_jour"] . "';")->fetchAll()[0];

                        $tabJours[$horaire['id_jour']] = json_encode([
                            $horaire["horaire_matin_debut"],
                            $horaire["horaire_matin_fin"],
                            $horaire["horaire_aprem_debut"],
                            $horaire["horaire_aprem_fin"]
                        ]);
                    }
                    echo json_decode($tabJours["Lundi"], true)[0];
                    ?>

                    <div class="jours">
                        <button type="button" id="btnL" class="btnHoraire">L</button>
                        <input type="hidden" name="lundi" class="inputJour">
                        <div class="ouvert">
                            <div class="heures1 horairesAfficher">
                                <label for="heure-debut">Le Lundi, vous êtes ouvert de </label>
                                <input type="time" class="heure-debut" name="debut-matin-L" step="60" <?php
                                                                                                        if (array_key_exists("Lundi", $tabJours)) { ?>value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Lundi"], true)[0], 0, 5);  ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-L" step="60" <?php
                                                                                                    if (array_key_exists("Lundi", $tabJours)) { ?>value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Lundi"], true)[1], 0, 5);  ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-L" step="60" <?php
                                                                                                        if (array_key_exists("Lundi", $tabJours)) { ?>value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Lundi"], true)[2], 0, 5);  ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-L" step="60" <?php
                                                                                                    if (array_key_exists("Lundi", $tabJours)) { ?>value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Lundi"], true)[3], 0, 5);  ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-Ma" step="60" <?php
                                                                                                        if (array_key_exists("Mardi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mardi"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-Ma" step="60" <?php
                                                                                                    if (array_key_exists("Mardi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mardi"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-Ma" step="60" <?php
                                                                                                        if (array_key_exists("Mardi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mardi"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-Ma" step="60" <?php
                                                                                                    if (array_key_exists("Mardi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mardi"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-Me" step="60" <?php
                                                                                                        if (array_key_exists("Mercredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mercredi"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-Me" step="60" <?php
                                                                                                    if (array_key_exists("Mercredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mercredi"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-Me" step="60" <?php
                                                                                                        if (array_key_exists("Mercredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mercredi"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-Me" step="60" <?php
                                                                                                    if (array_key_exists("Mercredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Mercredi"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-J" step="60" <?php
                                                                                                        if (array_key_exists("Jeudi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Jeudi"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-J" step="60" <?php
                                                                                                    if (array_key_exists("Jeudi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Jeudi"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-J" step="60" <?php
                                                                                                        if (array_key_exists("Jeudi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Jeudi"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-J" step="60" <?php
                                                                                                    if (array_key_exists("Jeudi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Jeudi"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-V" step="60" <?php
                                                                                                        if (array_key_exists("Vendredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Vendredi"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-V" step="60" <?php
                                                                                                    if (array_key_exists("Vendredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Vendredi"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-V" step="60" <?php
                                                                                                        if (array_key_exists("Vendredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Vendredi"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-V" step="60" <?php
                                                                                                    if (array_key_exists("Vendredi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Vendredi"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-S" step="60" <?php
                                                                                                        if (array_key_exists("Samedi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Samedi"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-S" step="60" <?php
                                                                                                    if (array_key_exists("Samedi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Samedi"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-S" step="60" <?php
                                                                                                        if (array_key_exists("Samedi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Samedi"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-S" step="60" <?php
                                                                                                    if (array_key_exists("Samedi", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Samedi"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
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
                                <input type="time" class="heure-debut" name="debut-matin-D" step="60" <?php
                                                                                                        if (array_key_exists("Dimanche", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Dimanche"], true)[0], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-matin-D" step="60" <?php
                                                                                                    if (array_key_exists("Dimanche", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Dimanche"], true)[1], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>

                                <h4 class="btnAjoutHoraire">+</h4>
                            </div>
                            <div class="heures2 horairesAfficher">
                                <label for="heure-debut">et de </label>
                                <input type="time" class="heure-debut" name="debut-aprem-D" step="60" <?php
                                                                                                        if (array_key_exists("Dimanche", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Dimanche"], true)[2], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                                <label for="heure-fin"> à </label>
                                <input type="time" class="heure-fin" name="fin-aprem-D" step="60" <?php
                                                                                                    if (array_key_exists("Dimanche", $tabJours)) { ?> value="<?php
                                                                                                                                        echo substr(json_decode($tabJours["Dimanche"], true)[3], 0, 5); ?>" <?php } else { ?> value="" <?php } ?>>
                            </div>
                        </div>
                        <div class="fermer">
                            <label>Vous ête fermés le Dimanche</label>
                        </div>
                    </div>
                </div>

                <div class="champs">
                    <div class="champsAdresse">
                        <label for="adresse">Adresse :</label>
                        <!-- Champs de saisie pour l'adresse avec valeurs préremplies -->
                        <div>
                            <input type="text" id="num" name="num" value="<?php echo $contentOffre["numero"]; ?>" minlength="1" maxlength="3" required>
                            <input type="text" id="nomRue" name="nomRue" value="<?php echo $contentOffre["rue"]; ?>" maxlength="80" required>
                            <input type="text" id="ville" name="ville" value="<?php echo $contentOffre["ville"]; ?>" maxlength="70" required>
                            <input type="text" id="codePostal" name="codePostal" value="<?php echo $contentOffre["codepostal"]; ?>" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" required>
                        </div>
                    </div>
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
                <div class="champs champsAccessibilite">
                    <label for="choixAccessible">Accessibilité aux personnes à mobilité reduite :</label>
                    <select id="choixAccessible" name="choixAccessible">
                        <option value="Accessible" <?php echo ($contentOffre["accessibilite"] == "Accessible") ? 'selected' : ''; ?>>Accessible</option>
                        <option value="PasAccessible" <?php echo ($contentOffre["accessibilite"] == "PasAccessible") ? 'selected' : ''; ?>>Pas Accessible</option>
                    </select>
                </div>

                <!-- <div class="champs">
                    futur data de mise en ligne
                </div> -->


                <!-- Bouton pour soumettre le formulaire -->
                <button type="submit" href="gestionOffres.php" class="btnConfirmer">
                    <p class="texteLarge boldArchivo">Valider</p>
                </button>


            </form>
            <script src="/js/modifOffre.js"></script>

            <?php
            include "../composants/footer/footer.php";
            ?>

    </body>

    </html>


    <script>
        function updatePreview() {
            const input = document.getElementById('fichier1');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                fileName.textContent = "Image sélectionnée : " + input.files[0].name;
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