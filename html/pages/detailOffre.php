<?php
    // reccuperation des parametre de connection a la BdD
    include('/var/www/html/php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

    $idOffre = null;
    $user = null;
    if(key_exists("idOffre", $_GET))
    {
        // reccuperation de id de l offre
        $idOffre =$_GET["idOffre"]; 
        
        // reccuperation du contenu de l offre
        $contentOffre = $dbh->query("select * from tripskell.offre_visiteur where idoffre='" . $idOffre . "';")->fetchAll()[0];          
    }
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>détail offre</title>
    <link rel="stylesheet" href="/style/pages/detailOffre.css">
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
        <?php
            include "/var/www/html/composants/header/header.php";
        ?>
        <main class="conteneurOffre">
                <article class="offre">
                    
                    <h2><?php echo $contentOffre["titreoffre"];?></h2>
                    <p>Visite</p>
                    <div class="conteneurSpaceBetween">
                        <div class="noteDetailOffre">
                            <div class="etoiles">
                                <p><?php echo $contentOffre["note"];?></p> <!-- affichage de la note -->
                                <?php
                                //
                                //  affichage de la note avec des etoiles
                                //
                                    include "/var/www/html/php/etoiles.php";
                                ?>
                            </div>
                            <!-- <p>38 avis</p> -->
                        </div>
                        <div class="conteneurSVGtexte">
                            <img src="/icones/logoUserSVG.svg" alt="pro">
                            <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $contentOffre["id_c"] . "';")->fetchAll()[0]["raison_social"]; ?></p>
                        </div>
                    </div>

                    <div class="imgChg">
                        <img src="/images/images_illsutration_tempo/fort_la_latte/carrou_fort1.jpg" alt="" id="imageChangeante">
                    </div>
                    <div class="resumePrixDetailOffre">
                        <p><?php echo $contentOffre["resume"];?></p>
                        <hr>
                        <p>À partir de <?php echo $contentOffre["tarifminimal"];?>€/pers</p>
                    </div>

                    <p id="descriptionOffre"><?php echo $contentOffre["description_detaille"]; ?></p>
                
                    <div class="conteneurSpaceBetween" id="conteneurTagsHoraires">
                        <div id="partieTags">
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
                            <hr><!--
                            <div id="conteneurJoursOffre">
                                <p class="jour jourOuvert">L</p>
                                <p class="jour jourOuvert">Ma</p>
                                <p class="jour jourOuvert">Me</p>
                                <p class="jour jourOuvert">J</p>
                                <p class="jour jourOuvert">V</p>
                                <p class="jour jourFerme">S</p>
                                <p class="jour jourFerme">D</p>
                            </div>-->
                            <div id="conteneurPlagesHoraires">
                                <p class="plageHoraire">De <span class="horaireEncadre"><?php echo explode("-",$contentOffre["horaires"])[0]; ?></span> à <span class="horaireEncadre"><?php echo explode("-",$contentOffre["horaires"])[1]; ?></span></p>
                            </div>
                        </div>
                    </div>
                    <!-- <a href="https://www.google.com/maps/search/?api=1&query=Fort%20La%20Latte%20-%20Ch%C3%A2teau%20de%20la%20Roche%20Goyon"
                    class="conteneurSVGtexte" id="itineraire" target="_blank">
                        <img src="/icones/adresseSVGblanc.svg" alt="icone adresse">
                        <p>Itinéraire</p>
                    </a> -->
                </article>
        </main>
        <?php                                                   //footer
            include "/var/www/html/composants/footer/footer.php";
        ?>
    </body>
    <script src="/js/scriptImageChangeante.js"></script>
</html>

<?php $dbh = null; // on ferme la connexion  ?>