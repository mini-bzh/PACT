<?php
session_start(); // Démarre la session

// Connexion à la base de données
include('../php/connection_params.php');
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$idCompte = $_SESSION['idCompte'];

    // cree $comptePro qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_pro.php');

    // cree $compteMembre qui est true quand on est sur un compte pro et false sinon
    include('../php/verif_compte_membre.php');



if (!isset($_SESSION["idCompte"])) {
    header("Location: /pages/erreur404.php");
    exit();
}
// On va récupérer ici l'identifiant id_c présent dans les vues pro.
if (key_exists("idCompte", $_SESSION)) {
    // reccuperation de id_c de pro_prive 
    $idproprive = $dbh->query("select id_c from tripskell.pro_prive where id_c=" . $_SESSION["idCompte"] . ";")->fetchAll()[0];
    //$idproprive = $dbh->query("select id_c from tripskell.pro_prive;")->fetchAll()[0];
}


/*-------------------------------------------------------------------------------------------------------------------------
 *                                     GESTION DES MODIFICATIONS DU COMPTE PRO PRIVÉ                                      *                    
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
// ici on exploite les fichier image afin de les envoyer dans un dossier du git dans le but de stocker les images reçus
$i = 0;
// Gestion des fichiers
$nom_img = null; // Initialiser le nom de l'image à null

if ($_FILES['fichier1']['size'] != 0) { // Vérifie si un fichier a été envoyé
    $extension = pathinfo($_FILES['fichier1']['name'], PATHINFO_EXTENSION); // Récupère l'extension
    $nom_img = time() . "_" . uniqid() . "." . $extension; // Génère un nom unique
    $upload_success = move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/pdp/" . $nom_img);

    if (!$upload_success) {
        $error_message = "Erreur lors du téléchargement de l'image.";
    }
}


        // Construction de la requête SQL
        $requete = "UPDATE tripskell.pro_prive SET
        login = :Login,
        adresse_mail = :Adresse_Mail,
        mot_de_passe = :Mot_de_P,
        numero_tel = :Telephone,
        numero = :num,
        rue = :nomRue,
        ville = :ville,
        codepostal = :codePostal,
        num_siren = :codeSiren,
        raison_social = :RaisonSociale,
        coordonnee_bancaire = :NumeroCB,
        date_exp = :DateCB,
        cryptogramme = :CryptoCB,
        nom_titulaire_carte = :TitulaireCB";

        if ($nom_img !== null) { // Ajoute uniquement si une image a été uploadée
        $requete .= ", pdp = :pdp";
        }

        $requete .= " WHERE id_c = :idCompte;";

        // Prépare et exécute la requête
        $stmt = $dbh->prepare($requete);
        $stmt->bindParam(":Login", $_POST["Login"]);
        $stmt->bindParam(":Adresse_Mail", $_POST["Adresse_Mail"]);
        $stmt->bindParam(":Mot_de_P", $_POST["Mot_de_P"]);
        $stmt->bindParam(":Telephone", $_POST["Telephone"]);
        $stmt->bindParam(":num", $_POST["num"]);
        $stmt->bindParam(":nomRue", $_POST["nomRue"]);
        $stmt->bindParam(":ville", $_POST["ville"]);
        $stmt->bindParam(":codePostal", $_POST["codePostal"]);
        $stmt->bindParam(":codeSiren", $_POST["codeSiren"]);
        $stmt->bindParam(":RaisonSociale", $_POST["RaisonSociale"]);
        $stmt->bindParam(":NumeroCB", $_POST["NumeroCB"]);
        $stmt->bindParam(":DateCB", $_POST["DateCB"]);
        $stmt->bindParam(":CryptoCB", $_POST["CryptoCB"]);
        $stmt->bindParam(":TitulaireCB", $_POST["TitulaireCB"]);

        if ($nom_img !== null) { // Lie l'image seulement si elle a été téléchargée
        $stmt->bindParam(":pdp", $nom_img);
        }

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
    <title>Modification Compte</title>

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
    <div class="pageChoixCo">
            <div class="textBulle decaleBulleGauche">
                <p>Modification d'un compte professionnel :</p>
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



<!-- Nom  -->
<div class="champs">
   <label for="RaisonSociale">Raison Sociale <span class="required">*</span> :</label>
   <input type="text" id="RaisonSociale" name="RaisonSociale" value="<?php echo $infos['raison_social'];?>" required>
</div>
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
    
    <div class="champs">
            <label for="codeSiren">Code SIREN  <span class="required"></span> :</label>
            <input type="text" id="codeSiren" name="codeSiren" value="<?php echo $infos['num_siren'];?>" minlength="9" maxlength="9" pattern="^^\d{9}$"> 
        </div>


        <!-- Adresse -->
        <div class="champs">
           <div class="labelAdresse">
               <label for="adresse">Adresse :</label>
           </div>
       <div class="champsAdresse">
           
           <input type="text" id="num" name="num" value="<?php echo $infos['numero'];?>" minlength="1" maxlength="3" >
           <input type="text" id="nomRue" name="nomRue" value="<?php echo $infos['rue'];?>" >
           <input type="text" id="ville" name="ville" value="<?php echo $infos['ville'];?>" >
           <input type="text" id="codePostal" name="codePostal" value="<?php echo $infos['codepostal'];?>" minlength="5" maxlength="5" pattern="^(?:0[1-9]|[1-8]\d|9[0-8])\d{3}$" >
       </div>
       </div>


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

<?php
}
?>