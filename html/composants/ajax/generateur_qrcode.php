<?php

require_once '../../vendor/autoload.php';

use OTPHP\TOTP;

session_start();

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