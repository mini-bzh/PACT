<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/pages/avis.css">
</head>
<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
    ?>>
        
    <?php include "../composants/header/header.php";        //import header (navbar)
    ?>
    <div class="titrePortable">

        <svg width="401" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg">
        <g filter="url(#filter0_d_169_4380)">
        <ellipse cx="169.5" cy="61" rx="231.5" ry="89" fill="white"/>
        </g>
        <defs>
        <filter id="filter0_d_169_4380" x="-66" y="-28" width="471" height="186" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
        <feFlood flood-opacity="0" result="BackgroundImageFix"/>
        <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
        <feOffset dy="4"/>
        <feGaussianBlur stdDeviation="2"/>
        <feComposite in2="hardAlpha" operator="out"/>
        <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
        <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_169_4380"/>
        <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_169_4380" result="shape"/>
        </filter>
        </defs>
        </svg>

        <div id="conteneurTitreMobile">
            <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
            <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
            <h3>Mes avis</h3>
        </div>
    </div>
    <main>
        <h1>Coming soon !</h1>
    </main>
    <?php                                                   //import footer
            include "../composants/footer/footer.php";
    ?>
</body>
</html>