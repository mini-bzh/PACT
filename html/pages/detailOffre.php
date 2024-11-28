<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');
    include('../php/verif_categorie.php');
    

    $user = null;
    if(key_exists("idOffre", $_GET))
    {
        // reccuperation de id de l offre
        $idOffre =$_GET["idOffre"]; 
        
        // reccuperation du contenu de l offre

        $contentOffre   = $dbh->query("select * from tripskell.offre_visiteur where idoffre='" . $idOffre . "';")->fetchAll()[0];
        $ouverture      = $dbh->query("select * from tripskell._ouverture where idoffre='" . $idOffre . "';")->fetchAll();
        print_r($ouverture);
        $horaire        = $dbh->query("select * from tripskell._horaire where id_hor=" . $ouverture[0]['id_hor'] . ";")->fetchAll()[0];
        print_r($horaire);
        $avis           = $dbh->query("select * from tripskell.avis where idoffre='" . $idOffre . "';")->fetchAll();
        print_r($avis);

        $categorie = categorie($idOffre);
        
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>détail offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/pages/detailOffre.css">
</head>
    <body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>
        <?php
            // ajout du header
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
                <img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">
            </div>
        </div>
        <main class="mainDetail">
            <section class="conteneurOffreAvis">
                <section class="conteneurOffre">
                    <article class="offre">
                        <h2><?php echo $contentOffre["titreoffre"];?></h2>
                        <!-- <p>Visite</p> future categorie -->
                        <div class="conteneurSpaceBetween">
                            <div class="noteDetailOffre">
                                <div class="etoiles">
                                    <p><?php echo $contentOffre["note"];?></p> <!-- affichage de la note -->
                                    <?php
                                    //
                                    //  affichage de la note avec des etoiles
                                    //
                                        include "../php/etoiles.php";
                                    ?>
                                </div>
                                <!-- <p>38 avis</p> -->
                                <p> Categorie : <?php echo $categorie ; ?></p>
                            </div>
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $contentOffre["id_c"] . "';")->fetchAll()[0]["raison_social"]; ?></p>
                            </div>
                        </div>
                        <div class="imgChg">
                            <!-- image de l'offre -->
                            <img src="/images/imagesOffres/<?php echo $contentOffre["img1"]; ?>" alt="" id="imageChangeante">
                        </div>
                        <div class="resumePrixDetailOffre">
                            <!-- Resume -->
                            <p><?php echo $contentOffre["resume"];?></p>
                            <hr>
                            <!-- Tarif minimal -->
                            <p>À partir de <?php echo $contentOffre["tarifminimal"];?>€/pers</p>
                        </div>

                        <!-- Offre detaille -->
                        <p id="descriptionOffre"><?php echo $contentOffre["description_detaille"]; ?></p>
                    
                        <div class="conteneurSpaceBetween" id="conteneurTagsHoraires">
                            <div id="partieTags"><!-- future tag -->
                                <div class="conteneurSVGtexte">
                                    <img src="/icones/tagSVG.svg" alt="icone tag">
                                    <h4>Tags</h4>
                                </div>
                                <hr> 
                                <div id="conteneurTagsOffre">
                                    <p class="tagOffre">Culturel</p>
                                    <p class="tagOffre">Histoire</p>
                                    <p class="tagOffre">Patrimoine</p>
                                    <p class="tagOffre">Famille</p>


                                </div>
                            </div> 
                            <div id="partieHoraires">
                                <div class="conteneurSVGtexte">
                                    <img src="/icones/horairesSVG.svg" alt="icone horaires">
                                    <h4>Horaires</h4>
                                </div>
                                <hr><!-- future jours d'ouverture -->
                                <div id="conteneurJoursOffre">
                                    <p class="jour jourOuvert">L</p>
                                    <p class="jour jourOuvert">Ma</p>
                                    <p class="jour jourOuvert">Me</p>
                                    <p class="jour jourOuvert">J</p>
                                    <p class="jour jourOuvert">V</p>
                                    <p class="jour jourFerme">S</p>
                                    <p class="jour jourFerme">D</p>
                                </div>

                                <!-- Horaires -->
                                <div id="conteneurPlagesHoraires">
                                    <p class="plageHoraire">De <span class="horaireEncadre"><?php  ?></span> à <span class="horaireEncadre"><?php ?></span></p>
                                </div>
                            </div>
                        </div>
                        <a href="https://www.google.fr/maps/place/<?php echo $contentOffre["ville"]?>"
                        class="conteneurSVGtexte grossisQuandHover" id="itineraire" target="_blank">
                            <img src="/icones/adresseSVGblanc.svg" alt="icone adresse">
                            <p>Itinéraire</p>
                        </a>
                    </article>
                </section>
                <h1>Avis</h1>
                <section class="conteneurAvis">
                    <?php
                    foreach ($avis as $key => $avisM) {
                        $membre = $dbh->query("select * from tripskell.membre where id_c=" . $avis[$key]['id_c'] . ";")->fetchAll()[0];
                    ?>
                    <article class="avis">
                        <div class="conteneurMembreAvis">
                            <img class="circular-image" src="../images/pdp/<?php echo $membre['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                            <div class="infoMembreAvis">
                                <h3><?php echo $membre['login'] ?></h3>
                                <p>Experience datant du : <?php echo $avis[$key]['dateexperience']?></p>
                                <p>Contexte : <?php echo $avis[$key]['cadreexperience']?></p>
                            </div>
                        </div>
                        <h3 class="titreAvis"><?php echo $avis[$key]['titreavis'] ?></h3>
                        <div class="conteneurAvisTexte">
                            <p><?php echo $avis[$key]['commentaire'] ?></p>
                        </div>
                        <?php
                        if($avis[$key]["imageavis"] != null){
                        ?>
                        <div class="conteneurAvisImage">
                            <img src="/images/imagesAvis/<?php echo $avis[$key]['imageavis'] ?>" alt="image de l'avis">
                        </div>
                        <?php
                        }
                        ?>
                    </article>
                    <?php
                        $membre++;
                    }
                    ?>
                </section>
            </section>
        </main>
        <?php                                                   
            // ajout du footer
            include "../composants/footer/footer.php";
        ?>
    </body>
    <!-- <script src="/js/scriptImageChangeante.js"></script> future carrousel d'image -->
</html>

<?php $dbh = null; // on ferme la connexion  ?>