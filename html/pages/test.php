<?php
// recuperation des parametre de connection a la BdD
include('/var/www/html/php/connection_params.php');
    
// connexion a la BdD
$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
$stmt = $dbh->prepare("select * from tripskell.pro");
$stmt->execute();
$result = $stmt->fetchAll();
print_r($result);