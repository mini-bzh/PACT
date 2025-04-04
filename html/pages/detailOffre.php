<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../composants/bdd/connection_params.php');

    // contient fonction affichage_etoiles pour afficher les etoiles
    include('../composants/affichage/etoiles.php'); 
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../composants/verif/verif_compte_pro.php');
    include('../composants/verif/verif_compte_membre.php');

    include('../composants/verif/verif_categorie.php');
    
    include_once("../composants/affichage/affichageAvis.php");

        
    $user = null;
    if(key_exists("idOffre", $_GET))
    {
        // recuperation de id de l offre
        $idOffre =$_GET["idOffre"];
        
        // recuperation du contenu de l offre
        $contentOffre   = $dbh->query("select * from tripskell.offre_visiteur where idoffre='" . $idOffre . "';")->fetchAll()[0];
        $ouverture      = $dbh->query("select * from tripskell._ouverture where idoffre='" . $idOffre . "';")->fetchAll();
        if ($comptePro && $dbh->query("select count(*) from tripskell._offre  where idoffre='" . $idOffre . "' and id_c='". $_SESSION["idCompte"] ."';")->fetchAll()[0]["count"] == 1  ) {
            $avis       = $dbh->query("select * from tripskell._avis where idoffre='" . $idOffre . "';")->fetchAll();    
        } elseif ($compteMembre) { 
            $avis       = $dbh->query("select * from tripskell._avis where idoffre='" . $idOffre . "' and (date_recup_token_blacklist ISNULL or id_c='". $_SESSION["idCompte"] ."');")->fetchAll();
        } else {
            $avis       = $dbh->query("select * from tripskell._avis where idoffre='" . $idOffre . "' and (date_recup_token_blacklist ISNULL);")->fetchAll();
        }
        $tags           = $dbh->query("select * from tripskell._possede where idoffre='" . $idOffre . "';")->fetchAll();

        $categorie = 'visite';
        
    }

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Détail de l'offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
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
        <header>
            <div class="titrePortable">
                    <img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">
            </div>
        </header>
        <main class="mainDetail">
            <section class="conteneurOffreAvis">
                <section class="conteneurOffre">
                    <article class="offre">
                        <p hidden id="idOffreCache"><?php echo $idOffre?></p>
                        
                        <div id="headerOffre">
                            <div>
                                <h1><?php echo $contentOffre["titreoffre"];?></h1>
                                <!-- <p>Visite</p> future categorie -->
                                <div class="noteDetailOffre">
                                    <div class="etoiles">
                                        <!-- affichage de la note -->
                                        <?php affichage_etoiles($contentOffre["note"]); ?>
                                        <p>(<?php if (isset($contentOffre["note"])) { printf("%.1f", $contentOffre["note"]);} else {echo "aucun avis";}?>)</p>
                                    </div>
                                    <p> Catégorie : <span id="nomCat"><?php echo $categorie ; ?></span></p>
                                </div>
                            </div>
                            
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $contentOffre["id_c"] . "';")->fetchAll()[0]["raison_social"]; ?></p>
                            </div>
                        </div>
                        
                        <div class="imgResume">
                            <img src="/images/imagesOffres/<?php echo $contentOffre["img1"]; ?>" alt="" id="imageChangeante">

                            <div class="resumePrixDetailOffre">
                                <!-- Resume -->
                                <p><?php echo $contentOffre["resume"];?></p>
                                <hr>
                                <!-- Tarif minimal -->
                                <p>À partir de <?php echo $contentOffre["tarifminimal"];?>€/pers</p>
                            </div>
                        </div>
                        <!-- Offre detaille -->
                        <p id="descriptionOffre"><?php echo $contentOffre["description_detaille"]; ?></p>
                    
                        <div id="bodyOffre">
                            <div class="gaucheBodyOffre">
                                <div id="partieTags">
                                <!-- tag -->

                                    <div class="conteneurSVGtexte">
                                        <img src="/icones/tagSVG.svg" alt="icone tag">
                                        <h4>Tags</h4>
                                    </div>
                                    <hr> 
                                    <div id="conteneurTagsOffre">
                                        <?php
                                        foreach($tags as $key => $tag){
                                            echo "<p class='tagOffre'>" . $tag["nomtag"] . "</p>";
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div id="partieAdresse">
                                    <div class="conteneurSVGtexte">
                                        <img src="/icones/adresseSVG.svg" alt="icone tag">
                                        <h4>Adresse</h4>
                                    </div>
                                    <hr>
                                    <a href="https://www.google.fr/maps/place/<?php 
                                        $adresse = $contentOffre["numero"] . " rue " . $contentOffre["rue"] . ", " . $contentOffre["ville"];

                                        echo urlencode($adresse);
                                    ?>" class="conteneurSVGtexte" id="itineraire" target="_blank">
                                        <p><?php
                                            echo($adresse);
                                        ?></p>
                                    </a>
                                    
                                </div>
                            </div>
                            <div class="droiteBodyOffre">
                            <div id="partieHoraires">
                                <div class="conteneurSVGtexte">
                                    <img src="/icones/horairesSVG.svg" alt="icone horaires">
                                    <h4>Horaires</h4>
                                </div>
                                <hr>
                                <!-- affichage horaires et jours d'ouverture -->
                                <div id="conteneurJoursOffre">
                                    <table>
                                    <thead>
                                        <tr>
                                            <th>Jour</th>
                                            <th>Ouverture</th>
                                            <th>Fermeture</th>
                                            <th>Ouverture</th>
                                            <th>Fermeture</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                            foreach($ouverture as $value){
                                                $horaire = $dbh -> query("select * from tripskell._horaire as h join tripskell._ouverture as o on h.id_hor=". $value["id_hor"] ." where o.idOffre='". $idOffre."' and o.id_hor=". $value["id_hor"] ." and o.id_jour='". $value["id_jour"] ."';")->fetchAll();
                                        ?>
                                        <tr>
                                            <th><?php echo $value["id_jour"]; ?></th>
                                            <td><?php echo substr($horaire[0]['horaire_matin_debut'], 0, 5); ?></td>
                                            <td><?php echo substr($horaire[0]['horaire_matin_fin'], 0, 5); ?></td>

                                            <?php
                                            if(($horaire[0]['horaire_aprem_debut'] != NULL)&&($horaire[0]['horaire_aprem_fin'] != NULL)){
                                            ?>
                                            <td><?php echo substr($horaire[0]['horaire_aprem_debut'], 0, 5); ?></td>
                                            <td><?php echo substr($horaire[0]['horaire_aprem_fin'], 0, 5); ?></td>
                                            <?php
                                            }
                                            ?>
                                        </tr>
                                        <?php
                                            }
                                        ?>
                                    </tbody>
                                    </table>
                                </div>
                                    <div id="partieCategorie">
                                        <div class="conteneurSVGtexte">
                                            <!--<img src="/icones/.svg" alt="icone tag">-->
                                            <h4>Informations supplémentaires</h4>
                                        </div>
                                        <hr>
                                        <?php //print_r($contentOffre); ?>
                                        <section id="secRestaurant" class="displayNone">
                                            <p>Gamme de prix :<span class="boldArchivo"> <?php echo $contentOffre['gammeprix']; ?></span></p>
                                            <a href="../images/imagesCarte/<?php echo $contentOffre['carte']; ?>" target="_blank"><img src="../images/imagesCarte/<?php echo $contentOffre['carte']; ?>" alt="Menu"></a>
                                        </section>

                                        <section id="secParcAttr" class="displayNone">
                                            <p>Nombre d'attraction : <span class="boldArchivo"><?php echo $contentOffre['nbattraction']; ?></span></p>
                                            <p>Âge minimal : <span class="boldArchivo"><?php echo $contentOffre['agemin']; ?> ans</span></p>
                                            <a href="../images/imagesPlan/<?php echo $contentOffre['plans']; ?>" target="_blank"><img src="../images/imagesPlan/<?php echo $contentOffre['plans']; ?>" alt="Plan" class="plan"></a>
                                        </section>

                                        <section id="secSpec" class="displayNone">
                                            <p>Nombre de places maximum : <span class="boldArchivo"><?php echo $contentOffre['capacite']; ?></span></p>
                                            <?php
                                                $parts = explode(':', $contentOffre['duree_s']); // Divise en parties (hh, mm, ss)
                                                $formattedTime = $parts[0] . 'h ' . $parts[1] . 'm'; // Reformate
                                            ?>
                                            <p>Durée du spectacle : <span class="boldArchivo"><?php echo $formattedTime; ?></span></p>
                                        </section>

                                        <?php

                                        $stmt = $dbh->prepare("select * from tripskell._possedeLangue where idoffre='" . $idOffre . "';");
                                        $stmt->execute();
                                        $result = $stmt->fetchAll();

                                        // Extraire les valeurs de la colonne "nomlangue"
                                        $langues = array_column($result, 'nomlangue');

                                        // Combiner les éléments en une seule chaîne séparée par des virgules
                                        $languesStr = implode(', ', $langues);
                                        ?>
                                        <section id="secVisite" class="displayNone">
                                            <p>Langue(s) de la visite :<br><span class="boldArchivo"><?php echo $languesStr; ?></span></p>
                                            <p>La visite <span class="boldArchivo"><?php ($contentOffre['guidee']) ? "" : "n'" ?>est <?php ($contentOffre['guidee']) ? "" : "pas" ?><?php echo $contentOffre['capacite']; ?> guidée</span>.</p>
                                            <?php
                                                $parts = explode(':', $contentOffre['duree_v']); // Divise en parties (hh, mm, ss)
                                                $formattedTime = $parts[0] . 'h ' . $parts[1] . 'm'; // Reformate
                                            ?>
                                            <p>Durée de la visite : <span class="boldArchivo"><?php echo $formattedTime; ?></span></p>
                                        </section>

                                        <section id="secAct" class="displayNone">
                                            <p><span class="boldArchivo">Prestation(s) proposée(s) :</span><br><?php echo $contentOffre['prestation']; ?></p>
                                            <p>Âge minimal : <span class="boldArchivo"><?php echo $contentOffre['ageminimum']; ?> ans</span></p>
                                            <?php
                                                $parts = explode(':', $contentOffre['duree_a']); // Divise en parties (hh, mm, ss)
                                                $formattedTime = $parts[0] . 'h ' . $parts[1] . 'm'; // Reformate
                                            ?>
                                            <p>Durée de l'activité : <span class="boldArchivo"><?php echo $formattedTime; ?></span></p>
                                        </section>

                                    </div>
                                </div>
                            </div>
                            
                            
                        </div>
                    </article>
                </section>
                <!-- Avis -->

                <h1>Avis</h1>
                <section class="conteneurAvis">
                    <section id="conteneurTrie">
                        <div id="btnTrieDate" class="btnTrie grossisQuandHover" onclick="trierDate()">
                            <img src="/icones/trierSVG.svg" alt="iconeDate" id="iconeTrieDate" class="iconeTrie">
                            <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieDate1" class="iconeTrie displayNone">
                            <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieDate2" class="iconeTrie displayNone">
                            <p id="txtBtnDate" class="txtBtnTrie">date</p>
                        </div> 
                        <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                            <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieNote" class="iconeTrie">
                            <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieNote1" class="iconeTrie displayNone">
                            <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieNote2" class="iconeTrie displayNone">
                            <p id="txtBtnNote" class="txtBtnTrie" >note</p>
                        </div>
                    </section>
                        <?php
                            if(isset($_SESSION["idCompte"]) && $_SESSION["idCompte"] !== null && $compteMembre) {   
                                //reagrde si le membre a déjà publié un avis pour l'offre
                                $avisDejaAjoute = false;
                                $stmt = $dbh->prepare("select * from tripskell._avis where id_c = " . $_SESSION["idCompte"] . 
                                " and idOffre = " . $_GET["idOffre"]);

                                $stmt->execute();
                                $result = $stmt->fetchAll();

                                if(sizeof($result) > 0)
                                {
                                    $avisDejaAjoute = true;
                                }

                                ?>
                                <a <?php 
                                    if(!$avisDejaAjoute)
                                    {
                                        ?>
                                        href="creaAvis.php?idOffre=<?php echo $idOffre;?>";
                                        <?php
                                    }
                                ?> id="btnAjouterAvis" 
                                class="grossisQuandHover <?php       //ajoute la classe btnAvisGrisé quand le memebre a déjà ajouté un avis
                                        if($avisDejaAjoute)
                                        {
                                            echo ("btnAjouterAvisGrise");
                                        }
                                    ?>">
                                    <img src="../icones/ajouterSVG.svg" alt="ajouter">
                                    <h3>Ajouter un avis</h3>
                                </a>
                                <?php
                            }
                        ?>
                    <section class="conteneurAvis">
                        
                            
                        <!-- Code pour un avis -->
                        <div id="overlay">
                            <img src="" alt="image overlay"> <!-- src vide tant que aucune image n'est selectionnée -->
                            <div id="btnFermerOverlay">
                                <p>Fermer</p>
                            </div>
                        </div>
                        <?php

                        foreach ($avis as $avisM) 
                        {
                            afficheAvis($avisM);
                        }
                        ?>
                    </section>
                </section>
            </section>
        <?php dependances_avis(); ?>
        </main>
        <?php                                                   
            // ajout du footer
            include "../composants/footer/footer.php";
        ?>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="../js/detailOffre.js"></script>
        <script src="../js/affichageAvis.js"></script>
        <script src="../js/animationApparition.js"></script>
    </body>
</html>

<?php $dbh = null; // on ferme la connexion  ?>