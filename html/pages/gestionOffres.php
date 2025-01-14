<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../php/verif_compte_pro.php');

// contient fonction affichage_etoiles pour afficher les etoiles
include('../php/etoiles.php'); 

// Creation requete pour recuperer les offres
// du professionnel connecte
$stmt = $dbh->prepare("select * from tripskell.offre_pro where id_c=:id_c;");

// binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
$stmt->bindParam(":id_c", var: $id_c);
$id_c = $_SESSION["idCompte"];

$stmt->execute();   // execution de la requete

// recuperation de la reponse et mise en forme
$contentMesOffres = $stmt->fetchAll();

// $stmt = $dbh->query("SELECT * FROM tripskell.facture WHERE idoffre=" . $contentMesOffres['idoffre'] . ";");
// $stmt->execute();
// $contentOffre = $stmt->fetchAll()[0];

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>gestion des offre</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="/style/pages/gestionOffres.css">
</head>

<body class=<?php echo "fondPro"; ?>> <!-- met le bon fond en fonction de l'utilisateur -->
    <?php
    include "../composants/header/header.php";              // import header (navbar)
    ?>
    <main id="mainGestionOffres">

        <?php
        // Un tableau pour stocker les données sur le prix des offres et des option
        $donneePrix = [];
        ?>

        <section id="conteneurBtnOffres">
            <a href="CreaOffrePro.php" id="btnAjouterOffre" class="grossisQuandHover">
                <div class="conteneurSVGtexte">
                    <img src="/icones/ajouterSVG.svg" alt="ajouter offre">
                    <h3>Ajouter une offre</h3>
                </div>
            </a>
            <section id="conteneurOffre">
                <?php foreach ($contentMesOffres as $contentOffre)                   // ajout des offres du professionnel récupérées plus tôt
                {
                    if ($contentOffre["enligne"]) {
                        $prixOffre = []; // Tableau pour stocker les prix d'une offre et de son option

                        $prixOffre['prixOffre'] = $contentOffre["prix_abo"];
                        $prixOffre['prixOption'] = $contentOffre["prix_option"];

                        $donneePrix[] = $prixOffre; // Ajout des données de l'offre dans donneePrix
                    }


                ?>
                    <article class="offre" id="offre<?php echo $contentOffre['idoffre'] ?>">
                        <div class="conteneurSpaceBetween">
                            <h2><?php echo $contentOffre["titreoffre"] ?></h2>
                            <h4>Statut :
                                    <?php
                                    if ($contentOffre["enligne"])    // définit l'affichage du statut de l'offre en fonction de en ligne / hors ligne
                                    { ?>
                                        <span class="enLigne" id="txtEnLigne">En ligne</span></h4>
                                    <?php } else { ?>
                                        <span class="horsLigne" id="txtEnLigne">Hors ligne</span></h4>
                                    <?php } ?>
                        </div>
                        
                        <div class="etoiles">
                            <?php affichage_etoiles($contentOffre["note"]); ?>
                        </div>

                        <!--gestion du bouton de mise en/hors ligne-->
                        <div id="conteneurBtnGestion">
                            <div class="btnGestionOffre grossisQuandHover" id="btnEnHorsLigne" onclick="toggleEnLigne(<?php echo $contentOffre['idoffre'] ?>)">
                                <?php
                               
                                if ($contentOffre["enligne"])    // définit l'affichage du bouton de mise en/hors ligne
                                {
                                ?>
                                    <img src="/icones/horsLigneSVG.svg" alt="svg hors ligne" id="imgEnHorsLigne">
                                <?php
                                } else {

                                ?>
                                    <img src="/icones/enLigneSVG.svg" alt="svg en ligne" id="imgEnHorsLigne">
                                <?php
                                }
                                ?>

                                <?php
                                if ($contentOffre["enligne"])    // définit l'affichage du bouton de mise en/hors ligne
                                {
                                ?>
                                    <p id="txtEnHorsLigne">Mettre l'offre hors ligne</p>
                                <?php
                                } else {
                                ?>
                                    <p id="txtEnHorsLigne">Mettre l'offre en ligne</p>
                                <?php
                                }
                                ?>
                                </p>
                            </div>
                            <a href="modifOffre.php?idOffre=<?php echo $contentOffre['idoffre'] ?>" class="<?php
                                if ($contentOffre["enligne"])    // cache le bouton modifier si l'offre est en ligne
                                {
                                    echo "btnModifCache";
                                }
                                ?> btnModif">
                                <div class="btnGestionOffre grossisQuandHover">
                                    <img src="/icones/crayonSVG.svg" alt="">
                                    <p>Modifier l'offre </p>

                                </div>
                            </a>
                            <!--gestion du bouton pour redirection pour la facture-->
                            
                            <a href="listeFacture.php?idOffre=<?php echo $contentOffre['idoffre'] ?>" class="styleBtn">
                                <div class="btnGestionOffre grossisQuandHover">
                                    <img src="/icones/creditCardSVG.svg" alt="">
                                    <p>Voir facture </p>
                                </div>
                            </a>
                        </div>

                    </article>
                <?php }
                ?>
            </section>


            <setion id="conteneurDetailPrix">

                <p>Total à payer :
                    <?php
                    $total = 0;
                    foreach ($donneePrix as $key => $value) {
                        $total += array_sum($value);
                    }
                    echo $total;
                    ?> €
                </p>
            </section>
        
<!--
        <setion id="conteneurDetailPrix">

            <p>Total à payer :
                <?php
                $total = 0;
                foreach ($donneePrix as $key => $value) {
                    $total += array_sum($value);
                }
                echo $total;
                ?> €
            </p>

            </section>-->
        </section>
    </main>
    <?php                                                   //footer
    include "../composants/footer/footer.php";
    ?>
</body>
<!--<script src="../js/scriptImageChangeante.js"></script>-->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="/js/gestionOffre.js"></script>

</html>