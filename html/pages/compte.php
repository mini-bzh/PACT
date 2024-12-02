<?php

use Dompdf\Dompdf;

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

    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/compte.css">
    <script src="../js/deconnexion.js" defer></script>
    <script src="../js/menuDeroulant.js" defer></script>
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
            <div class="image-container">
                <img class="circular-image" src="../images/pdp/<?php echo $infos['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
            </div>
<?php
            // On récupère la date au bon format de la date de création d'une offre
            // On récupère la date
            $dateString = $infos["date_crea_compte"];

            // On créer un objet DateTime à partir de la chaîne
            $date = new DateTime($dateString);

            // On formater la date au format dd/mm/aaaa
            $formattedDate = $date->format('d/m/Y');

            // On change le mdp en étoile
            $cache = str_repeat("*", strlen($infos["mot_de_passe"])); // On créer une variable qui possède autant de "*" que le nombre de lettres du mdp

            // On rajoute un espace entre 2 caractères pour le téléphone
            $tel = trim(chunk_split($infos["numero_tel"], 2, " "));

            // On récupère le type du compte
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

                // Dans le cas d'un compte pro, on décompose son numéro SIREN
                $siren = trim(chunk_split($infos["num_siren"], 3, " ")); // On ajoute un esapce entre 3 caractères

                // On concatène les informations de l'adresse d'un pro
                $adrPro = $infos["numero"] . " " . $infos["rue"] . ", " . $infos["ville"] . " " . $infos["codepostal"];
            }

            if ($compteMembre){  // Si c'est un membre on affiche son nom / prénom / login
?>
            <div class="infoPrinc">
                <p class="boldArchivo titreLogin"><?php echo $infos["login"] ?></p>
                <div>
                    <p class="resizeHide"><span class="boldArchivo">Nom : </span><?php echo $infos["nom"] ?></p>
                    <p class="resizeHide"><span class="boldArchivo">Prenom : </span><?php echo $infos["prenom"] ?></p>
                    <p class="texteSmall resizeShow">Création compte : <?php echo $formattedDate ?></p>
                    <p class="texteSmall resizeShow">Type de compte : <?php echo $tCompte ?></p>
                </div>
            </div>
<?php
            } else {
?>
            <div class="infoPrinc">
                <p class="boldArchivo titreLogin"><?php echo $infos["raison_social"] ?></p>

                <div>
                    <p class="resizeHide"><span class="boldArchivo">Numéro SIREN : </span><?php echo $siren ?></p>

                    <p class="texteSmall resizeShow">Création compte : <?php echo $formattedDate ?></p>
                    <p class="texteSmall resizeShow">Type de compte :<br><?php echo $tCompte ?></p>
                </div>
            </div>
<?php
            }
?>

            <button class="resizeShow btnDeplie">
                <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M75 62.5L50 37.5L25 62.5" stroke="white" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
                <svg class="fleche2 displayNone" width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M75 62.5L50 37.5L25 62.5" stroke="white" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                </svg>
            </button>

        </div>

        <!-- div du reste des informations en format portable -->
        <div class="displayNone" id="menuDeroule">
            <div class="zoneInfosPort">
<?php
                if ($compteMembre){  // Si c'est un membre on affiche son nom / prénom
?>                
                <div>
                    <p class="boldArchivo displayNone">Nom : <?php echo $infos["nom"] ?></p>
                    <p class="boldArchivo displayNone">Prenom : <?php echo $infos["prenom"] ?></p>
                </div>
<?php
                } else {  // Si c'est un pro, son numéro SIREN
?>
                <div>
                    <p class="boldArchivo displayNone">Numéro SIREN : <?php echo $siren ?></p>
                </div>
<?php
                }
?>

                <!-- Mot de passe -->
                <p class="boldArchivo displayNone">Mot de passe : <?php echo $cache ?></p>

                <!-- Téléphone -->
                <p class="boldArchivo displayNone">Téléphone : <?php echo $tel ?></p>

                <!-- E-mail -->
                <p class="boldArchivo displayNone">Adresse mail :<br><?php echo $infos["adresse_mail"] ?></p>


                <!-- Adresse -->
                <?php
                if ($compteMembre) {
?>
                <p class="boldArchivo displayNone">Adresse postal : <?php echo $infos["codepostal"] ?></p>
<?php
                } else {
?>
                <p class="boldArchivo displayNone">Adresse :<br><?php echo $adrPro ?></p>
<?php
                }
?>
            </div>
            <!-- div des boutons -->
            <div class="zoneBtnPort">
                <!-- Bouton de modification portable -->
                <button class="btnModifPort displayNone">
                    <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M88.2249 28.3831C90.4279 26.1807 91.6657 23.1934 91.6661 20.0784C91.6665 16.9633 90.4294 13.9757 88.227 11.7727C86.0246 9.56976 83.0373 8.33193 79.9222 8.33154C76.8071 8.33115 73.8195 9.56823 71.6166 11.7706L16.0082 67.3915C15.0408 68.3561 14.3254 69.5437 13.9249 70.8498L8.42072 88.9831C8.31304 89.3435 8.3049 89.7263 8.39719 90.0909C8.48947 90.4554 8.67872 90.7883 8.94487 91.054C9.21102 91.3197 9.54414 91.5084 9.90888 91.6001C10.2736 91.6918 10.6564 91.6831 11.0166 91.5748L29.1541 86.0748C30.4589 85.6779 31.6464 84.9669 32.6124 84.004L88.2249 28.3831Z" stroke="black" stroke-width="5" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M62.5 20.8335L79.1667 37.5002" stroke="black" stroke-width="7" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                    <p class="boldArchivo displayNone">Modifier les informations</p>
                </button>

                <!-- Bouton de supression compte portable -->
                <button class="btnSupPort displayNone">
                    <svg width="100" height="100" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12.5 25H87.5" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M79.1666 25V83.3333C79.1666 87.5 74.9999 91.6667 70.8333 91.6667H29.1666C24.9999 91.6667 20.8333 87.5 20.8333 83.3333V25" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M33.3333 24.9999V16.6666C33.3333 12.4999 37.4999 8.33325 41.6666 8.33325H58.3333C62.4999 8.33325 66.6666 12.4999 66.6666 16.6666V24.9999" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M41.6667 45.8333V70.8333" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                        <path d="M58.3333 45.8333V70.8333" stroke="black" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>

                    <p class="boldArchivo displayNone">Supprimer le compte</p>
                </button>
            </div>

        </div>

        <!-- div du reste des informations en format desktop -->
        <div class="resteInfos">

            <p><span class="boldArchivo">Création compte : </span><?php echo $formattedDate ?></p>

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
?>
            <p><span class="boldArchivo">Adresse : </span><?php echo $adrPro ?></p>
<?php
            }
?>
            <!-- Téléphone -->

            <p><span class="boldArchivo">Téléphone : </span><?php echo $tel ?></p>

            <!-- Mot de passe (caché) -->

            <p><span class="boldArchivo">Mot de passe : </span><?php echo $cache ?></p>

        </div>

    </div>


    <!-- div des boutons de compte -->
    <div class="zoneBtn">

        <!-- div des boutons de consultation / modification données de compte-->
        <div>
            <!-- Bouton de modification -->
            <button class="btnModifCompte">
            <?php
                include '../icones/modifierSVG.svg';
            ?>
                <p class="boldArchivo texteSmall">Modifier le profil</p>
            </button>

            <!-- Bouton de données -->
            <button class="btnDataCompte">
            <?php
                include '../icones/databaseSVG.svg';
            ?>
                <p class="boldArchivo texteSmall">Télécharger les données du compte</p>
            </button>

<?php
            // On affiche le bouton de données bancaires si c'est un pro
            if ($comptePro) {
?>

            <!-- Bouton de données bancaires -->
            <button class="btnDataBanc">
            <?php
                include '../icones/creditCardSVG.svg';
            ?>
                <p class="boldArchivo texteSmall">Modifier les informations bancaires</p>
            </button>

            <!-- Bouton de données bancaires -->
            <button class="btnAccesFacture">
            <?php
                include '../icones/creditCardSVG.svg';
            ?>
                <a href="telechargementFacture.php"><p class="boldArchivo texteSmall">Télécharger les factures</p></a>
            </button>

<?php
            }
?>

<?php
            // On affiche le bouton du prix à payer pour le mois en cours
            if ($comptePro) {
?>

            <!-- Bouton prix -->
            <div class="btnDetailPrix">
                <p class="boldArchivo texteSmall">Montant total à payer</p>
            </div>

            <div class="detailPrixDeplie displayNone">
                <?php
                // connexion a la BdD
                $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
                $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

                // Creation requete pour recuperer les offres
                // du professionnel connecte
                $stmt = $dbh->prepare("select * from tripskell.offre_pro where id_c=:id_c;");

                // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
                $stmt->bindParam(":id_c", var: $id_c); 
                $id_c = $_SESSION["idCompte"];

                $stmt->execute();   // execution de la requete
                
                // recuperation de la reponse et mise en forme
                $contentMesOffres = $stmt->fetchAll();

                foreach($contentMesOffres as $contentOffre)                   // ajout des offres du professionnel récupérées plus tôt
                {
                    $prixOffre=[]; // Tableau pour stocker les prix d'une offre et de son option

                    $prixOffre['prixOffre'] = $contentOffre["prix_abo"];
                    $prixOffre['prixOption'] = $contentOffre["prix_option"];

                    $donneePrix[] = $prixOffre ; // Ajout des données de l'offre dans donneePrix
                    
                }
                ?>
                <p>Total à payer : 
                <?php 
                $total = 0;
                foreach ($donneePrix as $key => $value) {
                    $total += array_sum($value);
                }
                echo $total;
                ?>
                </p>
            </div>

<?php
            }
?>

        </div>

        <!-- div des boutons dangereux -->
        <div>

            <!-- Bouton de deconnexion -->
            <button class="btnDeconnexion" onclick="confDeco()">
            <?php
                include '../icones/deconnexionSVG.svg';
            ?>
                <p class="boldArchivo">Déconnexion</p>
            </button>

            <!-- Bouton de suppression compte -->
            <button class="btnSupCompte">
            <?php
                include '../icones/supprimerSVG.svg';
            ?>
                <p class="boldArchivo">Supprimer le compte</p>
            </button>
            
        </div>

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