<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_pro.php');
include_once("../composants/affichage/affichageAvis.php");

$idCompte = $_SESSION["idCompte"];

if ($comptePro) {
    $offres = $dbh->query("select * from tripskell.offre_pro where id_c='" . $idCompte . "';")->fetchAll();
} else {
    $membre = $dbh->query("select * from tripskell.membre where id_c='" . $idCompte . "';")->fetchAll()[0];
    $avis = $dbh->query("select * from tripskell._avis where id_c='" . $idCompte . "';")->fetchAll();
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0" lang="fr">
    <title>Avis</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
</head>

<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro) {
                echo "fondPro";
            } else {
                echo "fondVisiteur";
            }
            ?>>

    <?php include "../composants/header/header.php";        //import header (navbar)
    ?>
    <header>
        <div class="titrePortable">
                <!--<img src="/images/logo/logo_grand.png" alt="logo grand" id="logoTitreMobile">-->
                <img src="/images/logo/logo_petit.png" alt="logo petit" id="logoTitreMobile">
                <h1>Mes Avis</h1>
        </div>
    </header>
    <main id="mainAvis">
        <div id="overlay">
            <img src="" alt="image overlay">
            <div id="btnFermerOverlay">
                <p>Fermer</p>
            </div>
        </div>
        <?php                          //met le bon fond en fonction de l'utilisateur
        if ($comptePro) {
        ?>
            <section class="mainAvis">

                <section class="mainAvisPro">
                    <p hidden id="idPro"><?php echo $_SESSION["idCompte"] ?></p>
                    <?php
                    $query =    "SELECT COUNT(*) from tripskell._offre JOIN tripskell._avis ON tripskell._offre.idoffre = tripskell._avis.idoffre 
                    WHERE tripskell._offre.id_c = :idCompte AND luparpro = false"; //compte le nombre d'avis déposés sur les offres du pro qu'il n'a pas encore lu

                    $stmt = $dbh->prepare($query);
                    $stmt->bindParam(":idCompte", $_SESSION["idCompte"]);
                    $stmt->execute();

                    $nbAvisNonLus = $stmt->fetch()["count"];

                    if ($nbAvisNonLus == 0) {
                    ?>
                        <h3 id="cptAvisNonLus">Vous n'avez pas de nouvel avis</h3>
                    <?php
                    } else if ($nbAvisNonLus == 1) {
                    ?>
                        <h3 id="cptAvisNonLus">Vous avec <span>1</span> nouvel avis</h3>
                    <?php
                    } else {
                    ?>
                        <h3 id="cptAvisNonLus">Vous avec <span><?php echo $nbAvisNonLus ?></span> nouveaux avis !</h3>
                    <?php
                    }
                    ?>
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
                            <p id="txtBtnNote" class="txtBtnTrie">note</p>
                        </div>
                    </section>

                    <?php
                    foreach ($offres as $offre) {
                        $query =    "SELECT COUNT(*) from tripskell._avis WHERE idoffre = :idOffre AND luparpro = false";
                        $stmt = $dbh->prepare($query);

                        $stmt->bindParam(":idOffre", $offre['idoffre']);

                        $stmt->execute();

                        $nbAvisNonLusOffre = $stmt->fetch()["count"];

                    ?>
                        <section class="conteneurAvisOffre">
                            <div class="conteneurBtnAndBlacklist" >
                                <div class="conteneurBtnTitre" id="offre<?php echo $offre["idoffre"]; ?>">
                                    <img src="../icones/chevronUpSVG.svg" alt="chevron ouvrir/fermer">
                                        <div class="conteneurTitrePastille">
                                            <h3><?php echo $offre["titreoffre"] ?></h3>
                                            <?php
                                            if ($nbAvisNonLusOffre > 0) {
                                            ?>
                                                <p class="pastilleCptAvisNonLus"></p>
                                            <?php
                                            }
                                            ?>
                                        </div>
                                </div>
                                <?php if ($offre['id_abo'] == 'Premium') {
                                    /* On récupère les tokens pour le blacklistage */
                                    $stmt = $dbh->prepare("select count(*) as nbtoken from tripskell._avis where idoffre = " . $offre['idoffre'] . " and date_recup_token_blacklist is not NULL and date_recup_token_blacklist>now();");
                                    $stmt->execute();   // execution de la requete
                                    $nbTokenBlacklist = $stmt->fetchAll()[0];
                                    //print_r($nbTokenBlacklist); 
                                ?>
                                    <h4>Blacklistage restant :<?php
                                                                if ($nbTokenBlacklist['nbtoken'] == 0) {
                                                                    echo " 3";
                                                                } elseif ($nbTokenBlacklist['nbtoken'] == 1) {
                                                                    echo " 2";
                                                                } elseif ($nbTokenBlacklist['nbtoken'] == 2) {
                                                                    echo " 1";
                                                                } elseif ($nbTokenBlacklist['nbtoken'] >= 3) {
                                                                    echo " 0";
                                                                } ?>/3</h4>
                                <?php } ?>
                            </div>

                            </div>
                            <div class="conteneurAvis">
                                <?php

                                $query =    "SELECT COUNT(*) from tripskell._avis WHERE idoffre = :idOffre AND luparpro = false";
                                $stmt = $dbh->prepare($query);
                                $stmt->bindParam(":idOffre", $offre[""]);

                                $stmt->execute();

                                $nbAvisNonLusOffre = $stmt->fetch()["count"];

                                $avis = $dbh->query("select * from tripskell._avis where idOffre=" . $offre['idoffre'] . ";")->fetchAll();


                                if ($avis != null) {
                                    foreach ($avis as $value) {
                                        afficheAvis($value);
                                    }
                                } else {
                                ?>
                                    <h3>Aucun avis déposé pour <?php echo $offre["titreoffre"] ?></h3>
                                <?php
                                }
                                ?>
                            </div>

                        </section>
                    <?php
                    }
                    ?>
                <?php
            } else {
                ?>
                    <h1>Mes avis</h1>

                    <section class="mainAvis">
                        <section id="conteneurTrie">
                            <div id="btnTrieDate" class="btnTrie grossisQuandHover" onclick="trierDate()">
                                <img src="/icones/trierSVG.svg" alt="iconeDate" id="iconeTrieDate" class="iconeTrie">
                                <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieDate1" class="iconeTrie displayNone">
                                <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieDate2" class="iconeTrie displayNone">
                                <p id="txtBtnDate" class="txtBtnTrie">date</p>
                            </div>
                            <div id="btnTrieNote" class="btnTrie grossisQuandHover" onclick="trierNote()">
                                <img src="/icones/trierSVG.svg" alt="iconeTrie" id="iconeTrieNote" class="iconeTrie">
                                <img src="/icones/trier1SVG.svg" alt="iconeTrie" id="iconeTrieNote1" class="iconeTrie displayNone">
                                <img src="/icones/trier2SVG.svg" alt="iconeTrie" id="iconeTrieNote2" class="iconeTrie displayNone">
                                <p id="txtBtnNote" class="txtBtnTrie">note</p>
                            </div>
                        </section>

                        <section class="conteneurAvis">

                            <?php
                            foreach ($avis as $value) {
                                afficheAvis($value);
                            }
                            ?>
                        </section>
                    </section>
                <?php
            }
        
    dependances_avis();
    ?>
    </main>
    <?php                                                   //import footer
    include "../composants/footer/footer.php";
    ?>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/avis.js"></script>
    <script src="../js/affichageAvis.js"></script>

    <?php 
    if($compteMembre)
    {
        ?>
            <script src="../js/animationApparition.js"></script>
        <?php
    }
    ?>
</body>

</html>