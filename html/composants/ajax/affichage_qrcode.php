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

// Clé secrète (doit être de 32 octets pour AES-256)
$cle_secrete = file_get_contents("../../.key");

// Décodage de la chaîne base64
$chiffre_iv_decoded = base64_decode($result['secretotp']);

// Séparer l'IV et le texte chiffré
$iv_recupere = substr($chiffre_iv_decoded, 0, openssl_cipher_iv_length('aes-256-cbc'));
$chiffre_recupere = substr($chiffre_iv_decoded, openssl_cipher_iv_length('aes-256-cbc'));

// Déchiffrement
$secret = openssl_decrypt($chiffre_recupere, 'aes-256-cbc', $cle_secrete, 0, $iv_recupere);


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