<?php 

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

if ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) {
    $username = $_POST['userName'];
    $password = $_POST['userPSW'];
}

$stmt = $dbh->prepare("SELECT * from tripskell.pro_prive where raison_social = :username");

$stmt->bindParam(':username', $username, PDO::PARAM_STR);

$stmt->execute();
$result = $stmt->fetchAll();

$stmt2 = $dbh->prepare("SELECT * from tripskell.pro_public where raison_social = :username");

$stmt2->bindParam(':username', $username, PDO::PARAM_STR);

$stmt2->execute();
$result2 = $stmt->fetchAll();

$correspond = false;

if (($correspond === false) && ($result)) {
    if ($password === $result[0]['mot_de_passe']) {
        $correspond = true;
    }
}

if (($correspond === false) && ($result2)) {
    if ($password === $result2[0]['mot_de_passe']) {
        $correspond = true;
    }
}

$message1 = "";
$message2 = "";

if ((empty($result)) && (empty($result2)) && ((isset($_POST['userName'])) && (isset($_POST['userPSW'])))) {
    $message1 = "<p style='color:red;'>Nom d'utilisateur incorrect.</p>";
}

// Traite si les logins sont corrects
if (($correspond === true)) {
?>
    <script>
        validerCorrect();
    </script>
<?php
} else if ($correspond === false && ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) && ((!empty($result)) && (!empty($result2)))){
    $message2 = "<p style='color:red;'>Mot de passe incorrect.</p>";
}


?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>
    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/connexion.css">
    <script src="../js/connexion.js" defer></script>
</head>
<body class=
<?php
    if ($_GET['user-tempo'] == 'pro') {
        echo 'fondPro';
    } else {
        echo 'fondVisiteur';
    }
?>
>

<!------ HEADER  ------>
<?php
    include "../composants/header/header.php";
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
        <img src="/html/images/logo/logo_petit.png" alt="logo petit">
        <h3>Connexion compte professionnel</h3>
    </div>
</div>


<!------ MAIN  ------>

<main>

<div class=textBulle>
    <p class="texteLarge">Connexion à un compte professionnel :</p>
</div>

<form action="" method="post">

    <div>
        <label for="userName"><p class="texteLarge">Nom d'entreprise :</p></label><br>
        <input type="text" id="userName" name="userName" maxlength="40" required>
    </div>

<?php
    echo $message1;
?>

    <div>
        <label for="userPSW"><p class="texteLarge">Mot de passe :</p></label><br>
        <input type="password" id="userPSW" name="userPSW" minlength="12" required>
    </div>

    <a href="#"><p class="texteSmall">Mot de passe oublié ?</p></a>

<?php
    echo $message2;
?>

    <div class="accepteSouvenir">
        <input type="checkbox" id="souvenir" name="souvenir">
        <p class="texteLarge">Se souvenir de moi</p>
    </div>

    <div class="zoneBtn">
        <a href="compte.php" class="btnAnnuler">
            <p class="texteLarge boldArchivo">Annuler</p>
<?php
            include '../icones/croixSVG.svg';
?>
        </a>

        <button type="submit" href="#" class="btnConfirmer">
            <p class="texteLarge boldArchivo">Confirmer</p>
<?php
            include '../icones/okSVG.svg';
?>

        </button>
    </div>

</form>

<!-- POP-UP -->
<div class="popUp">
    <div>
        <h2>Connexion réussie</h2>
    </div>
</div>

</main>

</div>  