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
<body>
    <!-- HEADER -->

    <main>

        <div>
            <h2>Création d'une offre</h2>
            <div id="test">
                <!-- <form name="test" action="http://localhost:8888/import.php" method="get"> -->

                <div class="champTexte">
                    <label for="titre">Titre : <input type="text" id="titre" name="titre" size="20" /></label>
                    <br />
                </div>

                <div class="champTexte">
                    <label for="categorie">Catégorie :
                        <select id="categorie" name="categorie">
                            <option value="restauration">Restauration</option>
                            <option value="visite">Visite</option>
                            <option value="spectacle">Spectacle</option>
                            <option value="parcDatraction">Parc d'attraction</option>
                            <option value="activite">Activité</option>
                        </select>
                    </label>
                    <br />
                </div>

                <?php

                ?>
                <div class="champTexte">
                    <!-- Liste déroulante -->
                    <label for="tags">Tags :
                        <select id="tags" name="tags">
                            <option value="FR">Française</option>
                            <option value="hotel">Hotel</option>
                            <option value="chateau">Chateau</option>
                            <option value="musee">Musée</option>
                            <option value="visite">Visite</option>
                            <option value="chambrehote">Chambre d'hôtes</option>
                            <option value="autre">Autre</option>
                        </select>
                    </label>
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

            <!-- </form> -->
        </div>
    </main>
    <!-- footer -->
    <?php
    echo file_get_contents('../composants/footer/footer.php');
    ?>
</body>

</html>