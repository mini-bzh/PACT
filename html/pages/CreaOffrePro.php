<?php
// CreaOffreProPubV5
// php -S localhost:8888
// http://localhost:8888/pages/CreaOffrePro.php

$user = "pro";

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation Offre</title>
    <link rel="stylesheet" href="../style/pages/CreaOffrePro.css">
</head>

<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($user == "pro") {
                echo "fondPro";
            }
            ?>>
    <?php include "../composants/header/header.php";        //import navbar
    ?>

    <main>

        <div class="conteneur-formulaire">
            <h1>Création d'une offre</h1>
            <form name="test" action="/pages/CreaOffrePro.php" method="post">
                <div class="champs">
                    <label for="titre">Titre <span class="required">*</span> :</label>
                    <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'offre" required>
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
                        <option value="">Sélectionnez des tags</option>
                        <option value="AlaUne">A la une</option>
                        <option value="EnRelief">En relief</option>
                        <option value="AlaUneEtEnRelief">A la une et En relief</option>
                    </select>
                </div>

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

echo "HW";

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

if (!empty($_POST)) {
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

    $titre = $_POST["titre"];
    $prixMin = $_POST["prix-minimal"];
    $resume = $_POST["resume"];
    $description = $_POST["description"];
    $heuresDebut = $_POST["heure-debut"];
    $heuresFin = $_POST["heure-fin"];
    $heures = $heuresDebut." ".$heuresFin;
    $numero = $_POST["num"];
    $nomRue = $_POST["nomRue"];
    $ville = $_POST["ville"];
    $codePostal = $_POST["codePostal"];
    $typeOffre = $_POST["offre"];
    $option = $_POST["option"];
    $note = 5;
    $accessible = $_POST["choixAccessible"];

    $qwery = "INSERT INTO tripskell.offre_pro(titreOffre,resume,description_detaille,tarifMinimal,note,horaires,accessibilite,enLigne,id_abo, id_option, numero, rue, ville, codePostal,id_c) VALUES('titre','resume','description',23,2,'23h-12h','accessible',true,'Premium','A la une','25','rue','ville','22540',1);";
    echo $qwery;

    $stmt = $dbh->prepare(
        $qwery
    );

    

    $stmt->execute();
    $dbh = null;

    header("location: /pages/accueil.php");
    exit();
}
?>