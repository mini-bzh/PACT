<?php
require_once '../../vendor/autoload.php';

use OTPHP\TOTP;

session_start();
header('Content-Type: application/json');

$otpSaisi = $_POST['otp'];

if ($_POST['secret'] != "") {
    $secret = $_POST['secret'];
} else {
    include('../bdd/connection_params.php');
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Stockage du secret en base de données après validation
    $stmt = $dbh->prepare("select secretotp from tripskell._compte WHERE id_c = :id");
    $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    $secret = $result['secretotp'];
}

$totp = TOTP::create($secret);

if ($totp->verify($otpSaisi)) {
    
    if ($_POST['secret'] != "") {
        include('../bdd/connection_params.php');
        $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
        $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    
        // Stockage du secret en base de données après validation
        $stmt = $dbh->prepare("UPDATE tripskell._compte SET secretotp = :secret WHERE id_c = :id");
        $stmt->bindParam(':secret', $secret, PDO::PARAM_STR);
        $stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
        $stmt->execute();
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
