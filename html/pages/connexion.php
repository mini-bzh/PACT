<?php
session_start();


$_SESSION['idCompte'] = null;       // met l'id de connextion a null pour eviter toute aproximation

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');


// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// Récupère les login si ils ont été entrés
if ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) {
    $username = $_POST['userName'];
    $password = $_POST['userPSW'];
}

if ($_GET['user-tempo'] == "pro") {
    $stmt = $dbh->prepare("SELECT * from tripskell.pro_prive where login = :username");

    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    $stmt->execute();
    $result = $stmt->fetchAll();

    $stmt2 = $dbh->prepare("SELECT * from tripskell.pro_public where login = :username");

    $stmt2->bindParam(':username', $username, PDO::PARAM_STR);

    $stmt2->execute();
    $result2 = $stmt2->fetchAll();

} else {
    $stmt = $dbh->prepare("SELECT * from tripskell.membre where login = :username");

    $stmt->bindParam(':username', $username, PDO::PARAM_STR);

    $stmt->execute();
    $result = $stmt->fetchAll();

}

$correspond = false;

// Voit si l'identifiant existe et correspond au mot de passe
if ($_GET['user-tempo'] == "pro") {
    if (($correspond === false) && ($result)) {
        if ($password === $result[0]['mot_de_passe']) {
            $correspond = true;
            $_SESSION['idCompte'] = $result[0]['id_c'];
        }
    }

    if (($correspond === false) && ($result2)) {
        if ($password === $result2[0]['mot_de_passe']) {
            $correspond = true;
            $_SESSION['idCompte'] = $result2[0]['id_c'];
        }
    }
} else{
    if (($correspond === false) && ($result)) {
        if ($password === $result[0]['mot_de_passe']) {
            $correspond = true;
            $_SESSION['idCompte'] = $result[0]['id_c'];
        }
    }
}

// Traite si les logins sont corrects
if ($correspond === true)
{
    header('Location: /pages/accueil.php');
}

$message1 = "";
$message2 = "";

// Affiche un message d'erreur à l'utilisateur selon son erreur
if ($_GET['user-tempo'] === 'pro') {
    if (($correspond === false) && (count($result) === 0) && (count($result2) === 0) && ((isset($_POST['userName'])) && (isset($_POST['userPSW'])))) {
        $message1 = "<p style='color:red;'>Ce login n'existe pas.</p>";
    }

    if (($correspond === false) && ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) && ((count($result) === 1) || (count($result2) === 1))){
        $message2 = "<p style='color:red;'>Mot de passe incorrect.</p>";
    }
} else {
    if (($correspond === false) && (count($result) === 0) && ((isset($_POST['userName'])) && (isset($_POST['userPSW'])))) {
        $message1 = "<p style='color:red;'>Ce login n'existe pas.</p>";
    }

    if (($correspond === false) && ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) && ((count($result) === 1))){
        $message2 = "<p style='color:red;'>Mot de passe incorrect.</p>";
    }
}

?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="/style/pages/connexion.css">

    <!-- <script src="../js/popUpmdpOublie.js" defer></script> -->
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

    <div id="conteneurTitreMobile">
        <img src="/images/logo/logo_petit.png" alt="logo petit">
        <h2>Connexion compte<br>professionnel</h2>
    </div>
</div>


<!------ MAIN  ------>

<main id="mainConnexion">

<!-- Formulaire de connexion -->
<form action="" method="post">
    <p class="texteLarge centerText"> Se connecter à un compte 
<?php
    if ($_GET["user-tempo"] === 'pro') {
        echo 'professionnel';
    } else {
        echo 'membre';
    }
?>
    </p>
    <div>
        <!--<label for="userName"><p class="texteLarge"></p></label>-->
        <input type="text" id="userName" name="userName" maxlength="40" placeholder="
<?php
    if ($_GET['user-tempo'] == "pro"){
            echo "Login de l'entreprise";
    } else {
            echo "Nom d'utilisateur";
    }
?>
        " required>


<!-- Ecrit le message "utilisateur inexistant si nécessaire" -->
<?php
    echo $message1;
?>

        <!--<label for="userPSW"><p class="texteLarge"></p></label>-->
        <input type="password" id="userPSW" name="userPSW" minlength="12" placeholder="Mot de passe" required>
    </div>

<!-- Ecrit le message "mot de passe incorrect" si nécessaire -->
<?php
    echo $message2;
?>
        <button type="submit" href="#" class="btnConnexion">
            <p class="texteLarge boldArchivo">Se connecter</p>
        </button>
    </div>

</form>

<!-- Pop-up du formulaire de demande de réinitialisation -->
<div id="resetForm" class="displayNone">
    <form action="../php/reset_request.php" method="POST">
        <div>
            <label for="recupLogin"><p>Login :</p></label>
            <input type="recupLogin" name="recupLogin" id="recupLogin" required>
        </div>
        <div>
            <label for="recupMail"><p>Adresse email :</p></label>
            <input type="recupMail" name="recupMail" id="recupMail" required>
        </div>
        <button type="submit"><p class="boldArchivo">Envoyer un email de<br>réinitialisation</p></button>
    </form>
</div>

</main>

</body>
<script>
        let btnConnexion =document.querySelector(".btnConnexion");
        console.log(btnConnexion);
        if(btnConnexion != undefined)
        {
            btnConnexion.addEventListener("click", ()=>{
                //supprime les cookies des pouces pour éviter qu'ils se conservent entre les comptes
                document.cookie = "poucesAvis=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/;SameSite=Lax";
                console.log(document.cookie);
            })
        }

    </script>

</html>
