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

    // Récupération du type de domaine
    $typeDomaine = $_POST['type_domaine']; // "privé" ou "public"

    // Assurez-vous que le type de domaine a une valeur valide
    if ($typeDomaine === 'privé') {
        $requete = "INSERT INTO tripskell.pro_prive(";
        $requete .= "login, ";
        $requete .= "adresse_mail, ";
        $requete .= "mot_de_passe, ";
        $requete .= "pdp, ";
        $requete .= "numero_tel, ";
        $requete .= "numero,";
        $requete .= "rue, ";
        $requete .= "ville, ";
        $requete.= "codepostal, ";
        $requete .= "num_siren, ";
        $requete .= "raison_social,";
        $requete .= "coordonnee_bancaire, ";
        $requete .= "date_exp,";
        $requete .= "cryptogramme, ";
        $requete .= "nom_titulaire_carte)";

        $requete .= "VALUES (";
        $requete.= ":Login,";
        $requete.= ":Adresse_Mail,";
        $requete.= ":Mot_de_P,";
        $requete.= ":pdp,";
        $requete.= ":Telephone,";
        $requete.= ":num,";
        $requete.= ":nomRue,";
        $requete.= ":ville, ";
        $requete.= ":codePostal,";
        $requete.= ":codeSiren, ";
        $requete.= ":RaisonSociale, ";
        $requete.= ":NumeroCB, ";
        $requete.= ":DateCB, ";
        $requete.= ":CryptoCB, ";
        $requete.= ":TitulaireCB)";

        if (!empty($_POST["codeSiren"]) && preg_match('/^\d{9}$/', $_POST["codeSiren"])) {
            $codeSiren = intval($_POST["codeSiren"]);
        } else {
            $codeSiren = null; // Ou déclenchez une erreur si le codeSiren est obligatoire
        }
        if (!empty($_POST["CryptoCB"]) && preg_match('/^\d{3}$/', $_POST["CryptoCB"])) {
            $CryptoCB = intval($_POST["CryptoCB"]);
        } else {
            $CryptoCB = null; // Ou déclenchez une erreur si le codeSiren est obligatoire
        }

        
        $stmt = $dbh->prepare($requete);
        $stmt->bindParam(":Login", $_POST["Login"]);
        $stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
        $stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
        $stmt->bindParam(":pdp", $nom_img['fichier1']);
        $stmt->bindParam(":Telephone", $_POST["Telephone"]);
        $stmt->bindParam(":num", $_POST["num"]);
        $stmt->bindParam(":nomRue", $_POST["nomRue"]);
        $stmt->bindParam(":ville", $_POST["ville"]);
        $stmt->bindParam(":codePostal", $_POST["codePostal"]);
        $stmt->bindParam(":codeSiren", $codeSiren, PDO::PARAM_INT); // Passe la valeur validée
        $stmt->bindParam(":RaisonSociale", $_POST["RaisonSociale"]);
        $stmt->bindParam(":NumeroCB", $_POST["NumeroCB"]);
        $stmt->bindParam(":DateCB", $_POST["DateCB"]);
        $stmt->bindParam(":CryptoCB", $CryptoCB, PDO::PARAM_INT);
        $stmt->bindParam(":TitulaireCB", $_POST["TitulaireCB"]);

        $stmt->execute(); // execution de la requete

        // on ferme la base de donnée
        $dbh = null;
    }
    elseif($typeDomaine === 'public') {
        $requete = "INSERT INTO tripskell.pro_public(";
        $requete .= "login, ";
        $requete .= "adresse_mail, ";
        $requete .= "mot_de_passe, ";
        $requete .= "pdp, ";
        $requete .= "numero_tel, ";
        $requete .= "numero,";
        $requete .= "rue, ";
        $requete .= "ville, ";
        $requete.= "codepostal, ";
        $requete .= "num_siren, ";
        $requete .= "raison_social)";

        $requete .= "VALUES (";
        $requete.= ":Login,";
        $requete.= ":Adresse_Mail,";
        $requete.= ":Mot_de_P,";
        $requete.= ":pdp,";
        $requete.= ":Telephone,";
        $requete.= ":num,";
        $requete.= ":nomRue,";
        $requete.= ":ville, ";
        $requete.= ":codePostal,";
        $requete.= ":codeSiren, ";
        $requete.= ":RaisonSociale)";


        if (!empty($_POST["codeSiren"]) && preg_match('/^\d{9}$/', $_POST["codeSiren"])) {
            $codeSiren = intval($_POST["codeSiren"]);
        } else {
            $codeSiren = null; // Ou déclenchez une erreur si le codeSiren est obligatoire
        }
        
        $stmt = $dbh->prepare($requete);
        $stmt->bindParam(":Login", $_POST["Login"]);
        $stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
        $stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
        $stmt->bindParam(":pdp", $nom_img['fichier1']);
        $stmt->bindParam(":Telephone", $_POST["Telephone"]);
        $stmt->bindParam(":num", $_POST["num"]);
        $stmt->bindParam(":nomRue", $_POST["nomRue"]);
        $stmt->bindParam(":ville", $_POST["ville"]);
        $stmt->bindParam(":codePostal", $_POST["codePostal"]);
        $stmt->bindParam(":codeSiren", $codeSiren, PDO::PARAM_INT); // Passe la valeur validée
        $stmt->bindParam(":RaisonSociale", $_POST["RaisonSociale"]);

        $stmt->execute(); // execution de la requete

        // on ferme la base de donnée
        $dbh = null;
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
                <input type="hidden" name="type_domaine" id="typeDomaine" value="">
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
            <input type="text" id="Telephone" name="Telephone" placeholder="0123456789" minlength="10" maxlength="10" pattern="^^\d{10}$">
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


         <!-- Adresse -->
         <div class="champs">
         <div class="labelAdresse">
                        <label for="adresse">Adresse :</label>
                    </div>
                <div class="champsAdresse">
                    
                    <input type="text" id="num" name="num" placeholder="Numéro" minlength="1" maxlength="3" >
                    <input type="text" id="nomRue" name="nomRue" placeholder="Nom de rue" >
                    <input type="text" id="ville" name="ville" placeholder="Ville" >
                    <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" >
                </div>
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

    
    const showCheckbox = document.getElementById('showCheckbox');
const hideCheckbox = document.getElementById('hideCheckbox');
const typeDomaineInput = document.getElementById('typeDomaine');
const extraFields = document.getElementById('extraFields');

// Fonction pour vérifier et forcer qu'une case est cochée
function checkCheckboxes() {
    if (!showCheckbox.checked && !hideCheckbox.checked) {
        // Si aucune case n'est cochée, on coche par défaut la case "Domaine Privé"
        showCheckbox.checked = true;
        typeDomaineInput.value = 'privé';
        extraFields.classList.remove('hidden');
    }
}

// Mise à jour des champs et sections en fonction des cases cochées
showCheckbox.addEventListener('change', function () {
    if (this.checked) {
        typeDomaineInput.value = 'privé';
        hideCheckbox.checked = false;
        extraFields.classList.remove('hidden');
    } else {
        typeDomaineInput.value = '';
        extraFields.classList.add('hidden');
    }
    checkCheckboxes();  // Vérifie l'état des cases
});

hideCheckbox.addEventListener('change', function () {
    if (this.checked) {
        typeDomaineInput.value = 'public';
        showCheckbox.checked = false;
        extraFields.classList.add('hidden');
    } else {
        typeDomaineInput.value = '';
    }
    checkCheckboxes();  // Vérifie l'état des cases
});

// Vérifie initialement si une case est cochée au chargement de la page
checkCheckboxes();

</script>

</body>
</html>




