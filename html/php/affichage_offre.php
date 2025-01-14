
<?php


// contient fonction caf_offre pour afficher les offres
include('../php/verif_categorie.php');

// contient fonction affichage_etoiles pour afficher les etoiles
include('../php/etoiles.php'); 

function af_offre($row) {
    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
    $stmt = $dbh->prepare("select idoffre from tripskell.offre_pro as p where p.id_option='En relief';");
    $stmt->execute();
    $enRelief = $stmt->fetchAll();
    $enRelief = array_column($enRelief, 'idoffre');
?>
    <article class="apercuOffre
<?php
    if (in_array($row["idoffre"], $enRelief)) {
        echo " relief";
    }
?>
    ">
        <h3><?php echo $row["titreoffre"];?></h3>
        <div class="conteneurSVGtexte">
            <img src="/icones/logoUserSVG.svg" alt="pro">
            <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $row["id_c"] . "';")->fetchAll()[0]["raison_social"];?></p>
        </div>
        <div class="conteneurSpaceBetween">
            <p id="cat"><?php echo categorie($row["idoffre"]); ?></p> <!-- catégorie -->
            <?php $ouvert=$dbh->query("SELECT tripskell.ouvert(".$row["idoffre"].");")->fetchAll()[0]["ouvert"]; ?>
            <p id ="ouvertFerme" class="<?php echo ($ouvert ? "ouvert" : "ferme"); ?>"><?php echo ($ouvert ? "Ouvert" : "Fermé"); ?></p>
        </div>

        <div class="conteneurImage">
            <img src="/images/imagesOffres/<?php echo $row["img1"]?>" alt="illustration offre">
            <p class="text-overlay">dès <span><?php echo $row["tarifminimal"]?>€</span> /pers</p>
        </div>
        
        <p class="resumeApercu"><?php echo $row["resume"]?></p>

        <div class="conteneurSVGtexte">
            <img src="/icones/adresseSVG.svg" alt="adresse">
            <p id="ville"><?php echo $row["ville"]?></p>
        </div>
        <div class="conteneurSpaceBetween">
            <div class="etoiles">
                <p id="note"><?php echo $row["note"]?></p>
                <?php affichage_etoiles($row["note"]);?>
            </div>
            <p>439 avis</p>
        </div>
    </article>
<?php
}