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

    $secret = $result['secretotp'];
}

$totp = TOTP::create($secret);

if ($totp->verify($otpSaisi)) {     // On vérifie si le code OTP est correct
    if ($_POST['secret'] !== "") {  // Si il est correct, on l'enregistre si il ne l'était pas
        include('../bdd/connection_params.php');
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $stmt = $dbh->prepare("UPDATE tripskell._compte SET secretotp = :secret WHERE id_c = :id");
        $stmt->bindParam(':secret', $secret, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);  // On retourne que le code est correct...
} else {
    echo json_encode(['success' => false, 'error' => 'OTP incorrect']); // ...Ou incorrect
}
?>
