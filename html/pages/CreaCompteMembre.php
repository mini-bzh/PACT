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

    <link rel="stylesheet" href="../style/pages/CreaCompteMembre.css">
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
<main>
    <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <p>Création d'un compte membre :</p>
            </div>
    </div>

    <div class = FirstSentence>
    <p>Les champs qui possède une </p> 
    <div class="Asterisque"> * </div> 
    <p>sont obligatoires.</p>
    </div>

    <!-- Formulaire de création d'offre -->

    <form name="creation" action="/pages/CreaCompteMembre.php" method="post" enctype="multipart/form-data">

        <!-- Nom Utilisateur -->
        <div class="champs">
            <label for="Nom_Utilisateur">Nom d'utilisateur <span class="required">*</span> :</label>
            <input type="text" id="Nom_Utilisateur" name="Nom Utilisateur" placeholder="Entrez un nom d'utilisateur" required>
        </div>

        <!-- Champs pour sélectionner les images -->
        <div class="champs">
            <label for="fichier1">Ajouter une photo de profil :</label>
            <input type="file" id="fichier1" name="fichier1" >
        </div>


        <!-- Adresse Mail -->
        <div class="champs">
            <label for="Adresse_Mail">E-mail <span class="required">*</span> :</label>
            <input type="text" id="Adresse_Mail" name="Adresse Mail" placeholder="jean.claude05@gmail.com" required>
        </div>

            <!-- Telephone -->
        <div class="champs">
            <label for="Telephone">Téléphone :</label>
            <input type="text" id="Telephone" name="Telephone" placeholder="0123456789">
        </div>


        <!-- Mot de Passe -->
        <div class="champs">
            <label for="Mot_de_P">Mot de passe <span class="required">*</span> :</label>
            <input type="text" id="Mot_de_P" name="Mot_de_P" required>
        </div>

        <div class="RequisMDP">
            <!--faire un rectangle -->
            <div class="rectangle"></div>
            <div>
            <p>Doit contenir au moins:</p>
            <ul>
                <li>12 caractères</li>
                <li>1 chiffre</li>
                <li>1 caractère spécial</li>
            </ul>    
        </div>   
    </div>        
        <!-- Mot de Passe -->
        <div class="champs">
            <label for="Confirm_Mot_de_P">Confirmation du mot de passe <span class="required">*</span> :</label>
            <input type="text" id="Confirm_Mot_de_P" name="Confirm_Mot_de_P" required>
        </div>
    
        <hr>
    
    </form>
        </main>
<?php
    include "../composants/footer/footer.php";
?>

</body>
</html>