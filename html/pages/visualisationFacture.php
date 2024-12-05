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


// Récupération de l'identifiant de la facture si présent dans l'URL
$id_facture = null;
if (key_exists("id_facture", $_GET)) {
    $id_facture = $_GET["id_facture"]; // Récupération de l'identifiant de la facture

    // Récupération des détails de la facture à partir de la base de données
    $contentAboOptFacture = $dbh->query("SELECT * FROM tripskell.facture WHERE id_facture=" . $id_facture . ";")->fetchAll();
    $contentFacture = $contentAboOptFacture[0];
}

    $nbSemaineOption = $dbh->query("SELECT FLOOR(EXTRACT(EPOCH FROM (dateFinSouscription - dateDebutSouscription)) / (7 * 24 * 60 * 60)) AS weeks FROM tripskell.facture where id_facture = " . $id_facture . ";")->fetchAll()[0];
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
        <?php print_r($contentFacture);
        echo "echo";
        print_r($nbSemaineOption);
        ?>
        <h1><?php echo "Facture N°" . $contentFacture['id_facture'] . " de l'offre " . $contentFacture['titreoffre']; ?></h1>

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
                        <td>Raison social : <?php echo $contentFacture['raison_social']; ?></td>
                        <td>Raison social : Tripskell</td>
                    </tr>
                    <tr>
                        <td>Adresse : <?php echo $contentFacture['numero'] . " " . $contentFacture['rue'] . " " . $contentFacture['ville']; ?></td>
                        <td>Adresse : 12 Rue de l'alma , Rennes, Bretagne</td>
                    </tr>
                    <tr>
                        <td>Code Postal : <?php echo $contentFacture['codepostal']; ?></td>
                        <td>Code Postal : 35238</td>
                    </tr>
                    <tr>
                        <td>Numéro SIREN : </td>
                        <td>Numéro de téléphone : +33 1 23 45 67 89</td>
                    </tr>
                    <tr>
                        <td>Numéro de téléphone : <?php echo $contentFacture['numero_tel']; ?></td>
                    </tr>
                    <tr>
                        <td>Adresse mail : <?php echo $contentFacture['adresse_mail']; ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="divTexte">
            <p>Info facture abonnement</p>
            <p>Date début de l'abonnement : <?php echo $contentFacture['datedebut']; ?>
            <p>Date d'écheance de l'abonnement: <?php echo $contentFacture['datefin']; ?></p>
            <p>Date de la prestation : <?php echo $contentFacture['date_creation']; ?></p>
        </div>
        <div class="divTab">
            <table id="infoFacture">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Durée</th>
                        <th>Prix HT</th>
                        <th>Total HT</th>
                        <th>Prix TTC</th>
                        <th>Total TTC</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $contentFacture['id_abo']; ?></td>
                        <td></td>
                        <td><?php if ($contentFacture['id_abo'] == 'Standard') {
                                echo "1,67 € HT";
                            } else {
                                echo "3,34 € HT";
                            } ?></td>
                        <td></td>
                        <td><?php if ($contentFacture['id_abo'] == 'Standard') {
                                echo "2 € HT";
                            } else {
                                echo "4 € HT";
                            } ?></td>
                        <td></td>
                    </tr>
                    <?php foreach ($contentAboOptFacture as $row) { ?>
                        <tr>
                            <td><?php echo $row['id_option']; ?></td>
                            <td><?php echo $nbSemaineOption['weeks'] . " semaines"; ?></td>
                            <?php if ($row['id_option'] == 'En relief') { ?>
                                <td>
                                    <?php $val = 8.34; echo "8,34 € HT"; ?>
                                </td>
                            <?php } else { ?>
                                <td>
                                    <?php $val = 16.69; echo "16,69 € HT"; ?>
                                </td>
                            <?php } ?>
                            <td><?php echo $nbSemaineOption['weeks']*$val . " €"; ?></td>
                            <?php if ($row['id_option'] == 'En relief') { ?>
                                <td>
                                    <?php $val = 10; echo "10 € HT"; ?>
                                </td>
                            <?php } else { ?>
                                <td>
                                    <?php $val = 20; echo "20 € HT"; ?>
                                </td>
                            <?php } ?>
                            <td><?php echo $nbSemaineOption['weeks']*$val . " €"; ?></td>
                        </tr>
                    <?php } ?>
                    <!-- <?php //foreach ($contentOffre as $row) { 
                            ?>
                        <tr>
                            <td>Standard</td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    <?php //} 
                    ?> -->
            </table>
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