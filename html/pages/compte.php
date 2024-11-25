<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');

    // cree $compteMembre qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_membre.php');

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
    <link rel="stylesheet" href="/style/pages/compte.css">
    <script src="/js/deconnexion.js"></script>
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
                <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">

<?php
                if(($comptePro) || ($compteMembre))
                {
?>
                <h3>Profil</h3>
<?php
                } 
                else
                {
?>
                <h3>Connexion</h3>
                <?php } ?>
            </div>
        </div>

<!-- SI C'EST UN VISITEUR !!! -->
<?php

if ((!$comptePro) && (!$compteMembre)) {
?>

<!------ MAIN  ------>
<main>

    <div class="pageChoixCo">
        <div class="textBulle decaleBulleGauche">
            <p>Veuillez sélectionner une option de connexion</p>
        </div>

        <div>
<?php
            include '../composants/btnConnexion/btnCoMembre.php';
            include '../composants/btnConnexion/btnCoPro.php';
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
            include '../composants/btnConnexion/btnNouvCo.php';
?>
        </div>

    </div>


</main>

<!-- SI C'EST UN UTILISATEUR CONNECTÉ !!! -->

<?php
} else {
?>

<!------ MAIN  ------>
<main>

<?php

    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    $idCompte = $_SESSION['idCompte'];

    if ($comptePro) {

        $stmt = $dbh->prepare("SELECT * from tripskell.pro_prive where id_c = :id");

        $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll();


        if (count($result) === 0) {

            $stmt = $dbh->prepare("SELECT * from tripskell.pro_public where id_c = :id");

            $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

            $stmt->execute();
            $result = $stmt->fetchAll();

        }
    } else {
        
        $stmt = $dbh->prepare("SELECT * from tripskell.membre where id_c = :id");

        $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll();

    }

    $infos = $result[0];


?>

<!-- div principale -->
<div class="informationsCompte">

    <!-- div des informations de compte -->
    <div class="zoneInfos">

        <!-- div de l'image et de l'identité -->
        <div class="infoId">
            <img class="circular-image" src="../images/pdp/<?php echo $infos['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
<?php
            if ($compteMembre){  // Si c'est un membre on affiche son nom / prénom / login
?>
            <div>
                <p class="boldArchivo titreLogin"><?php echo $infos["login"] ?></p>
                <div>
                    <p><span class="boldArchivo">Nom : </span><?php echo $infos["nom"] ?></p>
                    <p><span class="boldArchivo">Prenom : </span><?php echo $infos["prenom"] ?></p>
                </div>
            </div>
<?php
            } else {
?>
            <div>
                <p class="boldArchivo titreLogin"><?php echo $infos["raison_social"] ?></p>
<?php
                $siren = trim(chunk_split($infos["num_siren"], 3, " ")); // On ajoute un esapce entre 3 caractères
?>
                <p><span class="boldArchivo">Numéro SIREN : </span><?php echo $siren ?></p>
            </div>
<?php
            }
?>
        </div>

        <!-- div du reste des informations -->
        <div>

        <!-- On récupère la date au bon format de la date de création d'une offre-->
<?php
            // On récupère la date
            $dateString = $infos["date_crea_compte"];

            // On créer un objet DateTime à partir de la chaîne
            $date = new DateTime($dateString);

            // On formater la date au format dd/mm/aaaa
            $formattedDate = $date->format('d/m/Y');
?>
            <p><span class="boldArchivo">Création compte : </span><?php echo $formattedDate ?></p>

        <!-- On récupère le type du compte-->
<?php
        //Si c'est un membre, on met membre
        if ($compteMembre) {
            $tCompte = "Membre";
        // Si c'est un professionnel
        } else {
            // On regarde si c'est un professionnel privé
            if(count($dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0]) !== 0) {
                $tCompte = "Professionnel privé";

            // Sinon c'est un professionnel public
            } else {
                $tCompte = "Professionnel public";
            }
        }
?>
            <p><span class="boldArchivo">Type de compte : </span><?php echo $tCompte ?></p>

            <!-- Email -->
            <p><span class="boldArchivo">E-mail : </span><?php echo $infos["adresse_mail"] ?></p>

            <!-- Adresse -->
<?php
            if ($compteMembre) {
?>
            <p><span class="boldArchivo">Adresse postal : </span><?php echo $infos["codepostal"] ?></p>
<?php
            } else {

            $adrPro = $infos["numero"] . " " . $infos["rue"] . ", " . $infos["ville"] . " " . $infos["codepostal"]; // Variable pour concaténer les informations de l'adresse d'un pro
?>
            <p><span class="boldArchivo">Adresse : </span><?php echo $adrPro ?></p>
<?php
            }
?>
            <!-- Téléphone -->
<?php
            $tel = trim(chunk_split($infos["numero_tel"], 2, " ")); // On rajoute un espace entre 2 caractères
?>
            <p><span class="boldArchivo">Téléphone : </span><?php echo $tel ?></p>

            <!-- Mot de passe (caché) -->
<?php
            $cache = str_repeat("*", strlen($infos["mot_de_passe"])); // On créer une variable qui possède autant de "*" que le nombre de lettres du mdp
?>
            <p><span class="boldArchivo">Mot de passe : </span><?php echo $cache ?></p>

<?php
            // Si c'est un pro, on rajoute le mode de payement
            if ($comptePro) {
?>
            <p><span class="boldArchivo">Mode de payement : </span><?php echo "plus tard" ?></p>
<?php
            }
?>
        </div>

    </div>


    <!-- div des boutons de compte -->
    <div class="zoneBtn">

        <!-- Bouton de deconnexion -->
        <button class="btnDeconnexion" onclick="confDeco()">
        <?php
            include '../icones/deconnexionSVG.svg';
        ?>
            <p class="boldArchivo">Déconnexion</p>
        </button>

    </div>

</div>






<!-- POP-UP de deconnexion -->
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
    include "../composants/footer/footer.php";
?>

</body>

</html>