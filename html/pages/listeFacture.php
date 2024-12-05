<?php
session_start(); // recuperation de la sessions

// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
include('../php/verif_mois.php');

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

//print_r($contentOffre);

$contentDerniereFacture = $contentOffre[0];

$dateCreaRecente = $dbh->query("SELECT max(date_creation) FROM tripskell.facture;")->fetchAll()[0];
//print_r($dateCreaRecente);

$dateDebutFacture = $dateCreaRecente['max'];
//echo $dateDebutFacture;

// Conversion de la date de publication en timestamp
$datePublicationTimestamp = strtotime($dateDebutFacture);

// Calcul du premier jour du mois suivant à partir de la date de publication
$firstDayNextMonthTimestamp = strtotime('first day of next month', $datePublicationTimestamp);
$firstDayNextMonth = date('Y-m-d', $firstDayNextMonthTimestamp);
//echo $firstDayNextMonth;



// Timestamp actuel (date du jour)
$today = date('Y-m-d');
//echo $today;
//echo $dateCreaRecente['date_creation'];
$todayTimestamp = strtotime($today);

// Calcul du nombre de jours écoulés depuis la date de publication
$daysElapsed = floor(($firstDayNextMonthTimestamp - $todayTimestamp) / (60 * 60 * 24));

//print_r($contentDerniereFacture);

// Vérification pour éviter d'incrémenter après le début du mois suivant
if ($todayTimestamp >= $firstDayNextMonthTimestamp) {
    $datePublicationTimestamp = $firstDayNextMonthTimestamp;
    $firstDayNextMonthTimestamp = strtotime('first day of next month', $datePublicationTimestamp);
    // Découper la chaîne de date en utilisant le séparateur '-'
    $dateParts = explode('-', $dateDebutFacture);
    // Assigner les valeurs aux variables
    $year = $dateParts[0]; // Année
    $month = $dateParts[1]; // Mois
    $day = $dateParts[2]; // Jour
    if (isDateInMonth($dateDebutFacture, $month, $year) == false) {
        $stmt = $dbh->prepare(
            "insert into tripskell.facture (id_facture,idOffre, date_creation) values (DEFAULT," . $idOffre . ", now());"
        );
        $stmt->execute();
        $result = $stmt->fetchAll();
    }
}

// Initialisation du compteur (valeur minimale 0)
$counter = max(0, $daysElapsed);

// Affichage du compteur
echo "Compteur quotidien : " . $counter . "\n";

// if($contentOffre['enLigne'] == false){

// }


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

        <link rel="stylesheet" href="/style/pages/listeFacture.css">
    </head>

    <body class="fondPro">

        <?php include "../composants/header/header.php";        //import navbar
        ?>

        <main>
            <h1>Liste facture</h1>
            <div class="divTab">
                <table>
                    <thead>
                        <tr>
                            <th>Facture</th>
                            <th>Date facture</th>
                            <th>Visualiser la facture</th>
                            <th>télécharger la facture</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($contentOffre as $row) {
                        ?>
                            <tr>
                                <td><?php echo "Facture N°" . $row["id_facture"]; ?></td>
                                <td><?php echo $row["date_creation"]; ?></td>
                                <td><a href="visualisationFacture.php?id_facture=<?php echo $row['id_facture']; ?>">Visualiser</a></td>
                                <td><a href="telechargementFacture.php?id_facture=<?php echo $row['id_facture']; ?>">télécharger</a></td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </main>

        <?php
        include "../composants/footer/footer.php";
        ?>
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
        <link rel="stylesheet" href="/style/pages/CreaOffrePro.css">
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