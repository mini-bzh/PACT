<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// Inclusion du script pour vérifier si l'utilisateur a un compte pro
include('../php/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}


// On va récupérer ici l'identifiant id_c présent dans les vues pro.
if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];

    // reccuperation de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];
}



// $stock = false; // Stock est lié à la popup mais pour cause de soucis j'ai mit la popup de côté
?>
    <?php

    $idOffre = "";
if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.

// ici on exploite les fichier image afin de les envoyer dans un dossier du git dans le but de stocker les images reçus
$i = 0;
foreach ($_FILES as $key_fichier => $fichier) { // on parcour les fichiers de la super globale $_FILES

    $nom_img[$key_fichier] = null; // initialistion des noms des images a null

    if ($fichier["size"]!=0) {  // on verifie que le fichier a ete transmit

        // creation du nom de fichier en utilisant time et le type de fichier
        $nom_img[$key_fichier] = time() + $i++ . "." . explode("/", $_FILES[$key_fichier]["type"])[1];

        // deplacement du fichier depuis l'espace temporaire
        move_uploaded_file($fichier["tmp_name"], "../images/imagesOffres/" . $nom_img[$key_fichier]);

    }
}

/*
$type2 = explode("/", $image2["types"])[1];
$nom_img2 = time() . "." . $type2;
if (in_array($type2, ["png", "gif", "jpeg"])) {
    move_uploaded_file($image2["tmp_name"], "../images/imagesOffres/" . $nom_img2);
}

$type3 = explode("/", $image3["types"])[1];
$nom_img3 = time() . "." . $type3;
if (in_array($type3, ["png", "gif", "jpeg"])) {
    move_uploaded_file($image3["tmp_name"], "../images/imagesOffres/" . $nom_img3);
}

$type4 = explode("/", $image4["types"])[1];
$nom_img4 = time() . "." . $type4;
if (in_array($type4, ["png", "gif", "jpeg"])) {
    move_uploaded_file($image4["tmp_name"], "../images/imagesOffres/" . $nom_img4);
}
    */


// on definie ici la requête INSERT. C'est une étape préparatoire avant d'insérer les valeurs dans la vue. 
// requete va nous servir de variable de stock qui va concatener chaque partie de l'INSERT

$requete = "INSERT INTO tripskell.offre_pro(";
$requete .= "titreOffre, ";
$requete .= "resume, ";
$requete .= "description_detaille, ";
$requete .= "tarifMinimal, ";
$requete .= "note, ";
$requete .= "accessibilite, ";
$requete .= "enLigne, ";
$requete .= "id_abo, ";
$requete .= "id_option, ";
$requete .= "numero, ";
$requete .= "rue, ";
$requete .= "ville, ";
$requete .= "codePostal,";
$requete .= "id_c, ";
$requete .= "img1, ";
$requete .= "img2, ";
$requete .= "img3, ";
$requete .= "img4) ";

$requete .= "VALUES (";
$requete .= ":titre,";
$requete .= ":resume,";
$requete .= ":description,";
$requete .= ":tarif,";
$requete .= ":note,";
$requete .= ":accessibilite,";
$requete .= ":enLigne,";
$requete .= ":id_abo,";
$requete .= ":id_option,";
$requete .= ":numero,";
$requete .= ":rue,";
$requete .= ":ville,";
$requete .= ":codePostal,";
$requete .= ":id_c, ";
$requete .= ":img1, ";
$requete .= ":img2, ";
$requete .= ":img3, ";
$requete .= ":img4) ";
$requete .= "returning idOffre;";

// ici, on va éxecuter l'INSERT tout en assignant les variables correspondants à celle de la Vue
$stmt = $dbh->prepare($requete);
$stmt->bindParam(":titre", $titre);
$stmt->bindParam(":resume", $resume);
$stmt->bindParam(":description", $description);
$stmt->bindParam(":tarif", $tarif);
$stmt->bindParam(":note", $note);
$stmt->bindParam(":accessibilite", $accessible);
$stmt->bindParam(":enLigne", $enLigne);
$stmt->bindParam(":id_abo", $id_abo);
$stmt->bindParam(":id_option", $id_option);
$stmt->bindParam(":numero", $numero);
$stmt->bindParam(":rue", $rue);
$stmt->bindParam(":ville", $ville);
$stmt->bindParam(":codePostal", $codePostal);
$stmt->bindParam(":id_c", $id_c);
$stmt->bindParam(":img1", $img1);
$stmt->bindParam(":img2", $image2);
$stmt->bindParam(":img3", $image3);
$stmt->bindParam(":img4", $image4);



// On definit ici chacune des variables

$titre = $_POST["titre"];
$resume = $_POST["resume"];
$description = $_POST["description"];
$tarif = $_POST["prix-minimal"];
$note = 5;
$accessible = $_POST["choixAccessible"];
$enLigne = true;
//$id_abo = $_POST["offre"];
//$id_option = $_POST["option"];
$id_abo = 'Standard';
$id_option = null;
$numero = $_POST["num"];
$rue = $_POST["nomRue"];
$ville = $_POST["ville"];
$codePostal = $_POST["codePostal"];

$img1 = $nom_img["fichier1"];
$img2 = $nom_img["fichier2"];
$img3 = $nom_img["fichier3"];
$img4 = $nom_img["fichier4"];

// on récupère l'id_c de la session dans le but d'identifier quel compte est connecter.
$id_c = $_SESSION["idCompte"];

// on execute tout ce qui a été fait précèdement
$stmt->execute();

$idOffre = $stmt->fetchColumn();



// on ferme la base de donnée
$dbh = null;

header("Location: /pages/gestionOffres.php"); // on redirige vers la page de l'offre créée
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
        <title>Creation Offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="/style/pages/CreaOffrePro.css">
        <p id="idOffre" class="displayNone"><?php echo $idOffre; ?></p>

    </head>

    <body class="fondPro">

        <?php include "../composants/header/header.php";        //import navbar
        ?>

        <main>

            <div class="conteneur-formulaire">

                <h1>Création d'une offre</h1>

                <!-- Formulaire de création d'offre -->

                <form name="creation" action="/pages/CreaOffrePro.php" method="post" enctype="multipart/form-data">


                    <!-- titre -->
                    <div class="champs">
                        <label for="titre">Titre <span class="required">*</span> :</label>
                        <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'offre" required>
                    </div>

                    <!-- Champs pour sélectionner les images -->
                    <div class="champs">
                        <label for="fichier1">Selectionner une image 1 :</label>
                        <input type="file" id="fichier1" name="fichier1" >
                    </div>
                    <div class="champs">
                        <label for="fichier2">Selectionner une image 2 :</label>
                        <input type="file" id="fichier2" name="fichier2" />
                    </div>
                    <div class="champs">
                        <label for="fichier3">Selectionner une image 3 :</label>
                        <input type="file" id="fichier3" name="fichier3" />
                    </div>
                    <div class="champs">
                        <label for="fichier4">Selectionner une image 4 :</label>
                        <input type="file" id="fichier4" name="fichier4" />
                    </div>


                    <!-- catégorie et tags -->
                    <div class="champs">
                        <label for="categorie">Catégorie <span class="required">*</span> :</label>
                        <select id="categorie" name="categorie" required>
                            <option value="">Sélectionnez une catégorie</option>
                            <option value="activite">Activité</option>
                            <option value="visite">Visite</option>
                            <option value="ParcDattraction">Parc d'attraction</option>
                            <option value="spectacle">Spectacle</option>
                            <option value="restauration">Restauration</option>
                        </select>
                    </div>

                    <div class="champs">
                        <label for="tags">Tags :</label>
                        <select id="tags" name="tags">
                            <option value="">Sélectionnez des tags</option>
                            <option value="tag1">Français</option>
                            <option value="tag2">Local</option>
                            <option value="tag2">Rafraichissant</option>
                            <option value="tag2">cuisine</option>
                            <option value="tag2">repas</option>
                            <option value="tag2">divertissant</option>
                        </select>
                    </div>


                    <!-- prix minimum -->
                    <div class="champs">
                        <label for="prix-minimal">Prix minimal (euro) :</label>
                        <input type="text" id="prix-minimal" name="prix-minimal" placeholder="Entrez le prix minimal (euro)">
                    </div>

                    <!-- résumé -->
                    <div>
                        <label for="resume">Résumé <span class="required">*</span> :</label>
                        <textarea id="resume" name="resume" placeholder="Écrivez une description rapide (> 140 caractères)" required></textarea>
                    </div>

                    <!-- description détaillé -->
                    <div>
                        <label for="description">Description détaillée <span class="required">*</span> :</label>
                        <textarea id="description" name="description" placeholder="Écrivez une description détaillée (> 2000 caractères)" required></textarea>
                    </div>


                    <!-- jours ouvertures et heures d'ouverture -->
                    <div>

                        <label for="horaires">Horaires d'ouverture :</label>
                        <div class="jours">
                            <button type="button" id="btnL">L</button>
                            <button type="button" id="btnMa">Ma</button>
                            <button type="button" id="btnMe">Me</button>
                            <button type="button" id="btnJ">J</button>
                            <button type="button" id="btnV">V</button>
                            <button type="button" id="btnS">S</button>
                            <button type="button" id="btnD">D</button>
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


                    <!-- Adresse -->
                    <div class="champsAdresse">
                        <label for="adresse">Adresse <span class="required">*</span> :</label>
                        <input type="text" id="num" name="num" placeholder="Numéro" required>
                        <input type="text" id="nomRue" name="nomRue" placeholder="Nom de rue" required>
                        <input type="text" id="ville" name="ville" placeholder="Ville" required>
                        <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" required> 
                    </div>

                    <!-- Abonnement -->
                    <div class="champs">
                        <label for="offre">Type offre :</label>
                        <select id="offre" name="offre">
                            <option value="Standard">Sélectionnez un type d'offre</option>
                            <option value="Standard">Standard</option>
                            <option value="Premium">Premium</option>
                        </select>
                    </div>

                    <!-- Option -->
                    <div class="champs">
                        <label for="option">Option :</label>
                        <select id="option" name="option">
                            <option value="">Sélectionnez des options</option>
                            <option value="Aucune">Aucune</option>
                            <option value="AlaUne">A la une</option>
                            <option value="EnRelief">En relief</option>
                            <option value="AlaUneEtEnRelief">A la une et En relief</option>
                        </select>
                    </div>

                    <!-- accessibilité -->
                    <div class="champs">
                        <label for="choixAccessible">Accessibilité aux personnes à mobilité reduite :</label>
                        <select id="choixAccessible" name="choixAccessible">
                            <option value="">Sélectionnez un choix</option>
                            <option value="Accessible">Accessible</option>
                            <option value="PasAccessible">Pas Accessible</option>
                        </select>
                    </div>


                    <!-- <div class="champs">
                    <label for="prixOffre">Prix de l'offre : <?php // echo 
                                                                ?> </label>
                </div> -->


                    <!-- Bouton de confirmation d'ajout d'offre ou d'annulation -->

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
                    </div>


                    <!-- POPUP de confirmation (problème de placement de la popup, à revoir comment la faire) -->

                    <!-- <?php
                            // if (!empty($_POST)) {
                            ?>
                    <div id="popup">

                        <div>
                            <p>le prix de l'offre est de :</p>
                        </div>

                        <div class="zoneBtn">
                            <a href="CreaOffrePro.php" class="btnAnnuler">
                                <p class="texteLarge boldArchivo">Annuler</p>
                                <?php
                                // include '../icones/croixSVG.svg';
                                ?>
                            </a>

                            <button type="submit" href="gestionOffres.php" class="btnConfirmer">
                                <p class="texteLarge boldArchivo">Confirmer</p>
                                <?php
                                // include '../icones/okSVG.svg';
                                // $stock = true;
                                ?>
                            </button>
                        </div>
                    </div>
                <?php
                // }
                ?> -->
                <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
                <script src="../js/creaOffrePro.js"></script>


                    <!-- Données bancaire pour le pro privé. Cette partie ne s'affiche que si l'id_c est dans la table pro_prive -->
                    <?php
                    if (in_array($id_c, $idproprive)) { // permet de vérifier l'id_c 
                    ?>

                        <p>En confirmant la création de l'offre vous serez facturer au prix de l'abonnement que vous avez chosis avec en plus les options si vous en avez choisis.</p>

                        <div class="champs">
                            <label for="cb">coordonnée bancaire :<span class="required">*</span> :</label>
                            <input type="text" id="cb" name="cb" placeholder="Entrez vos coordonnées bancaires" required>
                        </div>
                        <div class="champs">
                            <label for="DE">Date expiration :<span class="required">*</span> :</label>
                            <input type="text" id="DE" name="DE" placeholder="MM/AA" required>
                        </div>
                        <div class="champs">
                            <label for="crypto">Cryptogramme :<span class="required">*</span> :</label>
                            <input type="text" id="crypto" name="crypto" placeholder="Ex: 123" required>
                        </div>
                        <div class="champs">
                            <label for="TC">Titulaire de la carte :<span class="required">*</span> :</label>
                            <input type="text" id="TC" name="TC" placeholder="Prenom NOM" required>
                        </div>
                    <?php
                    }
                    ?>

                </form>
            </div>
        </main>

        <?php
        include "../composants/footer/footer.php";
        ?>
    </body>

    </html>






    
<?php
}
?>
