<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="style/style.css">
</head>
<body class="fondVisiteur">
    <?php $header = file_get_contents("composants/header/header.php") ;
        echo $header ;?>
    <main>
        ercher
        <h1>Non esse occaecat pariatur commodo.</h1>
        <h2>Non esse occaecat pariatur commodo.</h2>
        <h3>Non esse occaecat pariatur commodo.</h3>
        <h4>Non esse occaecat pariatur commodo.</h4>
        <h5>Non esse occaecat pariatur commodo.</h5>
        <p>Do laborum Lorem proident cupidatat id esse Lorem.</p>

        <button class="coMembre"><p class="texteLarge">Connexion</p></button>




        <div class="rectangleTitre">
            <svg width="30" height="100" xmlns="http://www.w3.org/2000/svg">
                <rect width="30" height="100" x="10" y="10" />
            </svg>

            <h2>Un test de titre</h2>
        </div>

        <article class="apercuOffrePC">
            <h3>fort la Latte</h3>
            <div class="conteneurSVGtexte">
                <img src="icones/logoUserSVG.svg" alt="pro">
                <p>Famille Jouons-les-Longrais</p>
            </div>
            <div class="conteneurSpaceBetween">
                <p>Château</p>
                <p class="ouvert">Ouvert</p>
            </div>

            <div class="conteneurImage">
                <img src="images/images_illsutration_tempo/fort la latte/carrou_fort1.jpg" alt="fort la latte">
                <div class="text-overlay">dès 7€/pers</div>
            </div>
            
            <p>Le Fort La Latte, construit au XIVe siècle, est un château fort majestueux situé en Bretagne, sur une 
            falaise face à la mer.</p>

            <div class="conteneurSVGtexte">
                <img src="icones/adresseSVG.svg" alt="adresse">
                <p>Cap Fréhel</p>
            </div>
            <div class="conteneurSpaceBetween">
                <div class="etoiles">
                    <p>4.7</p>
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoileMoitiePleineSVG.svg" alt="">
                </div>
                <p>439 avis</p>
            </div>
        </article>

        <article class="apercuOffrePC miseEnExergue">
            <h3>Manoir de lan Kerralec</h3>
            <div class="conteneurSVGtexte">
                <img src="icones/logoUserSVG.svg" alt="pro">
                <p>Famille Daubé</p>
            </div>
            <div class="conteneurSpaceBetween">
                <p>Restaurant</p>
                <p class="fermeBientot">Ferme bientôt</p>
            </div>
            <img src="images/images_illsutration_tempo/manoir/carrou_manoir3.webp" alt="fort la latte">
            <p>Dans un cadre idyllique, en plein cœur des Côtes d'Armor.</p>
            <div class="conteneurSVGtexte">
                <img src="icones/adresseSVG.svg" alt="adresse">
                <p>Cap Fréhel</p>
            </div>
            <div class="conteneurSpaceBetween">
                <div class="etoiles">
                    <p>4.7</p>
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoilePleineSVG.svg" alt="">
                    <img src="icones/etoileMoitiePleineSVG.svg" alt="">
                </div>
                <p>439 avis</p>
            </div>
        </article>
    </main>
</body>
</html>
