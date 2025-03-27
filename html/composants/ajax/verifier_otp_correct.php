<?php
require_once '../../vendor/autoload.php';
use OTPHP\TOTP;

session_start();
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

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

if ($secret === "") {
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

if ($totp->verify($otpSaisi)) {
    if ($_POST['secret'] !== "") {
        include('../bdd/connection_params.php');
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        $stmt = $dbh->prepare("UPDATE tripskell._compte SET secretotp = :secret WHERE id_c = :id");
        $stmt->bindParam(':secret', $secret, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'OTP incorrect']);
}
?>
