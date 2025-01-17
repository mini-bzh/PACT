<?php
session_start(); // Démarre la session

// Connexion à la base de données
include('../composants/bdd/connection_params.php');
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../composants/verif/verif_compte_pro.php');

    // cree $compteMembre qui est true quand on est sur un compte pro et false sinon
    include('../composants/verif/verif_compte_membre.php');



if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}
// On va récupérer ici l'identifiant id_c présent dans les vues pro.
if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll()[0];
    //$idproprive = $dbh->query("select id_c from tripskell.pro_prive;")->fetchAll()[0];
    if(!isset($idproprive)){
    // reccuperation de id_c de pro_public
    $idpropublic = $dbh->query("select id_c from tripskell.pro_public where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll()[0];
    }
}


/*-------------------------------------------------------------------------------------------------------------------------
 *                          GESTION DES MODIFICATIONS DES INFORMATIONS BANCAIRES DU COMPTE PRO PRIVÉ                      *                    
 *------------------------------------------------------------------------------------------------------------------------*/


if (isset($idproprive)) {
    
    $idCompte = $_SESSION['idCompte'];
      
        $stmt2 = $dbh->prepare("SELECT * from tripskell.pro_prive where id_c = :id");

        $stmt2->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt2->execute();
        $result = $stmt2->fetchAll();

    $infos = $result[0];

if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.
   

// Vérifier si le login existe déjà
$checkQuery = "SELECT COUNT(*) AS count FROM tripskell.membre WHERE login = :Login";
$checkStmt = $dbh->prepare($checkQuery);
$checkStmt->bindParam(":Login", $_POST["Login"]);
$checkStmt->execute();
$result = $checkStmt->fetch();

if ($result['count'] > 0) {
    // Si le login existe déjà, définir un message d'erreur
    $error_message = "Le login est déjà utilisé. Veuillez en choisir un autre.";
} else {


        // Construction de la requête SQL
        $requete = "UPDATE tripskell.pro_prive SET
        coordonnee_bancaire = :NumeroCB,
        date_exp = :DateCB,
        cryptogramme = :CryptoCB,
        nom_titulaire_carte = :TitulaireCB";


        $requete .= " WHERE id_c = :idCompte;";

        // Prépare et exécute la requête
        $stmt = $dbh->prepare($requete);
        $stmt->bindParam(":NumeroCB", $_POST["NumeroCB"]);
        $stmt->bindParam(":DateCB", $_POST["DateCB"]);
        $stmt->bindParam(":CryptoCB", $_POST["CryptoCB"]);
        $stmt->bindParam(":TitulaireCB", $_POST["TitulaireCB"]);
        $stmt->bindParam(":idCompte", $idCompte, PDO::PARAM_INT);
        $stmt->execute();


        // on ferme la base de donnée
        $dbh = null;
   
header("Location: ../pages/accueil.php"); // on redirige vers la page de l'offre créée
}
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/pages/Formulaire.css">
    <link rel="stylesheet" href="../style/style.css">

</head>

<?php include "../composants/header/header.php";        //import navbar
        ?>

<body class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>
<main>


    <!-- Formulaire de création d'offre -->

    <form id="form" name="creation" action="" method="post" enctype="multipart/form-data">

        <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <div class="coBancaires">
                    <h3>Coordonnées bancaires :</h3>
                    <p>Vous devrez compléter ces champs si vous souhaitez publier une offre à l’avenir. </p>
                </div>
            </div>
        </div>

        <div class="champs">
            <label for="NumeroCB">Numéro de carte :  <span class="required"></span> </label>
            <input type="text" id="NumeroCB" name="NumeroCB" placeholder="Numero de votre carte" value="<?php echo $infos['coordonnee_bancaire'];?>" minlength="16" maxlength="16" pattern="^^\d{16}$"> 
        </div>
    
        <div class="InfoCB">
            <div class="champs">
                <label for="DateCB">Date d'expiration :  <span class="required"></span> </label>
                <input type="text" id="DateCB" name="DateCB" placeholder="MM/AA" value="<?php echo $infos['date_exp'];?>" minlength="5" maxlength="5" pattern="^(0[1-9]|1[0-2])\/\d{2}$"> 
            </div>

            <div class="champs">
                <label for="CryptoCB">Cryptogramme :  <span class="required"></span> </label>
                <input type="text" id="CryptoCB" name="CryptoCB" placeholder="123" value="<?php echo $infos['cryptogramme'];?>" minlength="3" maxlength="" pattern="^^\d{3}$"> 
            </div>
        </div>

            <div class="champs">
            <label for="TitulaireCB">Titulaire de la carte <span class="required"></span> :</label>
            <input type="text" id="TitulaireCB" value="<?php echo $infos['nom_titulaire_carte'];?>" name="TitulaireCB" >

           
        </div>

        <hr>

        <button type="submit" href="compte.php" class="btnConfirmer">
            <p class="texteLarge boldArchivo">Valider</p>
        </button>

    </form>
        </main>
<?php
    include "../composants/footer/footer.php";
?>

    

</body>
</html>

<?php
}
?>


