<?php
    // recuperation des parametre de connection a la BdD
    include('../bdd/connection_params.php');

    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat
    
    $query = "SELECT secretotp FROM tripskell._compte WHERE login = :login";
    $stmt = $dbh->prepare($query);
    $stmt->bindParam(":login", $_POST["login"]);
    $stmt->execute();

    // Clé secrète (doit être de 32 octets pour AES-256)
    $cle_secrete = file_get_contents("../../.key");

    // Décodage de la chaîne base64
    $chiffre_iv_decoded = base64_decode($stmt->fetch()['secretotp']);

    // Séparer l'IV et le texte chiffré
    $iv_recupere = substr($chiffre_iv_decoded, 0, openssl_cipher_iv_length('aes-256-cbc'));
    $chiffre_recupere = substr($chiffre_iv_decoded, openssl_cipher_iv_length('aes-256-cbc'));

    // Déchiffrement
    $secret = openssl_decrypt($chiffre_recupere, 'aes-256-cbc', $cle_secrete, 0, $iv_recupere);


    if ($secret == null)
    {
        echo 0;
    }
    else
    {
        echo 1;
    }