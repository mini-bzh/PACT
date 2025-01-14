<?php

    function afficheAvis($avis)
    {

        // recuperation des parametre de connection a la BdD
        include('../php/connection_params.php');
        
        // connexion a la BdD
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

        $membre = $dbh->query("select * from tripskell.membre where id_c=" . $avis['id_c'] . ";")->fetchAll()[0];
        ?>
        <article id="Avis<?php echo $avis["id_avis"]?>" class="avis <?php 
            if(!$avis["luparpro"])                              //ajoute la classe nouvelAvis si l'avis n'a pas encore été vu par le pro"
            {
                echo "nouvelAvis";
            }
        ?>">
            <!-- Date de publication-->
            <p class="datePublication"><?php echo $avis['datepublication']?></p>
            <!-- Information du membre -->
            <div class="conteneurMembreAvis">
                    <div class="infoMembreAvis">
                    <img class="circular-image" src="../images/pdp/<?php echo $membre['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                        <h3><?php echo $membre['login'] ?></h3>
                    </div>
                    <p>Contexte de la visite : <?php echo $avis['cadreexperience']?></p>
                    <div class="datesAvis">
                        <p>Visité le : <?php echo $avis['dateexperience']?></p>
                        <p>Posté le : <?php echo $avis['datepublication']?></p>
                    </div>
            </div>
            <hr>
            <!-- Titre de l'avis -->
            <h4 class="titreAvis"><?php echo $avis['titreavis'] ?></h4>
            <!-- Commentaire -->
            <p class="texteAvis"><?php echo $avis['commentaire'] ?></p>
            <hr>
            <!-- Image de l'avis -->
            <section class="conteneurSpaceBetween">
                <div class="conteneurAvisImage">
                    <?php
                        if($avis["imageavis"] != null)
                        {
                        ?>
                            <img src="../images/imagesAvis/<?php echo $avis['imageavis'] ?>" class="imageAvis" alt="image de l'avis">
                        <?php
                        }
                        else
                        {
                            ?>
                                <img src="../icones/noImageSVG.svg" alt="pas d'image">
                            <?php
                        }
                    ?>
                </div>
                <div class="conteneurBtnGestionAvis">
                    <?php                                               //bouton supprimer avis
                        if(array_key_exists("idCompte", $_SESSION))
                        {
                            $idCompteConnecte = $_SESSION["idCompte"];
                        }
                        else
                        {
                            $idCompteConnecte = null;
                        }
                        
                        if($avis["id_c"] == $idCompteConnecte)            //si cet avis a été publié par l'utilisateur connecté
                        {
                            ?>
                                <div class="btnSupprimerAvis grossisQuandHover">
                                    <img src="../icones/supprimerSVG.svg" alt="icone supprimer">
                                    <p>Supprimer</p>
                                    <p hidden><?php echo $avis["id_avis"]?></p>
                                </div>
                            <?php
                        }

                        if($avis["id_c"] == $idCompteConnecte)            //si cet avis a été publié par l'utilisateur connecté
                        {
                            ?>
                                <div class="btnSupprimerAvis grossisQuandHover">
                                    <img src="../icones/supprimerSVG.svg" alt="icone supprimer">
                                    <p>Modifier</p>
                                    <p hidden><?php echo $avis["id_avis"]?></p>
                                </div>
                            <?php
                        }
                    ?>
                    <div class="conteneurPouces">
                        <div class="pouceLike">
                            <img src="../icones/pouceHautSVG.svg" alt="pouce vers le haut">
                            <p><?php echo $avis["nbpoucesbleu"] ?></p>
                        </div>
                        <div class="pouceDislike">
                            <img src="../icones/pouceBasSVG.svg" alt="pouce vers le bas">
                            <p><?php echo $avis["nbpoucesrouge"] ?></p>
                        </div>
                        
                    </div>
                </div>
                
            </section>
            
        </article>

        <?php
    }


    function test($val)
    {
        ?>
            <p>val : <?php echo $val?></p>
        <?php
    }

