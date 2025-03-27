<?php

    function afficheAvis($avis)
    {

        // recuperation des parametre de connection a la BdD
        include('../composants/bdd/connection_params.php');
        
        // connexion a la BdD
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

        $membre = $dbh->query("select * from tripskell.membre where id_c=" . $avis['id_c'] . ";")->fetchAll()[0];

        $info_offre = $dbh->query("select id_abo, id_c from tripskell.offre_pro where idoffre=".
                              "(select idoffre from tripskell._avis where id_avis=" . 
                              $avis["id_avis"] .");")->fetch();
        
        $id_abo = $info_offre["id_abo"];
        $id_propri_offre = $info_offre["id_c"];
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
            <div class="headerAvis">
                <div class="infoMembreAvis">
                <img class="circular-image" src="../images/pdp/<?php echo $membre['pdp'] ?>" alt="Photo de profil" title="Photo de profil">
                    <h3><?php echo $membre['login'] ?></h3>
                </div>
                <div class="contexte">
                    <p>Contexte de la visite : <?php echo $avis['cadreexperience']?></p>
                    <div class="datesAvis">
                        <p>Visité le : <?php echo implode("-",array_reverse(explode("-",$avis['dateexperience'])))?></p>
                        <p>Posté le : <?php echo implode("-",array_reverse(explode("-",$avis['datepublication'])))?></p>
                    </div>
                </div>
            </div>
            <hr>
            <div class="bodyAvis">
                <div class="conteneurAvisGauche">
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
                <?php
                    $query = "SELECT * FROM tripskell._reponseAvis WHERE id_avis = :idAvis";
                    $stmt = $dbh->prepare($query);
                    $stmt->bindParam(":idAvis", $avis["id_avis"]);
                    $stmt->execute();

                    $resultReponse = $stmt->fetch();


                    if($resultReponse)                //si il y a une réponse à l'avis (un fetch renvoie false si rien n'a été trouvé)
                    {       

                        $idPro = $resultReponse["id_c"];
                        
                        $nomPro = "professionnel";
                        $pdpPro = "compteSVG.svg";


                        $query = "SELECT raison_social, pdp FROM tripskell.pro_prive WHERE id_c = :idPro";
                        $stmt = $dbh->prepare($query);
                        $stmt->bindParam(":idPro", $idPro);

                        $stmt->execute();
                        $result = $stmt->fetch();


                        if($result)           //affiche la réponse
                        {

                            $nomPro = $result["raison_social"];
                            $pdpPro = $result["pdp"];
                        }
                        else
                        {
                            $query = "SELECT raison_social, pdp FROM tripskell.pro_public WHERE id_c = :idPro";
                            $stmt = $dbh->prepare($query);
                            $stmt->bindParam(":idPro", $idPro);
                            $stmt->execute();

                            $result = $stmt->fetch();
                        }
                        ?>

                        <div class="reponse">
                            <hr>
                            <div class="proReponse">
                                <img src="../images/pdp/<?php echo $pdpPro?>" alt="photo de profil professionnel">

                                <h4>Réponse de <?php echo $nomPro?></h4>
                            </div>
                            <p><?php echo $resultReponse["textereponseavis"];?></p>
                        </div>
                        <?php
                    }
                    else
                    {
                        include('../composants/verif/verif_compte_pro.php');
                        
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
                                <?php
                            }
                        }
                    }
                ?>
            </div>
            <div class="conteneurAvisDroite">
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
                                <img src="../icones/noImage.png" class="noImage" alt="pas d'image pour cet avis">
                            <?php
                        }
                    ?>
                </div>
             </div>
            </div>
            <hr>
            <div class="footerAvis">
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
                        if ($id_abo == "Premium" && $idCompteConnecte == $id_propri_offre) {
                    ?>
                        <div id="btnBlacklisterAvis<?php echo $avis['id_avis'];?>" class="<?php echo (is_null($avis["date_recup_token_blacklist"]))?'btnSignalerAvis grossisQuandHover" onclick="confBlacklister(event, ' .$avis['id_avis'].')"':'btnDejaSignaler"';?>>     
                            <img src="../icones/<?php echo (is_null($avis["date_recup_token_blacklist"]))?'signalerSVG.svg':'okSVG.svg';?>" alt="icone signaler">
                            <p>Blacklister</p>
                            <p hidden><?php echo $idCompteConnecte?></p>
                        </div>
                    <?php
                        }
                    ?>
                </div>
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
        </article>
        <?php
    }


function dependances_avis() {
    ?>
        <!-- Pop-up Blacklister un avis -->
    <div class="filtePopUp" id="popUpBlacklister">
        <div class="popUpAvis">
            <p class="texteLarge">Êtes-vous sur de vouloir blacklister cet avis</p>
            <p>Cette opération est irréversible</p>
            <div class="boutonPopUp">
                <button class="btnAnnulerSignalement" onclick="fermeConfBlacklister()">Annuler</button>
                <button class="btnValiderId btnValiderSignalement" onclick="blacklisterAvis()">Valider</button>
            </div>
        </div>
    </div>

        <!-- Pop-up Signaler un avis -->
    <div class="filtePopUp" id="popUpSignaler">
        <div class="popUpAvis">
            <textarea name="motifSignalement" id="motifSignalement" cols="30" rows="10" placeholder="Entrez un motif de signalement"></textarea>
            <div class="boutonPopUp">
                <button class="btnAnnulerSignalement" onclick="fermeConfSignaler()">Annuler</button>
                <button class="btnValiderId btnValiderSignalement" onclick="signalerAvis()">Valider</button>
            </div>
        </div>
    </div>

    <?php
}