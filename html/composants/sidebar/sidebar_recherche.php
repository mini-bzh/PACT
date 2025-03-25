<?php
include('../composants/bdd/connection_params.php');
    
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
                
                <svg viewBox="0 0 89 81" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M10 10 L79 71 M79 10 L10 71" stroke="black" stroke-width="5" stroke-linecap="round"/>
                </svg>

            </div>
            <div class="content-aside">
                <h2 class="title-aside">Filtres</h2>
                <fieldset id="ouverture">
                    <legend>Ouverture du site</legend>
                    <label class="toggle-button">
                        <span>Ouvert</span>
                        <input type="checkbox" name="ouverture" value="ouvert">
                    </label>
                    <label class="toggle-button">
                        <span>Fermer</span>
                        <input type="checkbox" name="ouverture" value="ferme">
                    </label>
                </fieldset>

                <fieldset id="categorie">
                    <legend>Categorie</legend>
                    <label class="toggle-button">
                        <span>Restauration</span>
                        <input type="checkbox" name="ouverture" value="restauration">
                    </label>
                    <label class="toggle-button">
                        <span>Parcs</span>
                        <input type="checkbox" name="ouverture" value="parc d'attraction">
                    </label>
                    <label class="toggle-button">
                        <span>Spectacles</span>
                        <input type="checkbox" name="ouverture" value="spectacle">
                    </label>
                    <label class="toggle-button">
                        <span>Activités</span>
                        <input type="checkbox" name="ouverture" value="activité">
                    </label>
                    <label class="toggle-button">
                        <span>Visites</span>
                        <input type="checkbox" name="ouverture" value="visite">
                    </label>
                </fieldset>

                <fieldset id="prix">
                    <legend>Prix</legend>
                    <div class="double-range-slider-box">
                        <div class="double-range-slider">
                            <span class="range-bar" id="range-barPrix"></span>
                    
                            <div class="input-box"></div>

                            <div class="value-popup value-popupMin minvaluePrix"></div>
                            <div class="value-popup value-popupMax maxvaluePrix"></div>
    
                        </div>
                    </div>
                </fieldset>

                <fieldset id="note">
                    <legend>Note</legend>
                    <div class="double-range-slider-box">
                        <div class="double-range-slider">
                            <span class="range-bar" id="range-barNote"></span>
                    
                            <input type="range" class="inputNote minNote" min="0" max="5" value="0" step="0" />
                            <input type="range" class="inputNote maxNote" min="0" max="5" value="5" step="0" />

                            <div class="value-popup value-popupMin minvalueNote"></div>
                            <div class="value-popup value-popupMax maxvalueNote"></div>
    
                        </div>
                    </div>
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
                            <span><?php echo $tag; ?></span>
                            <input type="checkbox" name="ouverture" value="<?php echo $tag; ?>">
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

            <div id="btnAgrandir" class="button">
                <img src="../../icones/logo_visite.png" width="20px" alt="logo_carte" name="logo_carte" id="logo_carte">
            </div>
        </aside>
        