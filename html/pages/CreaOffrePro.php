<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('/var/www/html/php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('/var/www/html/php/verif_compte_pro.php');

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
    <?php include "/var/www/html/composants/header/header.php";        //import navbar
    ?>

    <main>

        <div class="conteneur-formulaire">
            <h1>Création d'une offre</h1>

            <form name="test" action="/pages/CreaOffrePro.php" method="post">

                    <div class="champs">
                        <label for="titre">Titre <span class="required">*</span> :</label>
                        <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'offre" required>

                        <label for="fichier">Envoyer fichier :</label>
                        <input type="file" id="fichier" name="fichier" accept="image/*,.png,.jpg,.gif" multiple/>
                        <img src="../icones/joindreImagesSVG.svg" alt="error">
                        <br />
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
                    <input type="text" id="prix-minimal" name="prix-minimal" placeholder="Entrez le prix minimal (euro)">
                </div>

                <div>
                    <label for="resume">Résumé <span class="required">*</span> :</label>
                    <textarea id="resume" name="resume" placeholder="Écrivez une description rapide (> 140 caractères)" required></textarea>
                </div>

                <div>
                    <label for="description">Description détaillée <span class="required">*</span> :</label>
                    <textarea id="description" name="description" placeholder="Écrivez une description détaillée (> 2000 caractères)" required></textarea>
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
                        <input type="time" id="heure-debut" name="heure-debut">
                        <label for="heure-fin">à</label>
                        <input type="time" id="heure-fin" name="heure-fin">
                    </div>
                </div>

                <div class="champsAdresse">
                    <label for="adresse">Adresse <span class="required">*</span> :</label>
                    <input type="text" id="num" name="num" placeholder="Numéro" required>
                    <input type="text" id="nomRue" name="nomRue" placeholder="Nom de rue" required>
                    <input type="text" id="ville" name="ville" placeholder="Ville" required>
                    <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" required>
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
                        <option value="">Sélectionnez des options</option>
                        <option value="Aucune">Aucune</option>
                        <option value="AlaUne">A la une</option>
                        <option value="EnRelief">En relief</option>
                        <option value="AlaUneEtEnRelief">A la une et En relief</option>
                    </select>
                </div>
                -->

                <div class="champs">
                    <label for="choixAccessible">Accessibilité aux personnes à mobilité reduite :</label>
                    <select id="choixAccessible" name="choixAccessible">
                        <option value="">Sélectionnez un choix</option>
                        <option value="Accessible">Accessible</option>
                        <option value="PasAccessible">Pas Accessible</option>
                    </select>
                </div>

                <!-- <div class="champs">
                    futur data de mise en ligne
                </div> -->

                <input type="submit" value="Soumettre" />
                <!-- Bouton de test temporaire -->

            </form>
        </div>
    </main>

    <?php
    include "../composants/footer/footer.php";
    ?>
</body>

</html>


<?php


if (!empty($_POST)) {

    $qwery = "INSERT INTO tripskell.offre_pro(";
    $qwery .= "titreOffre,";
    $qwery .= "resume,";
    $qwery .= "description_detaille,";
    $qwery .= "tarifMinimal,";
    $qwery .= "note,";
    $qwery .= "horaires,";
    $qwery .= "accessibilite,";
    $qwery .= "enLigne,";
    $qwery .= "id_abo, ";
    $qwery .= "id_option, ";
    $qwery .= "numero, ";
    $qwery .= "rue, ";
    $qwery .= "ville, ";
    $qwery .= "codePostal,";
    $qwery .= "id_c) ";

    $qwery .= "VALUES(";
    $qwery .= ":titre,";
    $qwery .= ":resume,";
    $qwery .= ":description,";
    $qwery .= ":tarif,";
    $qwery .= ":note,";
    $qwery .= ":horaires,";
    $qwery .= ":accessibilite,";
    $qwery .= ":enLigne,";
    $qwery .= ":id_abo,";
    $qwery .= ":id_option,";
    $qwery .= ":numero,";
    $qwery .= ":rue,";
    $qwery .= ":ville,";
    $qwery .= ":codePostal,";
    $qwery .= ":id_c);";
    echo $qwery;

    $stmt = $dbh->prepare($qwery);
    $stmt->bindParam(":titre", $titre);
    $stmt->bindParam(":resume", $resume);
    $stmt->bindParam(":description", $description);
    $stmt->bindParam(":tarif", $tarif);
    $stmt->bindParam(":note", $note);
    $stmt->bindParam(":horaires", $horaires);
    $stmt->bindParam(":accessibilite", $accessible);
    $stmt->bindParam(":enLigne", $enLigne);
    $stmt->bindParam(":id_abo", $id_abo);
    $stmt->bindParam(":id_option", $id_option);
    $stmt->bindParam(":numero", $numero);
    $stmt->bindParam(":rue", $rue);
    $stmt->bindParam(":ville", $ville);
    $stmt->bindParam(":codePostal", $codePostal);
    $stmt->bindParam(":id_c", $id_c);

    $titre = $_POST["titre"];
    $resume = $_POST["resume"];
    $description = $_POST["description"];
    $tarif = $_POST["prix-minimal"];
    $note = 5;

    $heuresDebut = $_POST["heure-debut"];
    $heuresFin = $_POST["heure-fin"];
    $horaires = $heuresDebut . "-" . $heuresFin;

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

    $id_c = $_SESSION["idCompte"];



    $stmt->execute();
    $dbh = null;
}
?>