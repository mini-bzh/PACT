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

$firstDayNextMonth = strtotime('first day of next month', strtotime($contentFacture['date_creation']));

//on récupère dans la base de donnée le nombre de jour et de semaine qui sépare le début et la fin de l'abonnement ou option
$nbSemaineOption = $dbh->query("SELECT FLOOR(EXTRACT(EPOCH FROM (dateFinSouscription - dateDebutSouscription)) / (7 * 24 * 60 * 60)) AS weeks FROM tripskell.facture where id_facture = " . $id_facture . ";")->fetchAll()[0];

$firstDayNextMonthFormatted = date('Y-m-d', $firstDayNextMonth);

// Préparer la requête
$sql = "SELECT (DATE('$firstDayNextMonthFormatted') - DATE(date_creation))::INTEGER AS jours FROM tripskell.facture WHERE id_facture = :id_facture;";

// Exécuter la requête avec un paramètre sécurisé
$stmt = $dbh->prepare($sql);
$stmt->execute([':id_facture' => $id_facture]);

// Récupérer le résultat
$nbJourAbo = $stmt->fetch(PDO::FETCH_ASSOC);

// echo $contentFacture['date_creation'];
// echo $nbJourAbo;

// déclaration de deux variables de stockage pour stocker les valeurs HT et TTC
$valAboHT = 0;
$valAboTTC = 0;
$valOptHT = 0;
$valOptTTC = 0;
$resHTopt = 0;
$resTTCopt = 0;
$resHTabo = 0;
$resTTCabo = 0;

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
        <?php /*print_r($contentFacture);
        echo "echo";
        print_r($nbSemaineOption);
        print_r($nbJourAbo);*/
        ?>
        <header>
            <img class="logoHeader" src="/images/logo/logo_grand.png" alt="logo PACT">
            <h1><?php echo "Facture N°" . $contentFacture['id_facture'] . " de l'offre " . $contentFacture['titreoffre']; ?></h1>
        </header>
        <main>
            <div id="enTeteFac">
                <h2>Facture datant du <?php echo $contentFacture['date_creation']; ?></h2>
                <p>Le règlement se fera le <?php echo date('Y-m-d', $firstDayNextMonth);?></p>
            </div>
            <div class="divTab">
                <table id="infoClient">
                    <thead>
                        <tr>
                            <th>Tripskell</th>
                            <th>Client</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Raison sociale : Tripskell</td>
                            <td>Raison sociale : <?php echo $contentFacture['raison_social']; ?></td>
                        </tr>
                        <tr>
                            <td>Adresse : 12 Rue de l'alma , Rennes</td>
                            <td>Adresse : <?php echo $contentFacture['numero'] . " " . $contentFacture['rue'] . " " . $contentFacture['ville']; ?></td>
                        </tr>
                        <tr>
                            <td>Code Postal : 35238</td>
                            <td>Code Postal : <?php echo $contentFacture['codepostal']; ?></td>
                        </tr>
                        <tr>
                            <td>Numéro de téléphone : +33 1 23 45 67 89</td>
                            <td>Numéro SIREN :  <?php echo $contentFacture['num_siren']; ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Numéro de téléphone : <?php echo $contentFacture['numero_tel']; ?></td>
                        </tr>
                        <tr>
                            <td></td>
                            <td>Adresse mail : <?php echo $contentFacture['adresse_mail']; ?></td>
                        </tr>
                    </tbody>
                </table>
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
                            <th>Total à payer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><?php echo $contentFacture['id_abo']; ?></td>
                            <td><?php echo $nbJourAbo['jours'] . " jours"; ?></td>
                            <td><?php if ($contentFacture['id_abo'] == 'Standard') {
                                    $valAboHT = 1.67;
                                    echo "1,67 € HT";
                                } else {
                                    $valAboHT = 3.34;
                                    echo "3,34 € HT";
                                } ?></td>
                            <td><?php $resHTabo += $nbJourAbo['jours'] * $valAboHT; echo $nbJourAbo['jours'] * $valAboHT . " €"; ?></td>
                            <!-- <td><?php //echo $resHTabo . " €"; ?></td> -->
                            <td><?php if ($contentFacture['id_abo'] == 'Standard') {
                                    $valAboTTC = 2;
                                    echo "2 € TTC";
                                } else {
                                    $valAboTTC = 4;
                                    echo "4 € TTC";
                                } ?></td>
                            <td><?php $resTTCabo += $nbJourAbo['jours'] * $valAboTTC; echo $nbJourAbo['jours'] * $valAboTTC . " €"; ?></td>
                            <td><?php echo $resTTCabo . " €"; ?></td>
                        </tr>
                        <?php 
                        foreach ($contentAboOptFacture as $row){
                        if($row['id_option'] != null){ ?>
                            <tr>
                                <td><?php echo $row['id_option']; ?></td>
                                <td><?php echo $nbSemaineOption['weeks'] . " semaines"; ?></td>
                                <?php if ($row['id_option'] == 'En relief') { ?>
                                    <td>
                                        <?php $valOptHT = 8.34;
                                        echo "8,34 € HT"; ?>
                                    </td>
                                <?php } else { ?>
                                    <td>
                                        <?php $valOptHT = 16.69;
                                        echo "16,69 € HT"; ?>
                                    </td>
                                <?php } ?>
                                <td><?php $resHTopt += $nbSemaineOption['weeks'] * $valOptHT; echo $nbSemaineOption['weeks'] * $valOptHT . " €"; ?></td>
                                <?php if ($row['id_option'] == 'En relief') { ?>
                                    <td>
                                        <?php $valOptTTC = 10;
                                        echo "10 € TTC"; ?>
                                    </td>
                                <?php } else { ?>
                                    <td>
                                        <?php $valOptTTC = 20;
                                        echo "20 € TTC"; ?>
                                    </td>
                                <?php } ?>
                                <td><?php $resTTCopt += $nbSemaineOption['weeks'] * $valOptTTC; echo $nbSemaineOption['weeks'] * $valOptTTC . " €"; ?></td>
                                <td><?php echo $resTTCopt . " €"; ?></td>
                            </tr>
                        <?php } } ?>
                            <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td><?php echo $resHTopt + $resHTabo . " €"; ?></td>
                                <td></td>
                                <td><?php echo $resTTCopt + $resTTCabo . " €"; ?></td>
                                <td><?php echo $resTTCopt + $resTTCabo . " €"; ?></td>
                            </tr>
                    </tbody>
                </table>
            </div>
        </main>
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