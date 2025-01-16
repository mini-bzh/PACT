<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="/style/pages/compte.css">
    <!-- <link rel="stylesheet" href="/style/pages/ChoixCreationCompte.css"> -->
</head>

<?php include "../composants/header/header.php";        //import navbar
        ?>


<body id="body-choixCreaCompte" class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>

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
        <h4>Choix creation<br>compte</h4>
    </div>
</div>

<!------ MAIN  ------>

<main>


<div class="pageChoixCo">
    <p class="texteLarge">Choisir entre un compte</p>
<?php
    include '../composants/btnConnexion/btnCreaCompteMembre.php';
    include '../composants/btnConnexion/btnCreaComptePro.php';
?>

</div>

</main>
</body>


<?php
    include "../composants/footer/footer.php";
?>
</html>