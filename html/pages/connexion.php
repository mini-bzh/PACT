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
        $message1 = "<p>Ce login n'existe pas.</p>";
    }

    if (($correspond === false) && ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) && ((count($result) === 1) || (count($result2) === 1))){
        $message2 = "<p>Mot de passe incorrect.</p>";
    }
} else {
    if (($correspond === false) && (count($result) === 0) && ((isset($_POST['userName'])) && (isset($_POST['userPSW'])))) {
        $message1 = "<p>Ce login n'existe pas.</p>";
    }

    if (($correspond === false) && ((isset($_POST['userName'])) && (isset($_POST['userPSW']))) && ((count($result) === 1))){
        $message2 = "<p>Mot de passe incorrect.</p>";
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

<header>
    <div class="titrePortable">
            <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
            <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
            <h1>Connexion</h1>
    </div>
</header>


<!------ MAIN  ------>

<main id="mainConnexion">

<div id="overlayOTP">                               <!-- pop-up qui demande l'OTP si nécéssaire -->
    <div id="fenetreOTP">
        <p>Entrez votre OTP</p>
        <input type="text" inputmode="numeric" id="userOTP" name="userOTP" maxlength="6" placeholder="One Time Passord">
        <p id="texteErreurOTP">OTP invalide</p>
        <button id="btnConfirmerOTP">
            <p>Confirmer</p>
            <span class="loader"></span>
        </button>
        <button id="btnAnnulerOTP">
            <p>Annuler</p>
        </button>
    </div>
</div>

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
        <button type="submit" href="#" class="btnConnexion" id="btnConnexionForm">
            <p class="texteLarge boldArchivo">Se connecter</p>
        </button>

</form>
</main>

</body>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="../js/connexion.js"></script>

</html>
