<?php
    session_start(); // recuperation de la sessions

    // recuperation des parametre de connection a la BdD
    include('../php/connection_params.php');
    
    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');
    include_once("../php/affichageAvis.php");

    $idCompte = $_SESSION["idCompte"];

    if($comptePro)
    {
        $offre = $dbh->query("select * from tripskell.offre_pro where id_c='" . $idCompte . "';")->fetchAll();
    }
    else{
        $membre = $dbh->query("select * from tripskell.membre where id_c='" . $idCompte . "';")->fetchAll()[0];
        $avis = $dbh->query("select * from tripskell._avis where id_c='" . $idCompte . "';")->fetchAll();
    }
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Avis</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
</head>
<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
    ?>>
        
    <?php include "../composants/header/header.php";        //import header (navbar)
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
            <h1 class="titrePrincipale">Mes avis</h1>
        </div>
    </div>
    <main id="mainAvis">
    <div id="overlay">
        <img src="" alt="image overlay">
        <div id="btnFermerOverlay">
            <p>Fermer</p>
        </div>
    </div>
    <?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
            ?>
            <section class="mainAvis">

            <section class="conteneurBtn">
                <div id="btnTrieDate" class="btnTrie grossisQuandHover" onclick="trierDate()">
                    <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieDate" class="iconeTrie">
                    <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieDate1" class="iconeTrie displayNone">
                    <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieDate2" class="iconeTrie displayNone">
                    <p id="txtBtnDate" class="txtBtnTrie">date</p>
                </div> 
                <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                    <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieNote" class="iconeTrie">
                    <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieNote1" class="iconeTrie displayNone">
                    <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieNote2" class="iconeTrie displayNone">
                    <p id="txtBtnNote" class="txtBtnTrie" >note</p>
                </div>
            </section>
              <section class="mainAvisPro">
                <?php
                    foreach($offre as $of)
                    {
                        ?>
                            <section class="conteneurAvisOffre">
                                <div class="conteneurBtnTitre">
                                    <img src="../icones/chevronUpSVG.svg" alt="chevron ouvrir/fermer">
                                    <h1><?php echo $of["titreoffre"] ?></h1>
                                </div>
                                <hr>
                                <div class="conteneurAvis">
                                    <?php
                                        $avis = $dbh->query("select * from tripskell._avis where idOffre=" . $of['idoffre'] . ";")->fetchAll();
                                        if($avis != null)
                                        {
                                            foreach ($avis as $value)
                                            {
                                                afficheAvis($value);
                                            }
                                        }
                                        else
                                        {
                                            ?>
                                                <h3>Aucun avis déposé pour <?php echo $of["titreoffre"]?></h3>
                                            <?php
                                        }
                                    ?>
                                </div>
                            </section>
                        <?php
                    }
                ?>
                </section>
                </section>
            <?php
            }
            else
            {
            ?>
               <h1>Mes avis</h1>

                <section class="mainAvis">
                <section class="conteneurBtn">
                        <div id="btnTrieDate" class="btnTrie grossisQuandHover" onclick="trierDate()">
                            <img src="/icones/trierSVG.svg" alt="iconeDate" id="iconeTrieDate" class="iconeTrie">
                            <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieDate1" class="iconeTrie displayNone">
                            <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieDate2" class="iconeTrie displayNone">
                            <p id="txtBtnDate" class="txtBtnTrie">date</p>
                        </div>
                    </section>
                    <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                        <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieNote" class="iconeTrie">
                        <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieNote1" class="iconeTrie displayNone">
                        <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieNote2" class="iconeTrie displayNone">
                        <p id="txtBtnNote" class="txtBtnTrie" >note</p>
                    </div>
                <section class="conteneurAvis">

                <?php
                foreach ($avis as $value){
                    afficheAvis($value);
                }
                ?>
                </section>
                </section>
            <?php
            }
    ?>
    </main>
    <?php                                                   //import footer
            include "../composants/footer/footer.php";
    ?>
    <script src="../js/avis.js"></script>
</body>
</html>