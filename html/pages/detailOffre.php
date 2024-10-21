<?php
    $idOffre = null;
    $user = null;
    if(key_exists("idOffre", $_GET))
    {
        $idOffre =$_GET["idOffre"];
    }
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>détail offre</title>
    <link rel="stylesheet" href="../style/pages/detailOffre.css">
</head>
    <body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($user == "pro")
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>
        <?php
            include "../composants/header/header.php";
        ?>
        <main class="conteneurOffre">
                <article class="offre">
                    <h2>Fort la Latte</h2>
                    <p>Visite</p>
                    <div class="conteneurSpaceBetween">
                        <div class="noteDetailOffre">
                            <div class="etoiles">
                                <p>4.7</p>
                                <img src="../icones/etoilePleineSVG.svg" alt="etoile pleine">
                                <img src="../icones/etoilePleineSVG.svg" alt="etoile pleine">
                                <img src="../icones/etoilePleineSVG.svg" alt="etoile pleine">
                                <img src="../icones/etoilePleineSVG.svg" alt="etoile pleine">
                                <img src="../icones/etoileMoitiePleineSVG.svg" alt="etoile moitié pleine">
                            </div>
                            <p>38 avis</p>
                        </div>
                        <div class="conteneurSVGtexte">
                            <img src="../icones/logoUserSVG.svg" alt="pro">
                            <p>Fort la Latte</p>
                        </div>
                    </div>

                    <div class="imgChg">
                        <img src="../images/images_illsutration_tempo/fort_la_latte/carrou_fort1.jpg" alt="" id="imageChangeante">
                    </div>
                    <div class="resumePrixDetailOffre">
                        <p>Le chateau de la roche, Fort la Latte, situé à Plévenon cap Fréhel</p>
                        <hr>
                        <p>À partir de 7.50€/pers</p>
                    </div>

                    <p id="descriptionOffre">
                    Le Fort La Latte, construit au XIVe siècle, est un château fort majestueux situé en Bretagne, sur une falaise face à 
                    la mer. Entouré de remparts et de tours, il surplombe la Côte d'Émeraude et offre des panoramas spectaculaires. Ce 
                    lieu emblématique attire de nombreux visiteurs pour son histoire et son cadre pittoresque.
                    </p>
                
                    <div class="conteneurSpaceBetween" id="conteneurTagsHoraires">
                        <div id="partieTags">
                            <div class="conteneurSVGtexte">
                                <img src="../icones/tagSVG.svg" alt="icone tag">
                                <h4>Tags</h4>
                            </div>
                            <hr>
                            <div id="conteneurTagsOffre">
                                <p class="tagOffre">Culturel</p>
                                <p class="tagOffre">Histoire</p>
                                <p class="tagOffre">Patrimoine</p>
                                <p class="tagOffre">Famille</p>


                            </div>
                        </div>
                        <div id="partieHoraires">
                            <div class="conteneurSVGtexte">
                                <img src="../icones/horairesSVG.svg" alt="icone horaires">
                                <h4>Horaires</h4>
                            </div>
                            <hr>
                            <div id="conteneurJoursOffre">
                                <p class="jour jourOuvert">L</p>
                                <p class="jour jourOuvert">Ma</p>
                                <p class="jour jourOuvert">Me</p>
                                <p class="jour jourOuvert">J</p>
                                <p class="jour jourOuvert">V</p>
                                <p class="jour jourFerme">S</p>
                                <p class="jour jourFerme">D</p>
                            </div>
                                <div id="conteneurPlagesHoraires">
                                    <p class="plageHoraire">De <span class="horaireEncadre">07h30</span> à <span class="horaireEncadre">19h00</span></p>
                            </div>
                        </div>
                    </div>
                    <a href="https://www.google.com/maps/search/?api=1&query=Fort%20La%20Latte%20-%20Ch%C3%A2teau%20de%20la%20Roche%20Goyon"
                    class="conteneurSVGtexte" id="itineraire" target="_blank">
                        <img src="../icones/adresseSVGblanc.svg" alt="icone adresse">
                        <p>Itinéraire</p>
                    </a>
                </article>
        </main>
        <?php                                                   //footer
            echo file_get_contents("../composants/footer/footer.php");
        ?>
    </body>
    <script src="../js/scriptImageChangeante.js"></script>
</html>