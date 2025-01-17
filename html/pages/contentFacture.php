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

if (key_exists("idCompte", $_SESSION))
{
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

    // formatage des donnees
    $contentFactureFormat = array();
    $exceptions = ['id_facture','date_creation', 'id_c', 'adresse_mail', 'numero_tel', 'num_siren', 'raison_social', 'numero', 'rue', 'ville', 'codepostal'];
 
    foreach ($contentFacture[0] as $key0 => $value0) {
        $contentFactureFormat[$key0] = array();
     
        if(!in_array($key0, $exceptions)) {
            foreach ($contentFacture as $key => $value) {
                array_push($contentFactureFormat[$key0], $value[$key0]);
            }
        } else {
            $contentFactureFormat[$key0] = $value0;
        }
    }
 
    $id_offres = array_unique($contentFactureFormat['idoffre']);

    $date = new DateTime( $dateFacture['annee']. "-" . $dateFacture['mois'] . "-01");
 
    //echo '<pre>' .  .'</pre>';
}

?>

<?php
if (in_array($_SESSION["idCompte"], $idproprive)) {
    //print_r($contentFacture);
    //print_r($dateFacture);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
    <link rel="stylesheet" href="../style/pages/contentFacture.css">
</head>

<!-- Style ici pour génération pdf  -->
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
    max-width: 100px;
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
                    <th>Nombre de jours en ligne</th>
                    <th>Prix abonnement HT / par jour </th>
                    <th>Durée option</th>
                    <th>Prix option HT / par semaine</th>
                    <th>Total HT</th>
                    <th>Total TTC</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $prix_HT = 0;
                    $prix_TTC = 0;

                    $nb_jour_mois = $date->format('t');
                    foreach($id_offres as $key => $id_offre_facturee){
                        $keys_offre = array_keys($contentFactureFormat['idoffre'], $id_offre_facturee);
                        

                        $tailleCell = count($keys_offre);
                        $titre_offre = $contentFacture[$key]['titreoffre'];
                        $prix_abonnement = $contentFacture[$key]['prix_abo'];
                        $nbJourEnLigne = $contentFacture[$key]['nbjourenligne'];

                        $ttl_prix_option = array_sum(array_map(function ($key_offre) use ($contentFactureFormat) {
                            return (intval($contentFactureFormat['duree_option'][$key_offre] - $contentFactureFormat['duree_option_debut_annulation'][$key_offre])/7)*$contentFactureFormat['prix_option'][$key_offre];
                        },$keys_offre));

                        $ttl_prix_abo = $nbJourEnLigne*$prix_abonnement;

                        $ttl_HT = $ttl_prix_option + $ttl_prix_abo;

                        $ttl_TTC = $ttl_HT * 1.2;

                        $prix_HT += $ttl_HT;
                        $prix_TTC += $ttl_TTC;
?>
                <tr>
                    <td rowspan="<?php echo $tailleCell;?>"><?php echo $titre_offre;?></td>
                    <td rowspan="<?php echo $tailleCell;?>"><?php echo $nbJourEnLigne; ?> jour(s)</td>
                    <td rowspan="<?php echo $tailleCell;?>"><?php echo $prix_abonnement; ?> €</td>
                    <td><?php echo intval(($contentFacture[$key]['duree_option'] - $contentFacture[$key]['duree_option_debut_annulation'])/7); ?> semaine</td>
                    <td><?php echo is_null($contentFacture[$key]['prix_option']) ? "/" : $contentFacture[$key]['prix_option']; ?> €</td> <!-- gestion du cas où il n'y a pas d'option -->
                    <td rowspan="<?php echo $tailleCell;?>"><?php echo round($ttl_HT,2);?> €</td>
                    <td rowspan="<?php echo $tailleCell;?>"><?php echo round($ttl_TTC,2);?> €</td>
                </tr>
                <?php 
                foreach ($contentFacture as $key_for_option => $offre_for_options) {
                    if ($key_for_option != $key  && $offre_for_options['idoffre'] == $id_offre_facturee) {
                    ?>
                <tr>
                    <td><?php echo intval(($offre_for_options['duree_option'] - $contentFacture[$key]['duree_option_debut_annulation'])/7);?> semaine</td>
                    <td><?php echo $offre_for_options['prix_option'];?> €</td>
                </tr>
                <?php
                }
                }
                    }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="espaceVide"></td>
                    <td><?php echo round($prix_HT,2); ?> €</td>
                    <td><?php echo round($prix_TTC,2); ?> €</td>
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