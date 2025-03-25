<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_pro.php');

// cree $compteMembre qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_membre.php');

// contient fonction caf_offre pour afficher les offres
include('../composants/affichage/affichage_offre.php');

if ($comptePro)      /* prépare la requête pour récupérer les offres à afficher : offres du pro si connecté en tant que pro, toutes les 
                         offres sinon */ {
    $stmt = $dbh->prepare("select * from tripskell.offre_pro where id_c=:id_c and enLigne");

    // binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
    $stmt->bindParam(":id_c", var: $id_c);
    $id_c = $_SESSION["idCompte"];
} else {
    $stmt = $dbh->prepare("select * from tripskell.offre_visiteur as p;");
}

$stmt->execute();
$rows = $stmt->fetchAll();          // rows : les offres à afficher

/* On compte le nombre d'offre à la une qu'il y a pour le carroussel*/
$stmt = $dbh->prepare("select count(idOffre) from tripskell.offre_visiteur as p where p.id_option='A la une';");

$stmt->execute();
$nbOffreALaUne = $stmt->fetchAll()[0];

/* On récupère toute les offres pour les afficher sur la page d'un visiteur ou membre */
$stmt = $dbh->prepare("select * from tripskell.offre_visiteur as p  where p.id_option='A la une';");

$stmt->execute();
$offreALaUne = $stmt->fetchAll();

// On recherche les 10 dernières offres
$stmt = $dbh->prepare("SELECT * FROM tripskell.offre_visiteur ORDER BY datepublication DESC LIMIT 10;");

$stmt->execute();
$nouvellesOffres = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/style.css">
</head>

<body id="mainAccueil" class=<?php                          //met le bon fond en fonction de l'utilisateur
                                if ($comptePro) {
                                    echo "fondPro";
                                } else {
                                    echo "fondVisiteur";
                                }
                                ?>>
    <?php include "../composants/header/header.php";        //import navbar

    ?>

    <div class="titrePortable">

        <svg width="401" height="158" viewBox="0 0 401 158" fill="none" xmlns="http://www.w3.org/2000/svg"> <!-- SVG pour  -->
            <g filter="url(#filter0_d_169_4380)">
                <ellipse cx="169.5" cy="61" rx="231.5" ry="89" fill="white" />
            </g>
            <defs>
                <filter id="filter0_d_169_4380" x="-66" y="-28" width="471" height="186" filterUnits="userSpaceOnUse" color-interpolation-filters="sRGB">
                    <feFlood flood-opacity="0" result="BackgroundImageFix" />
                    <feColorMatrix in="SourceAlpha" type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 127 0" result="hardAlpha" />
                    <feOffset dy="4" />
                    <feGaussianBlur stdDeviation="2" />
                    <feComposite in2="hardAlpha" operator="out" />
                    <feColorMatrix type="matrix" values="0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0.25 0" />
                    <feBlend mode="normal" in2="BackgroundImageFix" result="effect1_dropShadow_169_4380" />
                    <feBlend mode="normal" in="SourceGraphic" in2="effect1_dropShadow_169_4380" result="shape" />
                </filter>
            </defs>
        </svg>

        <div>
            <img src="/images/logo/logo_grand.png" alt="logo PACT" id="logoTitreMobile">
        </div>
    </div>
    <main>

        <?php
        if ($comptePro)                  //change le titre de la page
        {
        ?>
            <h1 class="displayNone">Mes offres en ligne</h1>

        <?php
        } else {
        ?>
            <h1>À la Une</h1>
            <?php if ($nbOffreALaUne['count'] >= 5) { ?>
                <div class="carrousel">
                    <div class="card">
                        <?php
                        foreach ($offreALaUne as $offre)          // parcourt les offres pour les afficher
                        {
                        ?>
                            <div class="card-body">
                                <a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                                    <?php
                                    af_offre($offre);
                                    ?>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                        <?php
                        foreach ($offreALaUne as $offre)          // parcourt les offres pour les afficher
                        {
                        ?>
                            <div class="card-body">
                                <a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                                    <?php
                                    af_offre($offre);
                                    ?>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php } elseif ($nbOffreALaUne['count'] == 0) { ?>
                <div class="carrousel">
                    <div class="card">
                        <?php
                        for ($i = 0; $i < 5; $i++)          // parcourt les offres pour les afficher
                        {
                        ?>
                            <div class="card-body">
                                <a href="/pages/CreaComptePro.php" class="grossisQuandHover">
                                    <img src="/images/baniere/pubBaniere1.png" alt="pubBaniere" class="pubBanniere">
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                    </div>
                </div>
            <?php } else { ?>
                <div class="carrousel">
                    <div class="card">
                        <?php
                        foreach ($offreALaUne as $offre)          // parcourt les offres pour les afficher
                        {
                        ?>
                            <div class="card-body">
                                <a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                                    <?php
                                    af_offre($offre);
                                    ?>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="card-body">
                            <a href="/pages/CreaComptePro.php?user-tempo=pro" class="grossisQuandHover">
                                <article>
                                    <img src="/images/banniere/pubBanniere<?php echo rand(1, 3); ?>.png" alt="pubBanniere" class="pubBanniere">
                                </article>
                            </a>
                        </div>
                        <?php
                        foreach ($offreALaUne as $offre)          // parcourt les offres pour les afficher
                        {
                        ?>
                            <div class="card-body">
                                <a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                                    <?php
                                    af_offre($offre);
                                    ?>
                                </a>
                            </div>
                        <?php
                        }
                        ?>
                        <div class="card-body">
                            <a href="/pages/CreaComptePro.php?user-tempo=pro" class="grossisQuandHover">
                                <article>
                                    <img src="/images/banniere/pubBanniere<?php echo rand(1, 3); ?>.png" alt="pubBanniere" class="pubBanniere">
                                </article>
                            </a>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <h1>Nouveautés</h1>
            <section id="conteneurOffres" class="conteneurOffres">
                <article>
                    <?php

                    foreach ($nouvellesOffres as $offre)          // parcourt les offres pour les afficher
                    {
                    ?><a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                            <?php
                            af_offre($offre);
                            ?></a><?php
                            }
                                ?>
                </article>
            </section>

            <h1>Autres offres</h1>
        <?php } ?>

        <section class="conteneurOffres">
            <article>
                <?php

                foreach ($rows as $offre)          // parcourt les offres pour les afficher
                {
                ?><a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                        <?php
                        af_offre($offre);
                        ?></a>
                <?php
                }
                ?>
            </article>
        </section>
<?php
            if ($comptePro) {
?>
                <h1>Nouveautés</h1>
                <section class="conteneurOffres">
                    <article>
                        <?php

                        foreach ($nouvellesOffres as $offre)          // parcourt les offres pour les afficher
                        {
                        ?><a href="/pages/detailOffre.php?idOffre=<?php echo $offre["idoffre"]; ?>" class="lienApercuOffre grossisQuandHover">
                                <?php
                                af_offre($offre);
                                ?></a><?php
                                    }
                                        ?>
                    </article>
                </section>
<?php
            }
?>

    </main>
    <?php
    include "../composants/footer/footer.php";
    ?>
    <script src="../js/acceuil.js"></script>
</body>

</html>