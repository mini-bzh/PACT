<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');

// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif


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

   
header("Location: ../pages/connexion.php?user-tempo=pro"); // on redirige vers la page de l'offre créée
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

    <link rel="stylesheet" href="../style/pages/CreaCompteMembre.css">
    <link rel="stylesheet" href="../style/style.css">

</head>

<?php include "../composants/header/header.php";        //import navbar
        ?>

<body  class=<?php                          //met le bon fond en fonction de l'utilisateur
            if ($_GET['user-tempo'] == 'pro') {
                echo 'fondPro';
            } else {
                echo 'fondVisiteur';
            }
        ?>>
<main>
    <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <p>Création d'un compte Professionnel :</p>
            </div>
    </div>

    <div class = FirstSentence>
    <p>Les champs qui possède une </p> 
    <div class="Asterisque"> * </div> 
    <p>sont obligatoires.</p>
    </div>

    <!-- Formulaire de création d'offre -->

    <form id="form" name="creation" action="" method="post" enctype="multipart/form-data">

        <div class="choixPro">
            <div class="propriv">
                <label>
                Domaine Privé 
                <input type="checkbox" id="showCheckbox">
                </label>
            </div>

            <div class="propub">
                <label>
                Domaine Public
                <input type="checkbox" id="hideCheckbox">    
                </label>
            </div> 
        </div>

        <!-- Login -->
        <div class="champs">
            <label for="Login">Login <span class="required">*</span> :</label>
            <input type="text" id="Login" name="Login" placeholder="Entrez un pseudonyme" required>
            <?php
            if (isset($error_message)) {
                echo '<div class="error">'. $error_message. '</div>';
            }
            ?>
        </div>

         <!-- Nom  -->
         <div class="champs">
            <label for="RaisonSociale">Raison Sociale <span class="required">*</span> :</label>
            <input type="text" id="RaisonSociale" name="RaisonSociale" placeholder="Entrez votre nom" required>
        </div>

        <!-- Champs pour sélectionner les images -->
        <div class="champs">
            <label for="fichier1">Ajouter une photo de profil :</label>
            <input type="file" id="fichier1" name="fichier1" accept="image/png, image/jpeg" onchange="updateFileName()" >
            <span id="fileName" class="file-name"></span> <!-- Zone pour afficher le nom -->
        </div>


        <!-- Adresse Mail -->
        <div class="champs">
            <label for="Adresse_Mail">E-mail <span class="required">*</span> :</label>
            <input type="email" id="Adresse_Mail" name="Adresse Mail" placeholder="jean.claude05@gmail.com" pattern='(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' required>
        </div>

            <!-- Telephone -->
        <div class="champs">
            <label for="Telephone">Téléphone :</label>
            <input type="number" id="Telephone" name="Telephone" placeholder="0123456789" minlength="10" maxlength="10">
        </div>


        <!-- Mot de Passe -->
        <!-- retenir le mot de passe dans une variable php -->
        <div class="champs">
            <label for="Mot_de_P">Mot de passe <span class="required">*</span> :</label>
            <input type="password" id="Mot_de_P" name="Mot_de_P" minlength="12" maxlength="50" required>
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
            <input type="password" id="Confirm_Mot_de_P" name="Confirm_Mot_de_P" minlength="12" maxlength="50" required>
        </div>
        <?php 
        // Vérification de la confirmation du mot de passe
        if (isset($_POST['Confirm_Mot_de_P']))
        {
            $confirm_mot_de_P = $_POST['Confirm_Mot_de_P'];
            $mot_de_P = $_POST['Mot_de_P'];
            if ($mot_de_P!= $confirm_mot_de_P)
            {
                echo '<div class="erreur">Les mots de passe ne correspondent pas.</div>';
            }
        }
        ?>

        <div class="champs">
            <label for="codeSiren">Code SIREN  <span class="required"></span> :</label>
            <input type="text" id="codeSiren" name="codeSiren" placeholder="CodeSiren" minlength="9" maxlength="9" pattern="^^\d{9}$"> 
        </div>

        <div id="extraFields" class="hidden">
        <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <div class="coBancaires">
                    <h2>Coordonnées bancaires :</h2>
                    <p>Vous devrez compléter ces champs si vous souhaitez publier une offre à l’avenir. </p>
                </div>
            </div>
        </div>

        <div class="champs">
            <label for="NumeroCB">Numéro de carte :  <span class="required"></span> </label>
            <input type="text" id="NumeroCB" name="NumeroCB" placeholder="Numero de votre carte" minlength="16" maxlength="16" pattern="^^\d{16}$"> 
        </div>
    
        <div class="champs">
            <label for="DateCB">Date d'expiration :  <span class="required"></span> </label>
            <input type="text" id="DateCB" name="DateCB" placeholder="MM/AA" minlength="5" maxlength="5" pattern="^(0[1-9]|1[0-2])\/\d{2}$"> 
        </div>

        <div class="champs">
            <label for="CryptoCB">Cryptogramme :  <span class="required"></span> </label>
            <input type="text" id="CryptoCB" name="CryptoCB" placeholder="123" minlength="3" maxlength="" pattern="^^\d{3}$"> 
        </div>


            <div class="champs">
            <label for="TitulaireCB">Titulaire de la carte <span class="required"></span> :</label>
            <input type="text" id="TitulaireCB" name="TitulaireCB">

            <!-- Adresse -->
            <div class="labelAdresse">
                        <label for="adresse">Adresse :</label>
                    </div>
                <div class="champsAdresse">
                    
                    <input type="text" id="num" name="num" placeholder="Numéro" minlength="1" maxlength="3" pattern="^^^\d{3}$" >
                    <input type="text" id="nomRue" name="nomRue" placeholder="Nom de rue" >
                    <input type="text" id="ville" name="ville" placeholder="Ville" >
                    <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" >
                </div>
        </div>
    </div> 
        <hr>

        <div class="zoneBtn">
                        <a href="ChoixCreationCompte.php" class="btnAnnuler">
                            <p class="texteLarge boldArchivo">Annuler</p>
                            <svg width="60" height="60" viewBox="0 0 60 60" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <g clip-path="url(#clip0_208_4609)">
                            <path d="M0 60L60 0.000228972" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M0 0L60 59.9998" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                            </g>
                            <defs>
                            <clipPath id="clip0_208_4609">
                            <rect width="60" height="60" fill="white"/>
                            </clipPath>
                            </defs>
                            </svg>

                        </a>

                        <button type="submit" href="compte.php" class="btnConfirmer">
                            <p class="texteLarge boldArchivo">Confirmer</p>
                            <svg width="60" height="60" viewBox="0 0 75 54" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M70.6667 4L24.8333 49.8333L4 29" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                        </button>

        </div>

    </form>
        </main>
<?php
    include "../composants/footer/footer.php";
?>





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

    const form = document.getElementById('form'); // Élément du formulaire
    const password = document.getElementById('Mot_de_P'); // Champ mot de passe
    const confirmPassword = document.getElementById('Confirm_Mot_de_P'); // Champ confirmation
    const regexMdp = /^(?=.*\d)(?=.*[!@#$%^&*(),.?":{}|<>]).+$/; // Regex pour validation

    // Écoute de la soumission du formulaire
    form.addEventListener('submit', function (e) {
        // Vérifie si les champs sont remplis
        if (!password.value || !confirmPassword.value) {
            e.preventDefault(); // Empêche le formulaire de s'enregistrer
            alert('Tous les champs sont obligatoires.'); // Affiche une alerte
            return; // Sort de la fonction
        }

        // Vérifie si les mots de passe correspondent
        if (password.value !== confirmPassword.value) {
            e.preventDefault(); // Empêche le formulaire de s'enregistrer
            alert('Les mots de passe ne correspondent pas.'); // Affiche une alerte
            return; // Sort de la fonction
        }

        // Vérifie si le mot de passe respecte les critères (chiffre et caractère spécial)
        if (!regexMdp.test(password.value)) {
            e.preventDefault(); // Empêche la soumission du formulaire
            alert("Le mot de passe doit contenir au moins un chiffre et un caractère spécial.");
            return; // Sort de la fonction
        }
    });

    
    // Récupération des éléments
    const showCheckbox = document.getElementById('showCheckbox');
    const hideCheckbox = document.getElementById('hideCheckbox');
    const extraFields = document.getElementById('extraFields');

    // Gestionnaire d'événements pour les deux checkbox
    showCheckbox.addEventListener('change', function() {
      if (this.checked) {
        extraFields.classList.remove('hidden');
        hideCheckbox.checked = false; // Décoche la deuxième checkbox
      }
    });

    hideCheckbox.addEventListener('change', function() {
      if (this.checked) {
        extraFields.classList.add('hidden');
        showCheckbox.checked = false; // Décoche la première checkbox
      }
    });
</script>



</body>
</html>




