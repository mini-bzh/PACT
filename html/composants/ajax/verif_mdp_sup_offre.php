<?php
session_start();

// Récupération des paramètres de connexion à la base de données
include('../bdd/connection_params.php');

header('Content-Type: application/json');

$id_c = $_SESSION['idCompte'];  // L'ID de l'utilisateur connecté
$password = trim($_POST['password']); // Le mot de passe envoyé depuis le formulaire, nettoyé des espaces

// Connexion à la base de données
$pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Vérification dans la table 'pro_prive'
$stmt = $pdo->prepare("SELECT mot_de_passe FROM tripskell.pro_prive WHERE id_c = :id_c");
$stmt->bindParam(':id_c', $id_c, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch();

// Si aucun utilisateur n'est trouvé dans 'pro_prive', vérifier dans 'pro_public'
if (!$user) {
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM tripskell.pro_public WHERE id_c = :id_c");
    $stmt->bindParam(':id_c', $id_c, PDO::PARAM_INT);
    $stmt->execute();
    $user = $stmt->fetch();
}

// Affichage des mots de passe pour débogage (en ajoutant des guillemets pour voir les espaces éventuels)
error_log("Mot de passe envoyé : '" . $password . "'"); 
if ($user) {
    error_log("Mot de passe stocké dans la base de données : '" . $user['mot_de_passe'] . "'");
} else {
    error_log("Aucun utilisateur trouvé");
}

// Vérification du mot de passe
if ($user && password_verify($password, $user['mot_de_passe'])) {
    echo json_encode(["success" => true]);
} else {
    // Si la vérification échoue, vérifier si le mot de passe est en texte clair
    if ($user && $password === $user['mot_de_passe']) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
