
<?php


// contient fonction caf_offre pour afficher les offres
include('../composants/verif/verif_categorie.php');

// contient fonction affichage_etoiles pour afficher les etoiles
include('../composants/affichage/etoiles.php');


function af_offre($row) {
    // recuperation des parametre de connection a la BdD
    include('../composants/bdd/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
    include('../composants/verif/verif_nouv_offre.php');
    
    $stmt = $dbh->prepare("select idoffre from tripskell.offre_pro as p where p.id_option='En relief';");
    $stmt->execute();
    $enRelief = $stmt->fetchAll();
    $enRelief = array_column($enRelief, 'idoffre');

    $nb_avis = $dbh->query("select count(*) from tripskell.avis where idOffre=".$row['idoffre'].";")->fetchAll()[0]['count'];
?>
    <article class="apercuOffre<?php
    if (in_array($row["idoffre"], $enRelief)) {
        echo " relief";
    }
?>
    ">
    <div class="headerResumeOffre">
    <p class="hideForGraphic">Titre offre :</p>
        <div class="debutOffre">
            <?php
            if (in_array($row["idoffre"], $nouvellesOffresId)) {
            ?>
                <img src="../../icones/logoNew.png" alt="Logo nouvelle offre" name="Logo nouvelle offre" id="logoNew">
            <?php
            }
            ?>
            <h3 class="titreOffre"><?php echo $row["titreoffre"];?></h3>
        </div>
        <img src="../../icones/<?php
            $cat = categorie($row["idoffre"]);
            switch ($cat) {
                case 'activité':
                    echo "logo_activite.png";
                    break;

                case 'spectacle':
                    echo "logo_spec.png";
                    break;

                case "parc d'attraction":
                    echo "logo_parc_attr.png";
                    break;

                case 'restauration':
                    echo "logo_resto.png";
                    break;

                case 'visite':
                    echo "logo_visite.png";
                    break;
                            
                default:
                    echo "croixSVG.svg";
                    break;
            }
    ?>
            " alt="logo_categorie" name="logo_categorie" id="logo_cat">
    </div>



        <div class="conteneurSpaceBetween" id="conteneur_mini_carte">
            <div class="conteneurSVGtexte">
                <img src="/icones/logoUserSVG.svg" alt="photo profil professionnel">
                <p class="hideForGraphic">Professionnel :</p>
                <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $row["id_c"] . "';")->fetchAll()[0]["raison_social"];?></p>
            </div>
            <p class="hideForGraphic">Categorie :</p>
            <p id="cat" class="displayNone"><?php echo categorie($row["idoffre"]); ?></p> <!-- catégorie -->
            <?php $ouvert=$dbh->query("SELECT tripskell.ouvert(".$row["idoffre"].");")->fetchAll()[0]["ouvert"]; ?>
            <p class="hideForGraphic">Actuellement :</p>
            <p id ="ouvertFerme" class="<?php echo ($ouvert ? "ouvert" : "ferme"); ?>"><?php echo ($ouvert ? "Ouvert" : "Fermé"); ?></p>
        </div>

        <div class="conteneurImage">
            <img src="/images/imagesOffres/<?php echo $row["img1"]?>" alt="illustration offre">
            <p class="text-overlay">dès <span><?php echo $row["tarifminimal"]?>€</span> /pers</p>
        </div>

        <p class="hideForGraphic">Description :</p>
        <p class="resumeApercu"><?php echo $row["resume"]?></p>

        <div class="conteneurSVGtexte conteneurAdresse">
            <img src="/icones/adresseSVG.svg" alt="adresse">
            <p id="ville" class="texteSmall"><?php echo $row["ville"]?></p>
            <p id="adresse" class="texteSmall"><?php $adresse = $row["numero"] . " " . $row["rue"];echo $adresse;?></p>
        </div>
        <div class="conteneurSpaceBetween">
            <div class="etoiles">
                <p class="hideForGraphic">Note /5 :</p>
                <p id="note"><?php echo $row["note"]?></p>
                <?php affichage_etoiles($row["note"]);?>
            </div>
            <p><?php echo $nb_avis; ?> avis</p>
        </div>
    </article>
<?php
}