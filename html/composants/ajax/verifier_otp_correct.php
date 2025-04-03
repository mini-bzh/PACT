<?php
require_once '../../vendor/autoload.php';
use OTPHP\TOTP;

session_start();
header('Content-Type: application/json');

if (!isset($_POST['otp']) || empty($_POST['otp'])) {
    echo json_encode(['success' => false, 'error' => 'OTP non fourni']);
    exit;
}

if (!isset($_POST['secret'])) {
    echo json_encode(['success' => false, 'error' => 'Secret non fourni']);
    exit;
}

$otpSaisi = $_POST['otp'];
$secret = $_POST['secret'];

// Clé secrète (doit être de 32 octets pour AES-256)
$cle_secrete = file_get_contents("../../.key");


if ($secret === "") {   // Si le secret n'est pas dans la variable, l'utilisateur à déjà activé Authentikator, donc on va chercher le secret en BDD
    include('../bdd/connection_params.php');
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    $stmt = $dbh->prepare("SELECT secretotp FROM tripskell._compte WHERE id_c = :id");
    $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$result || empty($result['secretotp'])) {
        echo json_encode(['success' => false, 'error' => 'Aucune clé OTP trouvée en base']);
        exit;
    }

    // Décodage de la chaîne base64
    $chiffre_iv_decoded = base64_decode($result['secretotp']);

    // Séparer l'IV et le texte chiffré
    $iv_recupere = substr($chiffre_iv_decoded, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $chiffre_recupere = substr($chiffre_iv_decoded, openssl_cipher_iv_length('aes-256-cbc'));

    // Déchiffrement
    $secret = openssl_decrypt($chiffre_recupere, 'aes-256-cbc', $cle_secrete, 0, $iv_recupere);

}

$totp = TOTP::create($secret);

if ($totp->verify($otpSaisi)) {     // On vérifie si le code OTP est correct
    if ($_POST['secret'] !== "") {  // Si il est correct, on l'enregistre si il ne l'était pas
        include('../bdd/connection_params.php');
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Votre clé à chiffrer
        $a_chiffrer = $secret;

        // Initialisation du vecteur de départ (IV) pour le mode CBC
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));

        // Chiffrement
        $chiffre = openssl_encrypt($a_chiffrer, 'aes-256-cbc', $cle_secrete, 0, $iv);

        // On doit aussi enregistrer l'IV pour le déchiffrement ultérieur
        $chiffre_iv = base64_encode($iv . $chiffre);


        $stmt = $dbh->prepare("UPDATE tripskell._compte SET secretotp = :secret WHERE id_c = :id");
        $stmt->bindParam(':secret', $chiffre_iv, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);  // On retourne que le code est correct...
} else {
    echo json_encode(['success' => false, 'error' => 'OTP incorrect']); // ...Ou incorrect
}
?>
