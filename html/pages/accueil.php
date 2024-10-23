<?php
    $user = null;
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }

$driver = "pgsql";

$server = "postgresdb";
$dbname = "postgres";

$user = "sae";
$pass = "ashton-izzY-c0mplet";

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

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
                    <?php
                        foreach($dbh->query("SELECT * from tripskell.offre_visiteur") as $row) 
                        {
                            ?>
                                <a href="/pages/detailOffre.php?user=<?php echo $user?>" class="lienApercuOffre grossisQuandHover">
                                    <article class="apercuOffre">
                                        <h3><?php echo $row["titreoffre"]?></h3>
                                        <div class="conteneurSVGtexte">
                                            <img src="/icones/logoUserSVG.svg" alt="pro">
                                            <p><?php $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $contentOffre["id_c"] . "';")->fetchAll()[0]["raison_social"];?></p>
                                        </div>
                                        <div class="conteneurSpaceBetween">
                                            <p>Visite</p>
                                            <p class="ouvert">Ouvert</p>
                                        </div>
                            
                                        <div class="conteneurImage">
                                            <img src="/images/images_illsutration_tempo/fort_la_latte/carrou_fort1.jpg" alt="fort la latte">
                                            <div class="text-overlay">dès <?php echo $row["tarifminimal"]?>€/pers</div>
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