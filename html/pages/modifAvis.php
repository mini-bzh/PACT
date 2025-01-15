<?php
session_start(); // Démarre la session pour récupérer les données de session

// Récupération des paramètres de connexion à la base de données
include('../php/connection_params.php');
include_once("../php/affichageAvis.php");
// Connexion à la base de données
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // Force l'utilisation d'un tableau associatif
if (isset($_GET['id_avis'])) {

    // Récupérer et sécuriser l'id_avis
    $idAvis = htmlspecialchars($_GET['id_avis']);
    $avis = $dbh->query("select * from tripskell._avis where id_avis='" . $idAvis . "';")->fetchAll()[0];

}

if (!empty($_POST)) { // On vérifie si le formulaire est compléter ou non.

// Sécurisation des entrées pour éviter des injections SQL
$titre = htmlspecialchars($_POST['titre']);
$note = htmlspecialchars($_POST['note']);
$commentaire = htmlspecialchars($_POST['commentaire']);
$contexte = htmlspecialchars($_POST['contexte']);
$dateExperience = htmlspecialchars($_POST['dateExperience']);

// Si une image est envoyée, gérer l'upload du fichier
$nom_img = null; // Initialisation
if (isset($_FILES['fichier1']) && $_FILES['fichier1']['size'] != 0) {
    $nom_img = time() . '.' . pathinfo($_FILES['fichier1']['name'], PATHINFO_EXTENSION);
    move_uploaded_file($_FILES['fichier1']['tmp_name'], "../images/imagesAvis/" . $nom_img);
}

// Requête SQL pour mettre à jour l'avis dans la base de données
$stmt = $dbh->prepare("
    UPDATE tripskell._avis 
    SET titreavis = :titre, 
        note = :note, 
        commentaire = :commentaire, 
        cadreexperience = :contexte, 
        dateexperience = :dateExperience, 
        imageavis = :imageAvis
    WHERE id_avis = :idAvis
");

// Exécution de la requête avec les valeurs préparées
$stmt->execute([
    ':titre' => $titre,
    ':note' => $note,
    ':commentaire' => $commentaire,
    ':contexte' => $contexte,
    ':dateExperience' => $dateExperience,
    ':imageAvis' => $nom_img, // Nom de l'image (peut être null)
    ':idAvis' => $idAvis
]);



header("Location: /pages/accueil.php"); // on redirige vers la page de l'offre créée
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un avis</title>

    <!-- Favicon -->
    <link rel="icon" href="/icones/favicon.svg" type="image/svg+xml">

    <link rel="stylesheet" href="../style/style.css">
    <link rel="stylesheet" href="../style/pages/CreaCompteMembre.css">

    <link rel="stylesheet" href="../style/pages/creaAvis.css">
</head>
<?php include "../composants/header/header.php";        //import navbar
        ?>
<body class="fondVisiteur">

    <form name="creation" action="" method="post">
        <div id="conteneurTitreForm">
            <h3>Modifier un avis</h3>
            <div>
                <p>Les champs qui possède une <span class="Asterisque"> * </span> sont obligatoires.</p> 
            </div>
        </div>

        <div id="conteneurTitreNote">
            <div class="champs">
                <label for="titre">Titre<span class="required">*</span> :</label>
                <input type="text" id="titre" name="titre" placeholder="Entrez le titre de votre avis" value="<?php echo $avis['titreavis'] ?>" maxlength="20" required>
            </div>
            <div class="champs">
                <label for="note">Note <span class="required">*</span> :</label>
                <div id="conteneurNote">
                    <input type="number" id="note" name="note" placeholder="Note" value="<?php echo $avis['note'] ?>" min="1" max="5" step="0.5" required>
                    <p>/5</p>
                    <img src="../icones/etoilePleineSVG.svg" alt="étoile">
                </div>
            </div>
        </div>
        

        <div class="champs">
            <label for="commentaire">Commentaire <span class="required">*</span> :</label>
            <textarea type="text" maxlength="200" id="commentaire" name="commentaire" required><?php echo $avis['commentaire']; ?></textarea>
        </div>
        <div id="conteneurContexteDate">
    <div class="champs">
        <label for="contexte">Contexte de la visite <span class="required">*</span> :</label>
        <input type="hidden" name="contexte" id="inputContexte" value="<?php echo $avis['cadreexperience']; ?>">
        <div id="menuContexte">
            <div class="conteneurSVGtexte">
                <img src="../icones/chevronUpSVG.svg" alt="chevron haut">
                <p id="selectedContexte"><?php echo $avis['cadreexperience']; ?></p>
            </div>
            <div id="conteneurOptionsContexte">
                <p onclick="selectContexte('en solo')">en solo</p>
                <p onclick="selectContexte('en famille')">en famille</p>
                <p onclick="selectContexte('entre amis')">entre amis</p>
                <p onclick="selectContexte('affaires')">affaires</p>
            </div>
        </div>
    </div>
            
            <div class="champs">
                <label for="dateExperience">Date de la visite<span class="required">*</span> :</label>
                <input type="date" name="dateExperience" id="dateExperience" value="<?php echo $avis['dateexperience']; ?>" required>
            </div>
        </div>
        <!-- Champs pour sélectionner les images -->
        <div class="champs">
                <div class ="PhotoAvis">
                    <img id="previewImage" src="../images/imagesAvis/<?php echo $contentOffre["imageAvis"]?>" 
                        alt="Cliquez pour ajouter une image" 
                        style="cursor: pointer; width: 200px; height: auto;" 
                        onclick="document.getElementById('fichier1').click()">
                    <input type="file" id="fichier1" name="fichier1" 
                        accept="image/png, image/jpeg" 
                        style="display: none;" 
                        onchange="updatePreview()">
                </div>    
            </div>

        <div id="conteneurConfirmation">
            <input type="checkbox" name="certifAvis" required>
            <label for="certifAvis">En publiant cet avis, je certifie qu'il reflète ma propre opinion et mon expérience, que je n'ai aucun lien
                (professionnel ou personnel) avec le professionnel de tourisme de cette offre, et que je n'ai reçu aucune compensation financière
                ou autre de sa part pour rédiger cet avis.
            </label>
        </div>
        <div class="zoneBtn">

            <button type="submit" href="#" class="btnConfirmer">
                    <p class="texteLarge boldArchivo">Confirmer</p>
            <?php
                    include '../icones/okSVG.svg';
            ?>
            </button>
        </div>
    </form>

</body>
<script src="../js/creaAvis.js">
    function selectContexte(contexte) {
    // Met à jour la valeur dans l'input caché
    document.getElementById('inputContexte').value = contexte;
    
    // Met à jour le texte visible
    document.getElementById('selectedContexte').innerText = contexte;
}
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
</html>