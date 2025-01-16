<?php
session_start(); // Démarre la session

// Connexion à la base de données
include('../php/connection_params.php');
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

if (!empty($_POST)) {
    // Vérifie si le login existe déjà
    $checkQuery = "SELECT COUNT(*) AS count FROM tripskell.membre WHERE login = :Login";
    $checkStmt = $dbh->prepare($checkQuery);
    $checkStmt->bindParam(":Login", $_POST["Login"]);
    $checkStmt->execute();
    $result = $checkStmt->fetch();

    if ($result['count'] > 0 && $idCompte != $_SESSION["idCompte"]) {
        $error_message = "Le login est déjà utilisé. Veuillez en choisir un autre.";
    } else {
        $nom_img = null;

        // Traitement de l'image si elle est envoyée
        if (!empty($_FILES['fichier1']) && $_FILES['fichier1']['size'] > 0) {
            $nom_img = time() . "." . explode("/", $_FILES['fichier1']['type'])[1];
            move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/pdp/" . $nom_img);
        }

        // Requête SQL
        $requete = "UPDATE tripskell.membre SET
            login = :Login,
            adresse_mail = :Adresse_Mail,
            mot_de_passe = :Mot_de_P,
            numero_tel = :Telephone,
            nom = :Nom,
            codepostal = :codePostal,
            prenom = :Prenom";

        // Ajoute le champ `pdp` uniquement si une nouvelle image a été uploadée
        if ($nom_img) {
            $requete .= ", pdp = :pdp";
        }

        $requete .= " WHERE id_c = :idCompte;";

        $stmt = $dbh->prepare($requete);
        $stmt->bindParam(":Login", $_POST["Login"]);
        $stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
        $stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
        $stmt->bindParam(":Telephone", $_POST["Telephone"]);
        $stmt->bindParam(":Nom", $_POST["Nom"]);
        $stmt->bindParam(":codePostal", $_POST["codePostal"]);
        $stmt->bindParam(":Prenom", $_POST["Prenom"]);
        $stmt->bindParam(":idCompte", $idCompte);

        // Lier le champ `pdp` si une image a été uploadée
        if ($nom_img) {
            $stmt->bindParam(":pdp", $nom_img);
        }

        $stmt->execute();

        // Fermeture de la base de données
        $dbh = null;

        // Redirection
        header("Location: ../pages/accueil.php");
    }
}
?>
<?php

    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    $idCompte = $_SESSION['idCompte'];
      
        $stmt = $dbh->prepare("SELECT * from tripskell.membre where id_c = :id");

        $stmt->bindParam(':id', $idCompte, PDO::PARAM_STR);

        $stmt->execute();
        $result = $stmt->fetchAll();

    $infos = $result[0];
?>






<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modification Compte</title>

    <!-- Favicon -->
    <link rel="icon" href="../icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/pages/CreaCompteMembre.css">
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
    <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <p>Modification d'un compte membre :</p>
            </div>
    </div>

    <div class = FirstSentence>
        <p>Les champs qui possède une </p> 
        <div class="Asterisque"> * </div> 
        <p>sont obligatoires.</p>
    </div>

    <!-- Formulaire de création d'offre -->

    <form id="form" name="creation" action="/pages/ModifComptemembre.php" method="post" enctype="multipart/form-data">

    <div class="LogetPdP">
        <!-- Login -->
        <div class="champs">
            <label for="Login">Login <span class="required">*</span> :</label>
            <input type="text" id="Login" name="Login" value="<?php echo $infos['login'];?>" required>
            <?php
            if (isset($error_message)) {
                echo '<div class="error">'. $error_message. '</div>';
            }
            ?>
        </div>

        <!-- Champs pour sélectionner les images -->
        <div class="champs">
                <div class ="PhotoDeProfil">
                    <img id="previewImage" 
                        src="../images/pdp/<?php echo $infos['pdp'] ?>" 
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
            <input type="text" id="Nom" name="Nom" value="<?php echo $infos['nom'];?>" required>
        </div>

         <!-- prenom  -->
         <div class="champs">
            <label for="Prenom">Prenom <span class="required">*</span> :</label>
            <input type="text" id="Prenom" name="Prenom" value="<?php echo $infos['prenom'];?>" required>
        </div>
        </div>


        <!-- Adresse Mail -->
        <div class="champs">
            <label for="Adresse_Mail">E-mail <span class="required">*</span> :</label>
            <input type="email" id="Adresse_Mail" name="Adresse_Mail" value="<?php echo $infos['adresse_mail'];?>" pattern='(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))' required>
        </div>

        <div class="InfoPerso">
            <!-- Telephone -->
        <div class="champs">
            <label for="Telephone">Téléphone :</label>
            <input type="number" id="Telephone" name="Telephone" value="<?php echo $infos['numero_tel'];?>" minlength="10" maxlength="10" pattern="^0[0-9]{9}$">
        </div>

        <div class="champs">
        <label for="codePostal">Code Postal  <span class="required">*</span> :</label>
        <input type="text" id="codePostal" name="codePostal" value="<?php echo $infos['codepostal'];?>" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" required> 
        </div>
        </div>


        <!-- Mot de Passe -->
        <!-- retenir le mot de passe dans une variable php -->
        <div class="champs">
            <label for="Mot_de_P">Mot de passe <span class="required">*</span> :</label>
            <input type="password" id="Mot_de_P" name="Mot_de_P" value="<?php echo $infos['mot_de_passe'];?>" minlength="12" maxlength="50" required>
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
            <input type="password" id="Confirm_Mot_de_P" name="Confirm_Mot_de_P"  value="<?php echo $infos['mot_de_passe'];?>" minlength="12" maxlength="50" required>
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
    
        <div class="zoneBtn">
                        <a href="compte.php" class="btnAnnuler">
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

        function updatePreview() {
            const input = document.getElementById('fichier1');
            const previewImage = document.getElementById('previewImage');
            const fileName = document.getElementById('fileName');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                };
                reader.readAsDataURL(input.files[0]);
                fileName.textContent = "Image sélectionnée : " + input.files[0].name;
            } else {
                previewImage.src = "<?php echo '../images/pdp/' . $infos['pdp']; ?>";
                fileName.textContent = "Aucune image sélectionnée";
            }
        }
    </script>


</body>
</html>