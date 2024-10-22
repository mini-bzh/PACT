<?php
    $user = null;
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Accueil</title>
        <link rel="stylesheet" href="../style/pages/accueil.css">
    </head>
    <body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($user == "pro")
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
        <main>
            <?php
                if($user == "pro")
                {
                    ?>
                    <h1>Mes offres</h1>
                    <?php
                }
            ?>

            <section id="conteneurOffres">
                <article>
                    <a href="/pages/detailOffre.php?user=<?php echo $user?>" class="lienApercuOffre">
                        <article class="apercuOffre">
                            <h3>fort la Latte</h3>
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p>Fort la Latte</p>
                            </div>
                            <div class="conteneurSpaceBetween">
                                <p>Visite</p>
                                <p class="ouvert">Ouvert</p>
                            </div>
                
                            <div class="conteneurImage">
                                <img src="/images/images_illsutration_tempo/fort_la_latte/carrou_fort1.jpg" alt="fort la latte">
                                <div class="text-overlay">dès 7€/pers</div>
                            </div>
                            
                            <p class="resumeApercu">Le Fort La Latte, construit au XIVe siècle, est un château fort majestueux situé en Bretagne, sur une 
                            falaise face à la mer.</p>
                
                            <div class="conteneurSVGtexte">
                                <img src="/icones/adresseSVG.svg" alt="adresse">
                                <p>Cap Fréhel</p>
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
                    <a href="" class="lienApercuOffre">
                        <article class="apercuOffre">
                            <h3>Manoir de Lan Kerellec</h3>
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p>Famille Daubé</p>
                            </div>
                            <div class="conteneurSpaceBetween">
                                <p>Restaurant</p>
                                <p class="fermeBientot">Ferme bientôt</p>
                            </div>
                
                            <div class="conteneurImage">
                                <img src="/images/images_illsutration_tempo/manoir/carrou_manoir1.jpeg" alt="manoir">
                                <div class="text-overlay">€€€</div>
                            </div>
                            
                            <p class="resumeApercu">Hôtel familial avec chambres raffinées, restaurant élégant, jardins paysagers avec vue sur mer.</p>
                
                            <div class="conteneurSVGtexte">
                                <img src="/icones/adresseSVG.svg" alt="adresse">
                                <p>Trébeurden</p>
                            </div>
                            <div class="conteneurSpaceBetween">
                                <div class="etoiles">
                                    <p>4.6</p>
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoileMoitiePleineSVG.svg" alt="">
                                </div>
                                <p>360 avis</p>
                            </div>
                        </article>
                    </a>
                    <a href="" class="lienApercuOffre">
                        <article class="apercuOffre">
                            <h3>Armoripark</h3>
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p>Armoripark</p>
                            </div>
                            <div class="conteneurSpaceBetween">
                                <p>Parc de loisirs</p>
                                <p class="ferme">Fermé</p>
                            </div>
                
                            <div class="conteneurImage">
                                <img src="/images/images_illsutration_tempo/armoripark/riviere.jpg" alt="armoripark">
                                <div class="text-overlay">des 5€/pers</div>
                            </div>
                            
                            <p class="resumeApercu">Parc de loisirs en plein air dans les Côtes d'Armor, à Bégard.</p>
                
                            <div class="conteneurSVGtexte">
                                <img src="/icones/adresseSVG.svg" alt="adresse">
                                <p>Bégard</p>
                            </div>
                            <div class="conteneurSpaceBetween">
                                <div class="etoiles">
                                    <p>4.1</p>
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoilePleineSVG.svg" alt="">
                                    <img src="/icones/etoileVideSVG.svg" alt="">
                                </div>
                                <p>294 avis</p>
                            </div>
                        </article>
                    </a>
                </article>
            </section>
        </main>
        <?php
            include "../composants/footer/footer.php";
        ?>
    </body>
</html>