<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../php/verif_compte_pro.php');

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
    <h1>
        Gestion des offres
    </h1>
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
                        <h2><?php echo $contentOffre["titreoffre"] ?></h2>
                        <!-- <p>Visite</p> future categorie -->
                        <div class="conteneurSpaceBetween">
                            <div class="noteDetailOffre">
                                <div class="etoiles">
                                    <p><?php echo $contentOffre["note"]; ?></p>
                                    <?php include "../php/etoiles.php"; ?>
                                </div>
                                <!-- <p>38 avis</p> -->
                            </div>
                            <div class="conteneurSVGtexte">
                                <img src="/icones/logoUserSVG.svg" alt="pro">
                                <p> <!-- insertion nom du professionel-->
                                    <?php echo $dbh->query("select raison_social from tripskell._professionnel as p where p.id_c='" . $contentOffre["id_c"] . "';")->fetchAll()[0]["raison_social"]; ?>
                                </p>
                            </div>
                        </div>

                        <div class="imgChg">
                            <img src="/images/imagesOffres/<?php echo $contentOffre["img1"]; ?>" alt="" id="imageChangeante"> <!-- insertion image de l'offre-->
                        </div>
                        <div class="resumePrixDetailOffre">
                            <p> <!-- insertion résumé de l'offre-->
                                <?php echo $contentOffre["resume"]; ?>
                            </p>
                            <hr>
                            <!-- Tarif minimal -->
                            <p>À partir de <?php echo $contentOffre["tarifminimal"]; ?>€/pers</p>
                        </div>
                        <p id="descriptionOffre"> <!-- insertion description de l'offre -->
                            <?php echo $contentOffre["description_detaille"]; ?>
                        </p>

                        <div class="conteneurSpaceBetween" id="conteneurTagsHoraires">
                            <div id="partieTags">
                                <div class="conteneurSVGtexte">
                                    <img src="/icones/tagSVG.svg" alt="icone tag">
                                    <h4>Tags</h4>
                                </div>
                                <hr>
                                <div id="conteneurTagsOffre">
                                    <?php
                                    $tags = $dbh->query("select * from tripskell._possede where idoffre='" . $contentOffre['idoffre'] . "';")->fetchAll();
                                    foreach ($tags as $key => $tag) {
                                        echo "<p class='tagOffre'>" . $tag["nomtag"] . "</p>";
                                    }
                                    ?>
                                </div>
                            </div>
                            <div id="partieHoraires">
                                <div class="conteneurSVGtexte">
                                    <img src="/icones/horairesSVG.svg" alt="icone horaires">
                                    <h4>Horaires</h4>
                                </div>
                                <hr>
                                <div id="conteneurJoursOffre">
                                    <table>
                                        <tbody>
                                            <?php
                                            $ouverture = $dbh->query("select * from tripskell._ouverture where idoffre='" . $contentOffre['idoffre'] . "';")->fetchAll();
                                            foreach ($ouverture as $key => $value) {
                                                $horaire = $dbh->query("select * from tripskell._horaire as h join tripskell._ouverture as o on h.id_hor=" . $ouverture[$key]["id_hor"] . " where o.idOffre=" . $contentOffre['idoffre'] . " and o.id_hor=" . $ouverture[$key]["id_hor"] . " and o.id_jour='" . $ouverture[$key]["id_jour"] . "';")->fetchAll();
                                            ?>
                                                <tr>
                                                    <th><?php echo $ouverture[$key]["id_jour"]; ?></th>
                                                    <td><?php echo $horaire[0]['horaire_matin_debut']; ?></td>
                                                    <td><?php echo $horaire[0]['horaire_matin_fin']; ?></td>
                                                    <?php
                                                    if (($horaire[0]['horaire_aprem_debut'] != NULL) && ($horaire[0]['horaire_aprem_fin'] != NULL)) {
                                                    ?>
                                                        <td><?php echo $horaire[0]['horaire_aprem_debut']; ?></td>
                                                        <td><?php echo $horaire[0]['horaire_aprem_fin']; ?></td>
                                                    <?php
                                                    }
                                                    ?>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                        <div id="partieAdresse"><!-- future tag -->
                            <div class="conteneurSVGtexte">
                                <img src="/icones/adresseSVG.svg" alt="icone tag">
                                <h4>Adresse</h4>
                            </div>
                            <hr>
                            <a href="https://www.google.fr/maps/place/<?php
                                                                        $adresse = $contentOffre["numero"] . " rue " . $contentOffre["rue"] . ", " . $contentOffre["ville"];
                                                                        echo $adresse;
                                                                        ?>"
                                class="conteneurSVGtexte" id="itineraire" target="_blank">
                                <p><?php
                                    echo ($adresse);
                                    ?></p>
                            </a>
                        </div>
                        <!--gestion du bouton de mise en/hors ligne-->
                        <hr id="separateurOffreGestion">
                        <div id="conteneurGestion">
                            <h4>Statut :
                                <?php
                                if ($contentOffre["enligne"])    // définit l'affichage du statut de l'offre en fonction de en ligne / hors ligne
                                {
                                ?>
                                    <span class="enLigne" id="txtEnLigne">En ligne</span>
                            </h4>
                        <?php
                                } else {
                        ?>
                            <span class="horsLigne" id="txtEnLigne">Hors ligne</span></h4>
                        <?php
                                }
                        ?>
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
                        <a href="modifOffre.php?idOffre=<?php echo $contentOffre['idoffre'] ?>" class="btnModif <?php
                                if ($contentOffre["enligne"])    // cache le bouton modifier si l'offre est en ligne
                                {
                                    echo "btnModifCache";
                                }
                            ?>">
                            <div class="btnGestionOffre grossisQuandHover">
                                <img src="/icones/crayonSVG.svg" alt="">
                                <p>Modifier l'offre </p>

                            </div>
                        </a>
                        <!--gestion du bouton pour redirection pour la facture-->
                         <!--       
                        <a href="listeFacture.php?idOffre=<?php echo $contentOffre['idoffre'] ?>" class="styleBtn">
                            <div class="btnGestionOffre grossisQuandHover">
                                <img src="/icones/creditCardSVG.svg" alt="">
                                <p>Voir facture </p>
                            </div>
                        </a>-->
                    </div>



                    </div>
                </article>
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