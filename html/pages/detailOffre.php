<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('/var/www/html/php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('/var/www/html/php/verif_compte_pro.php');

    $user = null;
    if(key_exists("idOffre", $_GET))
    {
        // reccuperation de id de l offre
        $idOffre =$_GET["idOffre"]; 
        
        // reccuperation du contenu de l offre
        
        $contentOffre = $dbh->query("select * from tripskell.offre_visiteur where idoffre='" . $idOffre . "';")->fetchAll()[0];          
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
            include "/var/www/html/composants/header/header.php";
        ?>
        <main class="conteneurOffre">
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
                        <!-- future tag<div id="partieTags">
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
                        </div> -->
                        <div id="partieHoraires">
                            <div class="conteneurSVGtexte">
                                <img src="/icones/horairesSVG.svg" alt="icone horaires">
                                <h4>Horaires</h4>
                            </div>
                            <hr><!-- future jours d'ouverture
                            <div id="conteneurJoursOffre">
                                <p class="jour jourOuvert">L</p>
                                <p class="jour jourOuvert">Ma</p>
                                <p class="jour jourOuvert">Me</p>
                                <p class="jour jourOuvert">J</p>
                                <p class="jour jourOuvert">V</p>
                                <p class="jour jourFerme">S</p>
                                <p class="jour jourFerme">D</p>
                            </div>-->

                            <!-- Horaires -->
                            <div id="conteneurPlagesHoraires">
                                <p class="plageHoraire">De <span class="horaireEncadre"><?php echo explode("-",$contentOffre["horaires"])[0]; ?></span> à <span class="horaireEncadre"><?php echo explode("-",$contentOffre["horaires"])[1]; ?></span></p>
                            </div>
                        </div>
                    </div>
                    <a href="https://www.google.fr/maps/place/<?php echo $contentOffre["ville"]?>"
                    class="conteneurSVGtexte" id="itineraire" target="_blank">
                        <img src="/icones/adresseSVGblanc.svg" alt="icone adresse">
                        <p>Itinéraire</p>
                    </a>
                </article>
        </main>
        <?php                                                   
            // ajout du footer
            include "/var/www/html/composants/footer/footer.php";
        ?>
    </body>
    <!-- <script src="/js/scriptImageChangeante.js"></script> future carrousel d'image -->
</html>

<?php $dbh = null; // on ferme la connexion  ?>