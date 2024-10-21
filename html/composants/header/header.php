<?php
    $user = null;
    if(key_exists("user", $_GET))
    {
        $user =$_GET["user"];
    }
?>

    <header class="headerPC-Tab <?php
        if($user == "pro")
        {
            echo "navBarPro";               //classe qui réduit la police (car il y a une brique de navigation de plus)
        }
    ?>">
        <nav class="navHeader">
            <img class="logoHeader" src="/html/images/logo/logo_grand.png" alt="logo PACT">
            <ul class="ulHeader">
                <li class="liHeader" id="btAccueil">
                    <a class="aHeader" href="/html/pages/accueil.php?user=<?php echo $user?>">
                    <img src="/html/icones/homeSVG.svg" alt="icone home">
                    <h3>Accueil</h3>
                    </a>
                </li>
                <li class="liHeader" id="btRech">
                    <a class="aHeader" href="">
                    <img src="/html/icones/rechercherSVG.svg" alt="icone rechercher">
                    <h3>Rechercher</h3>
                    </a>
                </li>
                <li class="liHeader" id="btAvis">
                    <a class="aHeader" href="">
                    <img src="/html/icones/commentSVG.svg" alt="icone commentaires">
                    <h3>Mes avis</h3>
                    </a>
                </li>
                <li class="liHeader" id="btOffres"    <?php 
                    if($user != "pro")
                    {
                        echo "hidden";                          //cache la brique "mes offres" si l'utilisateur n'est pas un professionnel
                    }
                ?>>
                    <a class="aHeader" href="">
                    <img src="/html/icones/offreSVG.svg" alt="icone offres">
                    <h3>Mes offres</h3>
                    </a>
                </li>
                <li class="liHeader" id="btConnect" hidden>
                    <a class="aHeader" href="">
                    <img src="/html/icones/compteSVG.svg" alt="icone compte">
                    <h3>Se connecter</h3>
                    </a>
                </li>
                <li class="liHeader" id="btCompte">
                    <a class="aHeader" href="/html/pages/compte.php?user=<?php echo $user; ?>">
                    <img src="/html/icones/compteSVG.svg" alt="icone compte">
                    <h3>Mon compte</h3>
                    </a>
                </li>
            </ul>
        </nav>
    </header> 

    <header class="headerMobile">
        <nav class="navHeaderMobile">
            <ul class="ulHeaderMobile">
                <li class="liHeaderMobile" id="btAccueilMobile">
                    <a class="aHeaderMobile" href="/html/pages/accueil.php?user=<?php echo $user?>">
                    <img src="../../icones/homeSVG.svg" alt="icone home">
                    <div class="trait" hidden></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btRechMobile">
                    <a class="aHeaderMobile" href="">
                    <img src="../../icones/rechercherSVG.svg" alt="icone rechercher">
                    <div class="trait"></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btAvisMobile">
                    <a class="aHeaderMobile" href="">
                    <img src="../../icones/commentSVG.svg" alt="icone commentaires">
                    <div class="trait" hidden></div>
                    </a>
                </li>
                <li class="liHeaderMobile" id="btCompteMobile">
                    <a class="aHeaderMobile" href="/html/pages/compte.php?user=<?php echo $user; ?>">
                    <img src="../../icones/compteSVG.svg" alt="icone compte">
                    <div class="trait" hidden></div>
                    </a>
                </li>
            </ul>
        </nav>
    </header>

