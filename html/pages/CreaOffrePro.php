<?php
// php -S localhost:8888
// http://localhost:8888/PACT/html/pages/CreaOffrePro.php
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/CreaOffrePro.css">
</head>

<body>
    <h1></h1>

    <div>
        <h2>Création d'une offre</h2>
        <div>
            <form name="test" action="http://localhost:8888/import.php" method="get">

                <div class="champTexte">
                    <label for="titre">Titre :</label>
                    <input type="text" id="titre" name="titre" required />
                    <br />
                </div>

                <div class="champTexte">
                    <label for="categorie">Catégorie :</label>
                    <select id="categorie" name="categorie">
                        <option value="restauration">Restauration</option>
                        <option value="visite">Visite</option>
                        <option value="spectacle">Spectacle</option>
                        <option value="parcDatraction">Parc d'attraction</option>
                        <option value="activite">Activité</option>
                    </select>
                    <br />
                </div>

                <?php

                ?>
                <div class="champTexte">
                    <!-- Liste déroulante -->
                    <label for="tags">Tags :</label>
                    <select id="tags" name="tags">
                        <option value="FR">Française</option>
                        <option value="hotel">Hotel</option>
                        <option value="chateau">Chateau</option>
                        <option value="musee">Musée</option>
                        <option value="visite">Visite</option>
                        <option value="chambrehote">Chambre d'hôtes</option>
                        <option value="autre">Autre</option>
                    </select>
                    <br />
                </div>
        </div>
        <div>
            <label for="resume">Résumé :</label>
            <textarea id="resume" name="resume" rows="5" cols="33" placeholder="Résumé de votre offre"></textarea>
            <br />

            <label for="descripDetail">Description détaillé :</label>
            <textarea id="resume" name="resume" rows="10" cols="60" placeholder="Description détaillé de votre offre"></textarea>
            <br />
        </div>

        </form>
    </div>
</body>

</html>