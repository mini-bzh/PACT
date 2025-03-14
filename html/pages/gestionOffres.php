<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../composants/bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../composants/verif/verif_compte_pro.php');

// contient fonction affichage_etoiles pour afficher les etoiles
include('../composants/affichage/etoiles.php');

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

    <link rel="stylesheet" href="/style/style.css">
    <link rel="stylesheet" href="/style/pages/compte.css">
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
                                    <span class="enLigne" id="txtEnLigne">En ligne</span>
                            </h4>
                        <?php } else { ?>
                            <span class="horsLigne" id="txtEnLigne">Hors ligne</span></h4>
                        <?php } ?>
                        </div>


                        <?php if($contentOffre['id_abo'] == 'Premium'){ ?> 
                            <div class="tokenBlacklist">
                                <?php
                                    /* On récupère les tokens pour le blacklistage */
                                    $stmt = $dbh->prepare("select count(*) as nbtoken from tripskell._avis where idoffre = " . $contentOffre['idoffre'] . " and date_recup_token_blacklist is not NULL and date_recup_token_blacklist>now();");
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
                            
                                <div class="etoiles">
                                    <?php affichage_etoiles($contentOffre["note"]); ?>
                                </div>
                            </div>
                        <?php }else{?>
                            <div class="noTokenBlacklist">            
                                <div class="etoiles">
                                    <?php affichage_etoiles($contentOffre["note"]); ?>
                                </div>
                            </div>
                        <?php } ?>

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

                            <a href="#" class="btnSupprimerOffre <?php if ($contentOffre["enligne"]) { echo "btnModifCache"; } ?>">
                                <div class="btnGestionOffre grossisQuandHover" onclick="confSupOffre(<?php echo $contentOffre['idoffre']; ?>)">
                                    <img src="/icones/supprimerSVG.svg" alt=""/>
                                    <p>Supprimer l'offre</p>
                                </div>
                            </a>

                            <!-- POP-UP de suppression d'offre -->
                            <div class="popUpSupOffre popUp">
                                <div class="popup-content">
                                    <p class="ajoutBorder">Pour valider la suppression de l'offre, veuillez entrer votre mot de passe :</p>
                                    <p id="textNonValideOffre" class="displayNone remplirChampsError" style="color: red">Mot de passe incorrect !</p>

                                    <div class="popup-suppr">
                                        <label for="pswSupOffre">Mot de passe :</label>
                                        <input id="pswSupOffre" name="pswSupOffre" type="password" placeholder="Mot de passe">
                                    </div>
                                    <p class="boldArchivo" style="color: red">Cette action est irréversible !</p>
                                    <div class="btnSup">

                                        <button class="btnValiderSupOffre" onclick="suppressionOffre()" disabled>
                                            Confirmer
                                            <!-- Ajouter une icône de suppression -->
                                        </button>
                                        <button class="btnAnnulerSupOffre" onclick="fermeConfSupOffre()">
                                            Annuler
                                            <!-- Ajouter une icône de fermeture -->
                                        </button>
                                    </div>
                                </div>
                            </div>

<!-- Champ caché pour l'ID de l'offre à supprimer -->
<input type="hidden" id="idOffre" name="idOffre" value="">

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
<script src="/js/suppressionOffre.js"></script>



</html>