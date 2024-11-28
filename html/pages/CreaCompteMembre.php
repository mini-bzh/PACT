<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif


if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.
    
// ici on exploite les fichier image afin de les envoyer dans un dossier du git dans le but de stocker les images reçus
$i = 0;
foreach ($_FILES as $key_fichier => $fichier) { // on parcour les fichiers de la super globale $_FILES

    $nom_img[$key_fichier] = null; // initialistion des noms des images a null

    if ($fichier["size"]!=0) {  // on verifie que le fichier a ete transmit

        // creation du nom de fichier en utilisant time et le type de fichier
        $nom_img[$key_fichier] = time() + $i++ . "." . explode("/", $_FILES[$key_fichier]["type"])[1];

        // deplacement du fichier depuis l'espace temporaire
        move_uploaded_file($fichier["tmp_name"], "../images/pdp/" . $nom_img[$key_fichier]);
    }
}


$requete = "INSERT INTO tripskell.membre(";
$requete .= "login, ";
$requete .= "adresse_mail, ";
$requete .= "mot_de_passe, ";
$requete .= "pdp, ";
$requete .= "numero_tel, ";
$requete .= "nom,";
$requete.= "prenom) ";

$requete .= "VALUES (";
$requete .= ":Login,";
$requete .= ":Adresse_Mail,";
$requete .= ":Mot_de_P,";
$requete .= ":fichier1,";
$requete .= ":Telephone,";
$requete .= ":Nom, ";
$requete .= ":Prenom); ";

echo $requete;

$stmt = $dbh->prepare($requete);
$stmt->bindParam(":Login", $_POST["Login"]);
$stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
$stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
$stmt->bindParam(":fichier1", $nom_img['fichier1']);
$stmt->bindParam(":Telephone", $_POST["Telephone"]);
$stmt->bindParam(":Nom", $_POST["Nom"]);
$stmt->bindParam(":Prenom", $_POST["Prenom"]);

print_r($stmt);
$stmt->execute(); // execution de la requete

// on ferme la base de donnée
$dbh = null;

header("Location: /pages/accueil.php"); // on redirige vers la page de l'offre créée
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

        <!-- Login -->
        <div class="champs">
            <label for="Login">Login <span class="required">*</span> :</label>
            <input type="text" id="Login" name="Login" placeholder="Entrez un pseudonyme" required>
        </div>

         <!-- Nom  -->
         <div class="champs">
            <label for="Nom">Nom <span class="required">*</span> :</label>
            <input type="text" id="Nom" name="Nom" placeholder="Entrez votre nom" required>
        </div>

         <!-- prenom  -->
         <div class="champs">
            <label for="prenom">Prenom <span class="required">*</span> :</label>
            <input type="text" id="Prenom" name="Prenom" placeholder="Entrez votre prenom" required>
        </div>

        <!-- Champs pour sélectionner les images -->
        <div class="champs">
            <label for="fichier1">Ajouter une photo de profil :</label>
            <input type="file" id="fichier1" name="fichier1" onchange="updateFileName()" >
            <span id="fileName" class="file-name"></span> <!-- Zone pour afficher le nom -->
        </div>


        <!-- Adresse Mail -->
        <div class="champs">
            <label for="Adresse_Mail">E-mail <span class="required">*</span> :</label>
            <input type="text" id="Adresse_Mail" name="Adresse Mail" placeholder="jean.claude05@gmail.com" pattern='(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' required>
        </div>

            <!-- Telephone -->
        <div class="champs">
            <label for="Telephone">Téléphone :</label>
            <input type="text" id="Telephone" name="Telephone" placeholder="01 23 45 67 89" minlength="10" maxlength="10">
        </div>


        <!-- Mot de Passe -->
        <!-- retenir le mot de passe dans une variable php -->
        <div class="champs">
            <label for="Mot_de_P">Mot de passe <span class="required">*</span> :</label>
            <input type="password" id="Mot_de_P" name="Mot_de_P" required>
            <?php
            // Vérification du mot de passe
            if (isset($_POST['Mot_de_P']))
            {
                $mot_de_P = $_POST['Mot_de_P'];
                $longueur = strlen($mot_de_P);
                $contient_chiffre = false;
                $contient_majuscule = false;
                $contient_minuscule = false;
                $contient_special = false;
                
                // Vérification de la longueur
                if ($longueur >= 12)
                {
                    // Vérification des caractères
                    for ($i = 0; $i < $longueur; $i++)
                    {
                        $caractere = $mot_de_P[$i];
                        
                        if (ctype_digit($caractere))
                        {
                            $contient_chiffre = true;
                        }
                        elseif (ctype_upper($caractere))
                        {
                            $contient_majuscule = true;
                        }
                        elseif (ctype_lower($caractere))
                        {
                            $contient_minuscule = true;
                        }
                        elseif (!ctype_alnum($caractere))
                        {
                            $contient_special = true;
                        }
                    }
                
                    // Affichage des messages d'erreur
                    if (!$contient_chiffre)
                    {
                        echo '<div class="erreur">Le mot de passe doit
                        contenir au moins un chiffre.</div>';
                    }
                    if (!$contient_majuscule)
                    {
                        echo '<div class="erreur">Le mot de passe doit
                        contenir au moins une majuscule.</div>';
                    }
                    if (!$contient_minuscule)
                    {
                        echo '<div class="erreur">Le mot de passe doit
                        contenir au moins une minuscule.</div>';
                    }
                    if (!$contient_special)
                    {
                        echo '<div class="erreur">Le mot de passe doit
                        contenir au moins un caractère spécial.</div>';
                    }
                    if (!$contient_chiffre &&!$contient_majuscule &&!$contient_minuscule &&!$contient_special)
                    {
                        echo '<div class="erreur">Veuillez entrer un mot de passe
                        adéquat.</div>';
                    }}}
                    ?>
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
            <input type="password" id="Confirm_Mot_de_P" name="Confirm_Mot_de_P" required>
        </div>
        <?php 
        // Vérification de la confirmation du mot de passe
        if (isset($_POST['Confirm_Mot_de_P']))
        {
            $confirm_mot_de_P = $_POST['Confirm_Mot_de_P'];
            if ($mot_de_P!= $confirm_mot_de_P)
            {
                echo '<div class="erreur">Les mots de passe ne correspondent pas.</div>';
            }
        }
        ?>
    
        <hr>
    
        <div class="zoneBtn">
                        <a href="ChoixCreationCompte.php" class="btnAnnuler">
                            <p class="texteLarge boldArchivo">Annuler</p>
                            <?php
                            include '../icones/croixSVG.svg';
                            ?>
                        </a>

                        <button type="submit" href="compte.php" class="btnConfirmer">
                            <p class="texteLarge boldArchivo">Confirmer</p>
                            <?php
                            include '../icones/okSVG.svg';
                            ?>
        </div>

    </form>
        </main>
<?php
    include "../composants/footer/footer.php";
?>

</body>
</html>


<script>
    function updateFileName() {
        const fileInput = document.getElementById('fichier1'); // Champ de fichier
        const fileName = document.getElementById('fileName'); // Zone où afficher le nom
        const label = document.getElementById('customFileLabel'); // Label du bouton

        if (fileInput.files.length > 0) {
            // Si un fichier est sélectionné, afficher son nom
            fileName.textContent = fileInput.files[0].name;
            label.textContent = "Changer la photo"; // Met à jour le texte du bouton
        } else {
            // Si aucun fichier n'est sélectionné
            fileName.textContent = "";
            label.textContent = "📷 Ajouter une photo de profil"; // Remet le texte original
        }
    }
</script>