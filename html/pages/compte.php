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
    <title>Compte</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/compte.css">
    <script src="../js/deconnexion.js"></script>
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

<!------ HEADER  ------>
<?php
    include "/var/www/html/composants/header/header.php";
?>

<!-- SI C'EST UN VISITEUR !!! -->
<?php

if (!$comptePro) {
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

    <div>
        <img src="/images/logo/logo_petit.png" alt="logo petit">
        <h3>Connexion</h3>
    </div>

</div>



<!------ MAIN  ------>
<main>

    <div class="pageChoixCo">
        <div class="textBulle decaleBulleGauche">
            <p>Veuillez séléctionner une option de connexion</p>
        </div>

        <div>
<?php
            include '/var/www/html/composants/btnConnexion/btnCoMembre.php';
            include '/var/www/html/composants/btnConnexion/btnCoPro.php';
?>
        </div>

        <hr>

        <div class="textBulle">
            <p><span>Pas encore de compte ?</span><br>
               Créez le !</p>
        </div>

        <div>
            <div class="fakeDiv"></div>
<?php
            include '/var/www/html/composants/btnConnexion/btnNouvCo.php';
?>
        </div>

    </div>


</main>

<?php
} else if ($comptePro) {
?>

<!-- SI C'EST UN PROFESSIONNEL !!! -->

<!------ MAIN  ------>
<main>

<button class="btnDeconnexion" onclick="confDeco()">
<?php
    include '../icones/deconnexionSVG.svg';
?>
    <p class="boldArchivo">Déconnexion</p>
</button>


<!-- POP-UP -->
<div class="popUpDeco">
    <div>
        <p>Êtes vous sur de vouloir vous déconnecter ?</p>
        <div>
            <button class="btnAnnuler" onclick="fermeConfDeco()">Non</button>
            <button class="btnValider" onclick="deconnexion()">OK</button>
        </div>
    </div>
</div>

</main>

<?php
}
?>

<!------ FOOTER  ------>

<?php
    include "/var/www/html/composants/footer/footer.php";
?>

</body>

</html>