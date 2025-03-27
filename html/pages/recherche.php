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

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.css" />
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/MarkerCluster.Default.css" />
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

            <svg width="384" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg">
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
                        <img src="/icones/trierSVG.svg" alt="icone non trié" id="iconeTriePrix" class="iconeTrie">
                        <img src="/icones/trier1SVG.svg" alt="icone tri décroissant" id="iconeTriePrix1" class="iconeTrie displayNone">
                        <img src="/icones/trier2SVG.svg" alt="icone tri croissant" id="iconeTriePrix2" class="iconeTrie displayNone">
                        <p id="txtBtnPrix" class="txtBtnTrie" >prix</p>
                    </div>

                    <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                        <img src="/icones/trierSVG.svg" alt="icone non trié" id="iconeTrieNote" class="iconeTrie">
                        <img src="/icones/trier1SVG.svg" alt="icone tri décroissant" id="iconeTrieNote1" class="iconeTrie displayNone">
                        <img src="/icones/trier2SVG.svg" alt="icone tri croissant" id="iconeTrieNote2" class="iconeTrie displayNone">
                        <p id="txtBtnNote" class="txtBtnTrie" >note</p>
                    </div>
                </section>
            </section>
                

            <div>
            <div id="map"></div>
            <section class="conteneurOffres">
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
            </div>
        </main>
        <?php
            include "../composants/footer/footer.php";
        ?>
        <script src="../js/recherche.js" ></script>
        <script src="../js/apparitionOffres.js"></script>

        <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
        <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
        <!-- Leaflet.markercluster JS -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/leaflet.markercluster/1.4.1/leaflet.markercluster.js"></script>
        
        <script src="../js/carte.js" ></script>
        <script src="../js/apparitionOffres.js"></script>

    </body>
</html>