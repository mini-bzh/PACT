<?php

require_once '../../vendor/autoload.php';

use OTPHP\TOTP;

session_start();

// recuperation des parametre de connection a la BdD
include('../bdd/connection_params.php');

// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

// On créer le secret puis le qrcode
$totp = TOTP::generate();
$totp->setLabel('PACT');
$totp->setIssuer('tripskell');
$totp->setPeriod(30);
$secret = $totp->getSecret();

$qrcodeUrl = $totp->getProvisioningUri();

header('Content-Type: application/json');
echo json_encode([  // Envoie du qrcode et du secret
    'qr_url' => $qrcodeUrl,
    'secret' => $secret
]);

?>