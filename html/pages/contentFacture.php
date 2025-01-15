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
    $contentFacture = $dbh->query("SELECT * FROM tripskell.facture WHERE id_facture=" . $id_facture . ";")->fetchAll();
    // Récupération des détails de la facture à partir de la base de données
    $infoClient = $dbh->query("SELECT * FROM tripskell.facture WHERE id_facture=" . $id_facture . ";")->fetchAll()[0];
    // On prend la date de création de la facture et on souhaite voir juste le mois
    $dateFacture = $dbh->query("SELECT 
    EXTRACT(MONTH FROM date_creation - INTERVAL '1 month') AS mois,
    EXTRACT(YEAR FROM date_creation - INTERVAL '1 month') AS annee,
    EXTRACT(MONTH FROM date_creation + INTERVAL '1 month') AS mois_suivant,
    EXTRACT(YEAR FROM date_creation + INTERVAL '1 month') AS annee_suivant
FROM 
    tripskell.facture WHERE id_facture=" . $id_facture . ";")->fetchAll()[0];
}

$valHT = 0;
$valTTC = 0;
$resHT = 0;
$resTTC = 0;
?>

<?php
if (in_array($_SESSION["idCompte"], $idproprive)) {
    print_r($contentFacture);
    print_r($dateFacture);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="../style/pages/contentFacture.css">
</head>

<!-- J'ai besoin du style ici sinon à la génération pdf cela ne l'applique pas ),: -->
<style>
    body {
    font-family: Arial, sans-serif;
    line-height: 1.6;
    margin: 0;
    padding: 0;
    background-color: #f9f9f9;
    color: #333;
}

.contentFacture {
    max-width: 800px;
    margin: 20px auto;
    background: #fff;
    padding: 5%;
    border: 1px solid #ddd;
    border-radius: 5px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

header {
    display: flex;
    justify-content: space-around;
    flex-direction: row;
    width: 100%;
    margin: 10px;
}

header h2 {
    margin: 0;
    font-size: 18px;
    color: #555;
}

.detailFacture {
    margin-bottom: 20px;
}

.detailFacture p {
    margin: 0;
    font-size: 14px;
}

.tableFacture {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
}

.tableFacture th,
.tableFacture td {
    border: 1px solid #ddd;
    padding: 8px;
    text-align: left;
    font-size: 14px;
}

.tableFacture th {
    background-color: #f4f4f4;
    font-weight: bold;
}

.tableFacture tfoot td {
    font-weight: bold;
    background-color: #f9f9f9;
}

.tableFacture .espaceVide{
    background-color: #555;
}

</style>
<body>
    <div class="contentFacture">
        <header>
            <div class="infoFournisseur">
                <h2>Entreprise proposant les services</h2>
                <p>Raison social : Tripskell</p>
                <p>Adresse : 12 Rue de l'alma , Rennes</p>
                <p>Code Postal  : 35238</p>
                <p>Numéro de téléphone : +33 1 23 45 67 89</p>
                <p>Adresse mail : tripskell.ventdouest@gmail.com</p>
            </div>
            <div class="infoClient">
                <h2>Client</h2>
                <p>Nom : <?php echo $infoClient['raison_social']; ?></p>
                <p>Adresse : <?php echo $infoClient['numero'] . " " . $infoClient['rue'] . " " . $infoClient['ville']; ?></p>
                <p>Code Postal  : <?php echo $infoClient['codepostal']; ?></p>
                <p>Numéro Siren : <?php echo $infoClient['num_siren']; ?></p>
                <p>Numéro de téléphone : <?php echo $infoClient['numero_tel']; ?></p>
                <p>Adresse mail : <?php echo $infoClient['adresse_mail']; ?></p>
            </div>
        </header>

        <div class="detailFacture">
            <p><strong>Facture N°<?php echo $id_facture?></strong></p>
            <p><strong>Date : <?php echo $dateFacture['mois'] . " " . $dateFacture['annee'];?></strong></p>
        </div>

        <table class="tableFacture">
            <thead>
                <tr>
                    <th>Désignation</th>
                    <th>Durée</th>
                    <th>Prix HT</th>
                    <th>Total HT</th>
                    <th>Prix TTC</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    foreach($contentFacture as $row){
                ?>
                <tr>
                    <td><?php echo $row['titreoffre'];?></td>
                    <td><?php echo $row['nbjourenligne'];?></td>
                    <td><p>1,67 €<p></td>
                    <td><?php echo 1.67*$row['nbjourenligne'];?></td>
                    <td><p>2 €<p></td>
                    <td><?php echo 2*$row['nbjourenligne'];?></td>
                </tr>
                <tr>
                    <td></td>
                    <td><?php echo $row['nbjourenligne'];?></td>
                    <td><p>3,34 €</p></td>
                    <td><?php echo 3.34*($row['duree_option']/7);?></td>
                    <td><p>4 €<p></td>
                    <td><?php echo 4*($row['duree_option']/7);?></td>
                </tr>
                <?php
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2" class="espaceVide"></td>
                    <td><strong>TOTAL HT</strong></td>
                    <td></td>
                    <td><strong>TOTAL TTC</strong></td>
                    <td></td>
                </tr>
            </tfoot>
        </table>

        <div class="detailFacture">
            <p><strong>Date maximum de règlement : <?php echo  "1/" . $dateFacture['mois_suivant'] . "/" . $dateFacture['annee_suivant']; ?></strong></p>
        </div>
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
    </body>
<?php
}
?>