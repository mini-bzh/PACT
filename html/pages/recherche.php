<?php
    session_start(); // recuperation de la sessions
    
    // recuperation des parametre de connection a la BdD
    include('../composants/bdd/connection_params.php');
    
    // Inclue la fonction qui verifie la catégorie d'une offre
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../composants/verif/verif_compte_pro.php');
    // cree $compteMembre qui est true quand on est sur un compte membre et false sinon
    include('../composants/verif/verif_compte_membre.php');


    // contient fonction af_offre pour afficher les offres
    include('../composants/affichage/affichage_offre.php');


    if($comptePro)      /* prépare la requête pour récupérer les offres à afficher : offres du pro si connecté en tant que pro, toutes les 
                         offres sinon */

    {
        $stmt = $dbh->prepare("select * from tripskell.offre_pro where ville = (SELECT ville FROM tripskell.pro_prive WHERE id_c=:id_c)");

        // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
        $stmt->bindParam(":id_c", var: $id_c);
        
        $stmt = $dbh->prepare("select * from tripskell.offre_pro where enLigne");

        $id_c = $_SESSION["idCompte"];
    }
    else
    {
        $stmt = $dbh->query("SELECT * from tripskell.offre_visiteur;");
    }

    $stmt->execute();
    $rows = $stmt->fetchAll();          // rows : les offres à afficher

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Rechercher</title>

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
        <?php include "../composants/header/header.php";        //import navbar
        ?>
        <div class="titrePortable">

            <svg width="401" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg">
            <g filter="url(#filter0_d_169_4380)">
            <ellipse cx="169.5" cy="61" rx="231.5" ry="89" fill="white"/>
            </g>
            <defs>
            <filter id="filter0_d_169_4380" x="-66" y="-28" width="471" height="186" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
            <feFlood flood-opacity="0" result="BackgroundImageFix"/>
            <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha"/>
            <feOffset dy="4"/>
            <feGaussianBlur stdDeviation="2"/>
            <feComposite in2="hardAlpha" operator="out"/>
            <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0"/>
            <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_169_4380"/>
            <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_169_4380" result="shape"/>
            </filter>
            </defs>
            </svg>

            <div id="conteneurTitreMobile">
                <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
                <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
                <h1>Rechercher</h1>
            </div>
        </div>
<?php include "../composants/sidebar/sidebar_recherche.php"; ?>
        <main class="main-recherche">
            <section id="conteneurBarre">
                <label for="searchbar"></label>
                <input type="searchbar" id="searchbar" placeholder="Rechercher">
            </section>

            <section id="conteneurBouton">
                <section id="conteneurTrie">
                    <div id="btnTriePrix" class="btnTrie grossisQuandHover" onclick="trierPrix()">
                        <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTriePrix" class="iconeTrie">
                        <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTriePrix1" class="iconeTrie displayNone">
                        <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTriePrix2" class="iconeTrie displayNone">
                        <p id="txtBtnPrix" class="txtBtnTrie" >prix</p>
                    </div>

                    <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                        <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieNote" class="iconeTrie">
                        <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieNote1" class="iconeTrie displayNone">
                        <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieNote2" class="iconeTrie displayNone">
                        <p id="txtBtnNote" class="txtBtnTrie" >note</p>
                    </div>
                </section>
            
                <div id="conteneurFiltres">
                    <section class="filtrerBarre">
                        <div class="filtreDeplie displayNone">
                            <div>
                                <div id="filtreCat">
                                    <div class="titreFiltre">
                                        <hr>
                                        <h3>Catégorie</h3>
                                    </div>

                                    <fieldset id="categorie">
                                        <label>
                                            <input type="checkbox" name="categorie" value="parc d'attraction">
                                            <p>Parc d'attractions</p>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="categorie" value="restauration">
                                            <p>Restauration</p>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="categorie" value="spectacle">
                                            <p>Spectacle</p>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="categorie" value="activité">
                                            <p>Activités</p>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="categorie" value="visite">
                                            <p>Visites</p>
                                        </label>
                                    </fieldset>
                                </div>
                                
                            </div>
                            <div>
                                <div id="filtreOuverture">
                                    <div class="titreFiltre">
                                        <hr>
                                        <h3>Ouverture</h3>
                                    </div>

                                    <fieldset id="ouverture">
                                        <label>
                                            <input type="checkbox" name="ouverture" value="ouvert">
                                            <p>Ouvert</p>
                                        </label>
                                        <label>
                                            <input type="checkbox" name="ouverture" value="ferme">
                                            <p>Fermé</p>
                                        </label>
                                    </fieldset>
                                </div>

                                <div id="filtreLieu">
                                    <div class="titreFiltre">
                                        <hr>
                                        <h3>Lieu</h3>
                                    </div>

                                    <input id="lieu" type="text" name="lieu" placeholder="Commune / Lieu-dit">
                                </div>

                            </div>
                            <div>

                              
                                <div id="filtrePrix">
                                    <div class="titreFiltre">
                                        <hr>
                                        <h3>Prix</h3>
                                    </div>
                                    <div class="prixFiltre">
                                        <div>
                                            <label for="prixMin"><p>Minimum</p></label>
                                            <input type="number" id="prixMin" name="prixMin" value="" min="0" step="1" placeholder="ex : 70">
                                            <div class="number-input">
                                                <button class="increment" onclick="adjustValue(1, 'prixMin')">⯅</button>
                                                <button class="decrement" onclick="adjustValue(-1, 'prixMin')">⯆</button>
                                            </div>
                                            <p>€</p>
                                        </div>
                                        <div>
                                            <label for="prixMax"><p>Maximum</p></label>
                                            <input type="number" id="prixMax" name="prixMax" value="" min="0" step="1" placeholder="ex : 300">
                                            <div class="number-input">
                                                <button class="increment" onclick="adjustValue(1, 'prixMax')">⯅</button>
                                                <button class="decrement" onclick="adjustValue(-1, 'prixMax')">⯆</button>
                                            </div>
                                            <p>€</p>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </section>

                    <section class="filtreTag">
                        
                        <div id="fieldsetTag" class="filtreTagDeplie displayNone">
<?php
                        // SELECTION DE TOUS LES TAGS
                        $stmt2 = $dbh->prepare("select DISTINCT tripskell._tags.nomTag from tripskell._tags");

                        $stmt2->execute();
                        $tags = $stmt2->fetchAll();
                        $tags = array_column($tags, 'nomtag');


                        for ($i = 0; $i < count($tags); $i += 3) {
?>
                            <div>
<?php
                                $first = $tags[$i];
                                $second = $tags[$i + 1] ?? null; // Vérifier si le second élément existe (évite une erreur)
                                $trois = $tags[$i + 2] ?? null; // Vérifier si le troisième élément existe (évite une erreur)
?>
                                <label>
                                    <input type="checkbox" name="tags" value="<?php echo $first ?>">
                                    <p><?php echo $first ?></p>
                                </label>
<?php
                                if ($second !== null) {
?>
                                <label>
                                    <input type="checkbox" name="tags" value="<?php echo $second ?>">
                                    <p><?php echo $second ?></p>
                                </label>
<?php
                                }

                                if ($trois !== null) {
?>
                                <label>
                                    <input type="checkbox" name="tags" value="<?php echo $trois ?>">
                                    <p><?php echo $trois ?></p>
                                </label>
<?php
                                }
?>
                            </div>
<?php
                            }
?>           
                        </div>
                    </section>   
                </section>
            </div>
            
            <div id="map"></div>

            <section id="conteneurOffres">
                <article>
                    
                    <?php
                        foreach($rows as $row)          // il parcourt les offres pour les afficher
                        {
                            ?>
                                <a href="/pages/detailOffre.php?idOffre=<?php echo $row["idoffre"]?>" class="lienApercuOffre grossisQuandHover" 
                                id="offre<?php echo $row['idoffre']?>">
                                    <!-- affichage des offrres -->
                                    <?php af_offre($row);?>
                                </a>
                            <?php
                        }
                    ?>
                </article>
            </section>
        </main>
        <?php
            include "../composants/footer/footer.php";
        ?>
        <style>
            #map{
                width: 90%;
                height: 500px;
            }
        </style>
        <script src="../js/recherche.js" ></script>
        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <script src="../js/carte.js" ></script>
    </body>
</html>