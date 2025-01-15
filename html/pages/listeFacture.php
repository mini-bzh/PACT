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


/* On récupère ici toute les factures qui existe sous l'id_c */
$contentFacture = $dbh->query("SELECT id_facture, date_creation from tripskell.facture where id_c = " . $id_c . " order by date_creation DESC;")->fetchAll();
//var_dump($contentFacture);



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
            <div class="divListeFacture">
                <?php
                foreach ($contentFacture as $row) {
                ?>
                <div class="divFacture">
                    <p>Facture N°<?php echo $row["id_facture"]; ?></p>
                    <p><?php echo $row["date_creation"]; ?></p>
                    <div class="btnFacture"><a href="contentFacture.php?id_facture=<?php echo $row['id_facture']; ?>">Visualiser</a></div>
                    <div class="btnFacture"><a href="telechargementFacture.php?id_facture=<?php echo $row['id_facture']; ?>">télécharger</a></div>
                </div>
                    <hr/>
                <?php
                }
                ?>
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