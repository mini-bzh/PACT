<?php
session_start();

// Récupération des paramètres de connexion à la base de données
include('../bdd/connection_params.php');

header('Content-Type: application/json');

$id_c = $_SESSION['idCompte'];

// Connexion à la base de données
$pdo = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

// Vérification si une valeur a été envoyée
if (isset($_POST['password'])) {
    $password = $_POST['password'];

    // Récupérer le mot de passe stocké en base (hashé de préférence)
    $stmt = $pdo->prepare("SELECT mot_de_passe FROM tripskell.membre WHERE id_c = $id_c");
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user && ($password == $user['mot_de_passe'])) {
        echo json_encode(["success" => true]);
    } else {
        echo json_encode(["success" => false]);
    }
}
?>
