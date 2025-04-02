<?php

require_once '../../vendor/autoload.php';

use OTPHP\TOTP;

session_start();

// recuperation des parametre de connection a la BdD
include('../bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

$stmt = $dbh->prepare("SELECT secretotp FROM tripskell._compte WHERE id_c = :id");
$stmt->bindParam(':id', $_SESSION["idCompte"], PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$secret = $result['secretotp'];

$totp = TOTP::create($secret); // On créer l'OTP à partir du secret déjà existant
$totp->setLabel('PACT');
$totp->setIssuer('tripskell');
$totp->setPeriod(30);   // période de 30s

$qrcodeUrl = $totp->getProvisioningUri();

header('Content-Type: application/json');
echo json_encode([  // Envoie du qrcode
    'qr_url' => $qrcodeUrl,
    'secret' => $secret
]);

?>