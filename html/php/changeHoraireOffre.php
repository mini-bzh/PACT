<?php
include("connection_params.php");

$idOffre = $_POST["idOffre"];

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);


$jours = ["lundi" => $_POST["lundi"], 
        "mardi"=> $_POST["mardi"], 
        "mercredi"=> $_POST["mercredi"],
        "jeudi"=> $_POST["jeudi"], 
        "vendredi"=> $_POST["vendredi"], 
        "samedi"=> $_POST["samedi"], 
        "dimanche"=> $_POST["dimanche"]];

foreach ($jours as $jour => $horaires) 
{
    for ($i=0; $i < 4; $i++) 
    { 
        if($horaires[$i] == "")
        {
            $horaires[$i] == null;
        }
    }

    $stmt = $dbh->prepare("SELECT add_horaire(".
        $idOffre .",".
        $horaires[0].",".
        $horaires[1].",".
        $horaires[2].",".
        $horaires[3].",".
        $jour.");"
    );

    $stmt->execute();
    $nbOffres = $stmt->fetch()["count"]; 
}
