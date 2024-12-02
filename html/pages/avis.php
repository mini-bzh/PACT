<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');

    $idCompte = $_SESSION["idCompte"];

    if($comptePro){
        $offre = $dbh->query("select * from tripskell.offre_pro where id_c='" . $idCompte . "';")->fetchAll();
    }
    else{
        $membre = $dbh->query("select * from tripskell.membre where id_c='" . $idCompte . "';")->fetchAll()[0];
        $avis = $dbh->query("select * from tripskell._avis where id_c='" . $idCompte . "';")->fetchAll();
    }
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
            <h3 class="titrePrincipale">Mes avis</h3>
        </div>
    </div>
    <main>
    <?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
            ?>
              <section class="mainAvis">
              <section class="mainAvisPro">
                <?php
                foreach ($offre as $key => $value){
                    $avis = $dbh->query("select * from tripskell._avis where idOffre=" . $offre[$key]['idoffre'] . ";")->fetchAll();
                    if($avis != null){
                ?>
                <h2 class="titreOffre"><?php echo $offre[$key]['titreoffre']?></h2>
                <section class="conteneurAvis">
                    <?php
                    foreach ($avis as $key => $value){
                        $membre = $dbh->query("select * from tripskell.membre where id_c=" . $avis[$key]['id_c'] . ";")->fetchAll()[0];
                    ?>
                    <article class="avis">
                    <!-- Date de publication-->
                    <p class="datePublication"><?php echo $avis[$key]['datepublication']?></p>
                        <!-- Information du membre -->
                        <div class="conteneurMembreAvis">
                            <img class="circular-image" src="../images/pdp/<?php echo $membre['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                            <div class="infoMembreAvis">
                                <h3><?php echo $membre['login'] ?></h3>
                                <p>Experience datant du : <?php echo $avis[$key]['dateexperience']?></p>
                                <p>Contexte : <?php echo $avis[$key]['cadreexperience']?></p>
                            </div>
                        </div>
                        <!-- Titre de l'avis -->
                        <h3 class="titreAvis"><?php echo $avis[$key]['titreavis'] ?></h3>
                        <!-- Commentaire -->
                        <div class="conteneurAvisTexte">
                            <p class="texteAvis"><?php echo $avis[$key]['commentaire'] ?></p>
                        </div>
                        <!-- Image de l'avis -->
                        <?php
                        if($avis[$key]["imageavis"] != null){
                        ?>
                        <hr>
                        <div class="conteneurAvisImage">
                            <img src="/images/imagesAvis/<?php echo $avis[$key]['imageavis'] ?>" alt="image de l'avis">
                        </div>
                        <?php
                        }
                        ?>
                </article>
                    <?php
                    }
                    ?>
                </section>
                <?php
                    }
                }
                ?>
                </section>
                </section>
            <?php
            }
            else
            {
            ?>
                <section class="mainAvis">
                <section class="conteneurAvis">
                <?php
                foreach ($avis as $key => $value){
                    $offre = $dbh->query("select * from tripskell._offre where idoffre=" . $avis[$key]['idoffre'] . ";")->fetchAll()[0];

                ?>
                <h2 class="titreOffre"><?php echo $offre['titreoffre']?></h2>
                <article class="avis">
                    <!-- Date de publication-->
                    <p class="datePublication"><?php echo $avis[$key]['datepublication']?></p>
                        <!-- Information du membre -->
                        <div class="conteneurMembreAvis">
                            <img class="circular-image" src="../images/pdp/<?php echo $membre['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                            <div class="infoMembreAvis">
                                <h3><?php echo $membre['login'] ?></h3>
                                <p>Experience datant du : <?php echo $avis[$key]['dateexperience']?></p>
                                <p>Contexte : <?php echo $avis[$key]['cadreexperience']?></p>
                            </div>
                        </div>
                        <!-- Titre de l'avis -->
                        <h3 class="titreAvis"><?php echo $avis[$key]['titreavis'] ?></h3>
                        <!-- Commentaire -->
                        <div class="conteneurAvisTexte">
                            <p class="texteAvis"><?php echo $avis[$key]['commentaire'] ?></p>
                        </div>
                        <!-- Image de l'avis -->
                        <?php
                        if($avis[$key]["imageavis"] != null){
                        ?>
                        <hr>
                        <div class="conteneurAvisImage">
                            <img src="/images/imagesAvis/<?php echo $avis[$key]['imageavis'] ?>" alt="image de l'avis">
                        </div>
                        <?php
                        }
                        ?>
                </article>
                <?php
                }
                ?>
                </section>
                </section>
            <?php
            }
    ?>
    </main>
    <?php                                                   //import footer
            include "../composants/footer/footer.php";
    ?>
</body>
</html>