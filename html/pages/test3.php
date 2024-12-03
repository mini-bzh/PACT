<?php
// recuperation des parametre de connection a la BdD
include('../php/connection_params.php');
    
// connexion a la BdD

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);
$dbh->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

$stmt = $dbh->prepare("select * from tripskell._possede");
$stmt->execute();
$result = $stmt->fetchAll();

foreach ($result as $row)
{
    print_r($row);
    ?><br><br><?php
}
