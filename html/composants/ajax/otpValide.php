<?php
    // recuperation des parametre de connection a la BdD
    include('../bdd/connection_params.php');

    use OTPHP\TOTP;

    require_once '../../vendor/autoload.php';

    // connexion a la BdD
    $dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
    $dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC); // force l'utilisation unique d'un tableau associat

    $query = "SELECT secretotp FROM tripskell._compte WHERE login = :login";
    $stmt = $dbh->prepare($query);

    $stmt = $dbh->prepare($query);
    $stmt->bindParam(":login", $_POST["login"]);

    $stmt->execute();

    $secret = $stmt->fetch()["secretotp"];

    $totp = TOTP::create($secret);

    if($totp->verify($_POST["otp"])) {
        echo 1;
    }
    else{
        echo 0;
    }