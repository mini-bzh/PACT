<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');

    if($comptePro)      /* prépare la requête pour récupérer les offres à afficher : offres du pro si connecté en tant que pro, toutes les 
                         offres sinon */

    {
        $stmt = $dbh->prepare("select * from tripskell.offre_pro where id_c=:id_c");

        // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
        $stmt->bindParam(":id_c", var: $id_c);
        $id_c = $_SESSION["idCompte"];
    }
    else
    {
        $stmt = $dbh->query("SELECT * from tripskell.offre_pro WHERE enligne = true");
    }

    $stmt->execute();
    $rows = $stmt->fetchAll();          // rows : les offres à afficher

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="/style/pages/accueil.css">
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
        <?php include "../composants/header/header.php";        //import navbar
        ?>
        <div class="titrePortable">

            <svg width="401" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg"> <!-- SVG pour  -->
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
        <main>
            <?php
                if($comptePro)                  //change le titre de la page
                {
                    ?>
                    <h1>Mes offres</h1>
                    <?php
                }
                else
                {
                    ?>
                    <h1>Toutes les offres</h1>
                    <?php
                }
            ?>

            <section id="conteneurOffres">
                <article>
                    <?php
                        foreach($rows as $row)          // parcourt les offres pour les afficher
                        {
                            ?>
                                <a <?php
                                    if($comptePro)      
                                    {
                                        ?>
                                            href="/pages/gestionOffres.php/#offre<?php echo $row['idoffre'];?>"
                                        <?php
                                    }
                                    else
                                    {
                                        ?>
                                            href="/pages/detailOffre.php?idOffre=<?php echo $row["idoffre"]?>"
                                        <?php
                                    }
                                ?>
                                href="/pages/detailOffre.php?idOffre=<?php echo $row["idoffre"]?>" class="lienApercuOffre grossisQuandHover">
                                    <article class="apercuOffre">
                                        <h3><?php echo $row["titreoffre"]?></h3>
                                        <div class="conteneurSVGtexte">
                                            <img src="/icones/logoUserSVG.svg" alt="pro">
                                            <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $row["id_c"] . "';")->fetchAll()[0]["raison_social"];?></p>
                                        </div>
                                        <div class="conteneurSpaceBetween">
                                            <p>Visite</p> <!-- catégorie -->
                                            <p class="ouvert">Ouvert</p>
                                        </div>
                            
                                        <div class="conteneurImage">
                                            <img src="/images/imagesOffres/<?php echo $row["img1"]?>" alt="illustration offre">
                                            <p class="text-overlay">dès <span><?php echo $row["tarifminimal"]?>€</span> /pers</p>
                                        </div>
                                        
                                        <p class="resumeApercu"><?php echo $row["resume"]?></p>
                            
                                        <div class="conteneurSVGtexte">
                                            <img src="/icones/adresseSVG.svg" alt="adresse">
                                            <p><?php echo $row["ville"]?></p>
                                        </div>
                                        <div class="conteneurSpaceBetween">
                                            <div class="etoiles">
                                                <p>4.7</p>
                                                <img src="/icones/etoilePleineSVG.svg" alt="">
                                                <img src="/icones/etoilePleineSVG.svg" alt="">
                                                <img src="/icones/etoilePleineSVG.svg" alt="">
                                                <img src="/icones/etoilePleineSVG.svg" alt="">
                                                <img src="/icones/etoileMoitiePleineSVG.svg" alt="">
                                            </div>
                                            <p>439 avis</p>
                                        </div>
                                    </article>
                                </a>
                            <?php
                        }
                    ?>
                </article>
            </section>
        </main>
        <?php
            include "../composants/footer/footer.php";
        ?>
    </body>
</html>