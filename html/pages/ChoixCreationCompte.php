<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Creation Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/pages/ChoixCreationCompte.css">
</head>

<?php include "../composants/header/header.php";        //import navbar
        ?>

<body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($comptePro)
            {
                echo "fondPro";
            }
            else
            {
                echo "fondVisiteur";
            }
        ?>>


<div class="pageChoixCo">
        <div class="textBulle decaleBulleGauche">
            <p>Choisir entre un compte:</p>
        </div>

    <div class="ChoixCrea">
<?php
            include '../composants/btnConnexion/btnCreaCompteMembre.php';
            ?>
            <div class= "TxtEntreBtn">
            <p> OU </p>
            </div>
            <?php  
            include '../composants/btnConnexion/btnCreaComptePro.php';
?>
    </div>

<div class="zoneBtn">
        <a href="compte.php" class="btnAnnuler">
            <p class="texteLarge boldArchivo">Annuler</p>
<?php
            include '../icones/croixSVG.svg';
?>
</a>    
</div>

</body>


<?php
    include "../composants/footer/footer.php";
?>
</html>