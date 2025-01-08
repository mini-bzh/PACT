<?php
include('../php/connection_params.php');
    
// Inclue la fonction qui verifie la catégorie d'une offre
// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

$stmt = $dbh->prepare("select DISTINCT tripskell._tags.nomTag from tripskell._tags");

$stmt->execute();
$tags = $stmt->fetchAll();
$tags = array_column($tags, 'nomtag');
?>

        <aside id="filtres-aside" class="displayNone">
            <div id="bn-sidebar-exit" class="button">
                <span>x</span>
            </div>
            <div class="content-aside">
                <fieldset id="ouverture">
                    <legend>Ouverture du site</legend>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="ouvert">
                        <span>Ouvert</span>
                    </label>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="ferme">
                        <span>Fermer</span>
                    </label>
                </fieldset>

                <fieldset id="categorie">
                    <legend>Categorie</legend>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="restauration">
                        <span>Restauration</span>
                    </label>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="parc d'attraction">
                        <span>Parcs</span>
                    </label>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="spectacle">
                        <span>Spectacles</span>
                    </label>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="activité">
                        <span>Activités</span>
                    </label>
                    <label class="toggle-button">
                        <input type="checkbox" name="ouverture" value="visite">
                        <span>Visites</span>
                    </label>
                </fieldset>

                <fieldset id="prix">
                    <legend>Prix</legend>
                    <span class="price-value">0 €</span>
                    <input type="range" id="price" name="price" min="0" max="100" value="50" step="1">
                    <span class="price-value">100 €</span>
                </fieldset>

                <fieldset id="filtreLieu">
                    <legend>Lieu</legend>
                    <div class="input-group">
                        <input id="lieu" type="text" name="lieu" placeholder="Commune / Lieu-dit">
                    </div>
                </fieldset>

                <fieldset id="fieldsetTag">
                    <legend>Tags</legend>
                    <?php 
                    foreach ($tags as $tag) {?>
                        <label class="toggle-button">
                            <input type="checkbox" name="ouverture" value="<?php echo $tag; ?>">
                            <span><?php echo $tag; ?></span>
                        </label>
                    <?php }
                    ?>
                </fieldset>
            </div>
            
        </aside>

        <aside id="menu-aside">
            <div id="bn-sidebar-filtres" class="button">
                <svg viewBox="0 0 89 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M86.3333 3H3L36.3333 42.4167V69.6667L53 78V42.4167L86.3333 3Z">
                </svg>
            </div>
        </aside>