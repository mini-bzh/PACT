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
                        <p>Visité le : <?php echo implode("-",array_reverse(explode("-",$avis['dateexperience'])))?></p>
                        <p>Posté le : <?php echo implode("-",array_reverse(explode("-",$avis['datepublication'])))?></p>
                    </div>
            </div>
            <hr>
            <!-- Titre de l'avis -->
            <h4 class="titreAvis"><?php echo $avis['titreavis'] ?></h4>
            <div class="etoiles">
            <?php
                    include_once("etoiles.php");
                    echo affichage_etoiles($avis["note"]);
                ?>
                <p>(<?php echo $avis["note"] ?>)</p>
            </div>
            <!-- Commentaire -->
            <p class="texteAvis"><?php echo $avis['commentaire'] ?></p>
            <hr>
            <?php
                $query = "SELECT * FROM tripskell._reponseAvis WHERE id_avis = :idAvis";
                $stmt = $dbh->prepare($query);
                $stmt->bindParam(":idAvis", $avis["id_avis"]);
                $stmt->execute();

                $resultReponse = $stmt->fetch();

                if($resultReponse != [])                //si il y a une réponse à l'avis
                {                    
                    $idPro = $resultReponse["id_c"];
                    
                    $nomPro = "professionnel";
                    $pdpPro = "compteSVG.svg";


                    $query = "SELECT raison_social, pdp FROM tripskell.pro_prive WHERE id_c = :idPro";
                    $stmt = $dbh->prepare($query);
                    $stmt->bindParam(":idPro", $idPro);

                    $stmt->execute();
                    $result = $stmt->fetch();

                    if($result != [])           //affiche la réponse
                    {
                        $nomPro = $result["raison_social"];
                        $pdpPro = $result["pdp"];
                    }
                    else
                    {
                        $query = "SELECT raison_social, pdp FROM tripskell.pro_public WHERE id_c = 3";
                        $stmt = $dbh->prepare($query);

                        $stmt->execute();
                        $result = $stmt->fetch();

                        if($result != [])
                        {
                            $nomPro = $result["raison_social"];
                            $pdpPro = $result["pdp"];
                        }
                    }
                    ?>
                    <div class="reponse">
                        <div class="proReponse">
                            <img src="../images/pdp/<?php echo $pdpPro?>" alt="photo du pro">

                            <h4>Réponse de <?php echo $nomPro?></h4>
                        </div>
                        <p><?php echo $resultReponse["textereponseavis"];?></p>
                    </div>
                    <hr>
                    <?php
                }
                else
                {
                    include('../php/verif_compte_pro.php');
                    
                    if($comptePro)
                    {
                        $query =    "SELECT count(*) from tripskell._offre JOIN tripskell._avis ON tripskell._offre.idoffre = tripskell._avis.idoffre 
                        WHERE tripskell._offre.id_c = :idCompte AND tripskell._avis.id_avis = :idAvis";         //regarde si le pro connecté possède l'offre sur laquelle on a déposé una vis

                        $stmt = $dbh->prepare($query);
                        $stmt->bindParam(":idCompte", $_SESSION["idCompte"]);
                        $stmt->bindParam(":idAvis", $avis["id_avis"]);
                        $stmt->execute();

                        $offreDuPro = $stmt->fetch()["count"];
                        
                        if($offreDuPro)
                        {
                            ?>
                            <h5>Voulez-vous répondre à cet avis ?</h5>
                            <div class="formReponse">
                                <textarea type=""text name="reponseAvis" maxlength="200" class="reponseAvis" placeholder="Répondez à l'avis de <?php echo $membre["login"];?> !"></textarea>
                                
                                <p class="erreurReponseVide" hidden>Veuillez écrire votre réponse !</p>
                                <div class="btnRepondre grossisQuandHover">
                                    <p>répondre</p>
                                </div> 
                            </div>
                            <hr>
                            <?php
                        }
                    }
                }    
            ?>
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
                            <a href="modifAvis.php?id_avis=<?php echo $avis["id_avis"]?>">
                                <div class="btnModifierAvis grossisQuandHover">
                                    <img src="../icones/modifierSVG.svg" alt="icone modifier">
                                    <p>Modifier</p>
                                </div>
                                </a>
                            <?php
                        }
                        else if($avis["id_c"] != $idCompteConnecte && $idCompteConnecte != null){ //bouton de signalement d'avis qui s'affiche si l'avis n'est pas de l'utilisateur et si il n'a pas déjà signalé l'avis
                            $avisSignaler = $dbh->query("select * from tripskell._signalerAvis where id_c=" . $idCompteConnecte . " and id_avis=". $avis["id_avis"].";")->fetchAll();
                            if($avisSignaler == null){ //bouton pour signaler
                            ?>
                                <div id="<?php echo $avis["id_avis"]?>" class="btnSignalerAvis grossisQuandHover" onclick="confSignaler(event)">     
                                    <img src="../icones/signalerSVG.svg" alt="icone signaler">
                                    <p>Signaler</p>
                                    <p hidden><?php echo $idCompteConnecte?></p>
                                    <p hidden><?php echo $avis["id_avis"]?></p>
                                </div>
                            <?php
                            }
                            else if($avisSignaler != null){ // bouton déjà signaler
                                ?>
                                    <div class="btnDejaSignaler grossisQuandHover">
                                        <img src="../icones/okSVG.svg" alt="icone signaler">
                                        <p>Signalé</p>
                                    </div>
                                <?php
                            }
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

