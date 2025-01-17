<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../composants/bdd/connection_params.php');

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


$requete = "INSERT INTO tripskell.membre(";
$requete .= "login, ";
$requete .= "adresse_mail, ";
$requete .= "mot_de_passe, ";
$requete .= "pdp, ";
$requete .= "numero_tel, ";
$requete .= "nom,";
$requete .= "codepostal, ";
$requete.= "prenom) ";

$requete .= "VALUES (";
$requete .= ":Login,";
$requete .= ":Adresse_Mail,";
$requete .= ":Mot_de_P,";
$requete .= ":fichier1,";
$requete .= ":Telephone,";
$requete .= ":Nom, ";
$requete .= ":codePostal,";
$requete .= ":Prenom); ";

$stmt = $dbh->prepare($requete);
$stmt->bindParam(":Login", $_POST["Login"]);
$stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
$stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
$stmt->bindParam(":fichier1", $nom_img['fichier1']);
$stmt->bindParam(":Telephone", $_POST["Telephone"]);
$stmt->bindParam(":Nom", $_POST["Nom"]);
 $stmt->bindParam(":codePostal", $_POST["codePostal"]);  // on ajoute le code postal à la requete
$stmt->bindParam(":Prenom", $_POST["Prenom"]);

$stmt->execute(); // execution de la requete

// on ferme la base de donnée
$dbh = null;

header("Location: ../pages/connexion.php?user-tempo=membre"); // on redirige vers la page de l'offre créée
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

    <div class = FirstSentence>
    <p>Les champs qui possède une </p> 
    <div class="Asterisque"> * </div> 
    <p>sont obligatoires.</p>
    </div>

    <!-- Formulaire de création d'offre -->

    <form id="form" name="creation" action="" method="post" enctype="multipart/form-data">
        <p class="titreFrom">Création d'un compte membre</p>
        <div class="LogetPdP">
            <!-- Login -->
            <div class="champs">
                <div class="champlogin">
                    <label for="Login">Login <span class="required">*</span> :</label>
                    <input type="text" id="Login" name="Login" placeholder="Entrez un pseudonyme" required>
                    <?php
                    if (isset($error_message)) {
                        echo '<div class="error">'. $error_message. '</div>';
                    }
                    ?>
                </div>
            </div>

           <!-- Champs pour sélectionner les images -->
            <div class="champs">
                <div class ="PhotoDeProfil">
                    <img id="previewImage" src="../images/logo/ajoutimage.png" 
                        alt="Cliquez pour ajouter une image" 
                        style="cursor: pointer; width: 200px; height: auto;" 
                        onclick="document.getElementById('fichier1').click()">
                    <input type="file" id="fichier1" name="fichier1" 
                        accept="image/png, image/jpeg" 
                        style="display: none;" 
                        onchange="updatePreview()">
                </div>    
            </div>


        </div> 

        <div class="InfoPerso">
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
        </div>

        <!-- Adresse Mail -->
        <div class="champs">
            <label for="Adresse_Mail">E-mail <span class="required">*</span> :</label>
            <input type="email" id="Adresse_Mail" name="Adresse Mail" placeholder="jean.claude05@gmail.com" pattern='(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' required>
        </div>

        <div class="InfoPerso">
                <!-- Telephone -->
            <div class="champs">
                <label for="Telephone">Téléphone :</label>
                <input type="number" id="Telephone" name="Telephone" placeholder="0123456789" minlength="10" maxlength="10">
            </div>

            <div class="champs">
            <label for="codePostal">Code Postal  <span class="required">*</span> :</label>
            <input type="text" id="codePostal" name="codePostal" placeholder="Code Postal" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" required> 
            </div>
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
    
        <hr>

        <button type="submit" href="compte.php" class="btnConfirmer">
            <p class="texteLarge boldArchivo">Valider</p>
        </button>

        </div>

    </form>
        </main>
<?php
    include "../composants/footer/footer.php";
?>





<script>

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
</script>

<script>
    function updatePreview() {
        const input = document.getElementById('fichier1');
        const fileName = document.getElementById('fileName');
        const previewImage = document.getElementById('previewImage');

        // Vérifie si un fichier a été sélectionné
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            
            // Quand le fichier est chargé, met à jour l'image
            reader.onload = function (e) {
                previewImage.src = e.target.result; // Change la source de l'image
            }
            
            reader.readAsDataURL(input.files[0]); // Lit le fichier comme URL de données
            
            // Met à jour le nom du fichier
            fileName.textContent = input.files[0].name;
        } else {
            fileName.textContent = ''; // Efface le nom si aucun fichier sélectionné
        }
    }
</script>


</body>
</html>




