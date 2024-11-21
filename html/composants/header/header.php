
<?php
    // Inclusion du script pour vérifier si l'utilisateur a un compte pro
    include('../php/verif_compte_pro.php');
?>
    
    <link rel="stylesheet" href="../style/style.css">
    <header class="headerPC-Tab <?php
        if($comptePro)
        {
            echo "navBarPro";               //classe qui réduit la police (car il y a une brique de navigation de plus)
        }
    ?>">
        <nav class="navHeader">
            <a id="logoEtPro" href="/pages/accueil.php">
                <img class="logoHeader" src="/images/logo/logo_grand.png" alt="logo PACT">
                <?php
                    if($comptePro)
                    {
                        ?>
                        <p id="textPro">Pro</p>
                        <?php
                    }
                ?>
            </a>

            <ul class="ulHeader">
                <li class="liHeader" id="btAccueil">
                    <a class="aHeader" href="/pages/accueil.php">
                    <img src="/icones/homeSVG.svg" alt="icone home">
                    <h3>Accueil</h3>
                    </a>
                </li>
                <li class="liHeader" id="btRech">
                    <a class="aHeader" href="recherche.php">
                    <img src="/icones/rechercherSVG.svg" alt="icone rechercher">
                    <h3>Rechercher</h3>
                    </a>
                </li>
                <?php if($comptePro)
                {
                    ?>
                        <li class="liHeader" id="btAvis">
                            <a class="aHeader" href="avis.php">
                            <img src="/icones/commentSVG.svg" alt="icone commentaires">
                            <h3>Mes avis</h3>
                            </a>
                        </li>
                    <?php 
                } 
                ?>
                <li class="liHeader" id="btOffres"    <?php 
                    if(!$comptePro)
                    {
                        echo "hidden";                          //cache la brique "mes offres" si l'utilisateur n'est pas un professionnel
                    }
                ?>>
                    <a class="aHeader" href="/pages/gestionOffres.php">
                    <img src="/icones/offreSVG.svg" alt="icone offres">
                    <h3>Gestion des offres</h3>
                    </a>
                </li>
                <li class="liHeader" id="btCompte">
                    <a class="aHeader" href="/pages/compte.php">
                    <img src="/icones/compteSVG.svg" alt="icone compte">
                    <?php
                        if($comptePro)
                        {
                            ?>
                            <h3>Mon compte</h3>
                            <?php
                        }
                        else
                        {
                            ?>
                            <h3>Connexion</h3>
                            <?php
                        }
                    ?>
                    
                    </a>
                </li>
            </ul>
        </nav>
    </header> 

    <header class="headerMobile">
        <nav class="navHeaderMobile">
            <ul class="ulHeaderMobile">
                <li class="liHeaderMobile" id="btAccueilMobile">
                    <a class="aHeaderMobile" href="/pages/accueil.php">
                    <img src="/icones/homeSVG.svg" alt="icone home">
                    <div class="trait"></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btRechMobile">
                    <a class="aHeaderMobile" href="/pages/recherche.php">
                    <img src="/icones/rechercherSVG.svg" alt="icone rechercher">
                    <div class="trait"></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btAvisMobile">
                    <a class="aHeaderMobile" href="/pages/avis.php">
                    <img src="/icones/commentSVG.svg" alt="icone commentaires">
                    <div class="trait"></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btCompteMobile">
                    <a class="aHeaderMobile" href="/pages/compte.php">
                    <img src="/icones/compteSVG.svg" alt="icone compte">
                    <div class="trait"></div>
                    </a>
                </li>
            </ul>
        </nav>
    </header>
    <script src="/js/click.js"></script>