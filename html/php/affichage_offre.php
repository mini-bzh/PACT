
<?php


// contient fonction caf_offre pour afficher les offres
include('../php/verif_categorie.php');

function af_offre($row) {
    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
?>
    <article class="apercuOffre">
        <h3><?php echo $row["titreoffre"];?></h3>
        <div class="conteneurSVGtexte">
            <img src="/icones/logoUserSVG.svg" alt="pro">
            <p><?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $row["id_c"] . "';")->fetchAll()[0]["raison_social"];?></p>
        </div>
        <div class="conteneurSpaceBetween">
            <p><?php echo categorie($row["idoffre"]); ?></p> <!-- catégorie -->
            <?php $ouvert=$dbh->query("SELECT tripskell.ouvert(".$row["idoffre"].");")->fetchAll()[0]["ouvert"]; ?>
            <p class="<?php echo ($ouvert ? "ouvert" : "ferme"); ?>"><?php echo ($ouvert ? "Ouvert" : "Fermé"); ?></p>
        </div>

        <div class="conteneurImage">
            <img src="/images/imagesOffres/<?php echo $row["img1"]?>" alt="illustration offre">
            <p class="text-overlay">dès <span><?php echo $row["tarifminimal"]?>€</span> /pers</p>
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
<?php
}