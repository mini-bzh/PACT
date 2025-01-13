
<?php
    // Inclusion du script pour vérifier si l'utilisateur a un compte pro
    include('../php/verif_compte_pro.php');
    include('../php/verif_compte_membre.php');
?>
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
                <li class="liHeader" id="btRech">
                    <a class="aHeader" href="recherche.php">
                        <h3>Rechercher</h3>
                    </a>
                </li>
                <?php if($comptePro || $compteMembre)
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
                        <h3>Gestion des offres</h3>
                    </a>
                </li>
                
                <li class="liHeader" id="btCompte">
                    <a class="aHeader" href="/pages/compte.php">
                    <?php
                        if($comptePro || $compteMembre)
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
                    <a class="aHeaderMobile" 
                    <?php
                        if($comptePro || $compteMembre)
                        {
                            ?>
                            href="/pages/avis.php"
                            <?php
                        }
                        else
                        {
                            ?>
                            href="/pages/compte.php"
                            <?php
                        }
                    ?>>
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