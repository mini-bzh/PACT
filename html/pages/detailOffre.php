<?php
    $idOffre = null;
    $user = null;
    if(in_array("idOffre", $_GET))
    {
        $idOffre =$_GET["idOffre"];
    }
    if(in_array("user", $_GET))
    {
        $idOffre =$_GET["user"];
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>détail offre</title>
    <link rel="stylesheet" href="../style/style.css">
</head>
    <body  class=<?php
            if ($user = "pro") 
            {
                
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>
        <main>
            <div class="conteneurOffre">
                <article class="offre">
                    <h2>Fort la Latte</h2>
                    <div class="conteneurSpaceBetween">
                        <div class="noteDetailOffre">
                            <div class="etoiles">
                                <p>4.7</p>
                                <img src="../icones/etoilePleineSVG.svg" alt="">
                                <img src="../icones/etoilePleineSVG.svg" alt="">
                                <img src="../icones/etoilePleineSVG.svg" alt="">
                                <img src="../icones/etoilePleineSVG.svg" alt="">
                                <img src="../icones/etoileMoitiePleineSVG.svg" alt="">
                            </div>
                            <p>38 avis</p>
                        </div>
                        <div class="conteneurSVGtexte">
                            <img src="../icones/logoUserSVG.svg" alt="pro">
                            <p>Famille Jouons-les-Longrais</p>
                        </div>
                        <div class="imageChangeante">
                            <img src="../images/images_illsutration_tempo/fort_la_latte/carrou_fort1.jpg" alt="" id="imageChangeante">
                        </div>
                    </div>
                </article>
            </div>
        </main>
    </body>
    <script src="../../js/scriptImageChangeante.js"></script>
</html>