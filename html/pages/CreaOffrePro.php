<?php
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
<body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($user == "pro")
            {
                echo "fondPro";
            }
        ?>>
        <?php include "../composants/header/header.php";        //import navbar
        ?>

    <main>

    <div class="conteneur-formulaire">
        <h1>Création d'une offre</h1>
        <form>
            <div class="champs">
                <label for="titre">Titre <span class="required">*</span> :</label>
                <input type="text" id="titre" name="titre" placeholder="Entrez le titre de l'offre" required>
            </div>

            <div class="champs">
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
            </div>

            <div class="champs">
                <label for="prix-minimal">Prix minimal :</label>
                <input type="number" id="prix-minimal" name="prix-minimal" placeholder="Entrez le prix minimal">
            </div>

            <div class="champs">
                <label for="resume">Résumé <span class="required">*</span> :</label>
                <textarea id="resume" name="resume" placeholder="Écrivez une description rapide (> 140 caractères)" required></textarea>
            </div>

            <div class="champs">
                <label for="description">Description détaillée <span class="required">*</span> :</label>
                <textarea id="description" name="description" placeholder="Écrivez une description détaillée (> 2000 caractères)" required></textarea>
            </div>

            <div class="champs">
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
        </form>
    </div>
    </main>

    <?php
            include "../composants/footer/footer.php";
        ?>
</body>

</html>