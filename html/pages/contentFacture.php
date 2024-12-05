<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// cree $comptePro qui est true quand on est sur un compte pro et false sinon
include('../php/verif_compte_pro.php');

if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}

// binding pour l'id du compte (id_c <- idCompte(dans $_SESSION))
$id_c = $_SESSION["idCompte"];

if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];

    // reccuperation de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c='" . $_SESSION["idCompte"] . "';")->fetchAll()[0];
}


// Récupération de l'identifiant de l'offre si présent dans l'URL
$idOffre = null;
if (key_exists("idOffre", $_GET)) {
    $idOffre = $_GET["idOffre"]; // Récupération de l'identifiant de l'offre

    // Récupération des détails de l'offre à partir de la base de données
    $contentOffre = $dbh->query("SELECT * FROM tripskell.facture WHERE idOffre='" . $idOffre . "';")->fetchAll();
}
?>
<?php
if (in_array($_SESSION["idCompte"], $idproprive)) {
?>

    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>gestion des offre</title>

        <!-- Favicon -->
        <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

        <link rel="stylesheet" href="/style/pages/contentFacture.css">
    </head>

    <body>
        <h1><?php echo "Facture N°" . $contentOffre['id_facture'] . " de l'offre " . $contentOffre['titreOffre']?></h1>

        <div class="divTab">
            <table id="infoClient">
                <thead>
                    <tr>
                        <th>Client</th>
                        <th>Plateform</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Raison social : </td>
                        <td><?php echo $contentOffre['raison_social']?></td>
                        
                    </tr>
                    <tr>
                        <td>Adresse :</td>
                        <td><?php echo $contentOffre['numero'] . " " . $contentOffre['rue'] . " " . $contentOffre['ville']?></td>
                    </tr>
                    <tr>
                        <td>Code Postal :</td>
                        <td><?php echo $contentOffre['codePostal']?></td>
                    </tr>
                    <tr>
                        <td>Numéro SIREN :</td>
                        <td><?php echo $contentOffre['num_siren']?></td>
                    </tr>
                    <tr>
                        <td>Numéro de téléphone :</td>
                        <td><?php echo $contentOffre['num_tel']?></td>
                    </tr>
                    <tr>
                        <td>Adresse mail :</td>
                        <td><?php echo $contentOffre['adresse_mail']?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="divTexte">
            <p>Info facture abonnement</p>
            <p>Date début de l'abonnement : <?php echo $contentOffre['dateDebut']?>
            <p>Date d'écheance de l'abonnement: <?php echo $contentOffre['dateFin']?></p>
            <p>Date de la prestation : <?php echo $contentOffre['date_creation']?></p>
        </div>
        <div class="divTab">
            <table id="infoFactureAbonnement">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Nombre</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Standard</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Premium</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td>1222220000</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="divTab">
            <table id="infoFactureOption">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prix</th>
                        <th>Nombre</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Standard</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td>Premium</td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
                <tbody>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>Total</td>
                        <td>1222220000</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="divTexte">
            <p> date règlement : </p>

        </div>
    </body>

    </html>
<?php
} else { // si id_c n'est pas dans pro_prive on génère une erreur 404.
?>
    <!DOCTYPE html>
    <html lang="fr">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Creation Offre</title>
        <link rel="stylesheet" href="/style/pages/contentFacture.css">
    </head>

    <body class="fondPro">

        <?
        include "../composants/header/header.php";        //import navbar
        ?>

        <main>
            <h1> ERROR 404 </h1>
        </main>

        <?php
        include "../composants/footer/footer.php";
        ?>
    <?php
}
    ?>