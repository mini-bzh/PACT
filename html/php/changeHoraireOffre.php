<?php
include("connection_params.php");

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

$stmt = $dbh->prepare("select * from tripskell._offre");
$stmt->execute();
$result = $stmt->fetchAll();

$idOffre = sizeof($result);

$jours = ["lundi" => $_POST['lundi'],
        "mardi"=> $_POST["mardi"],
        "mercredi"=> $_POST["mercredi"],
        "jeudi"=> $_POST["jeudi"],
        "vendredi"=> $_POST["vendredi"],
        "samedi"=> $_POST["samedi"],
        "dimanche"=> $_POST["dimanche"]];


foreach ($jours as $jour => $horaires)
{
    $query = "SELECT add_horaire(:idOffre, :debMatin, :finMatin, :debAprem, :finAprem, :jour);";
    $stmt = $dbh->prepare($query);

    // Lier les variables aux paramètres
    $stmt->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
    $stmt->bindValue(':debMatin', $horaires[0], PDO::PARAM_STR);
    $stmt->bindValue(':finMatin', $horaires[1], PDO::PARAM_STR);
    $stmt->bindValue(':debAprem', $horaires[2], PDO::PARAM_STR);
    $stmt->bindValue(':finAprem', $horaires[3], PDO::PARAM_STR);
    $stmt->bindValue(':jour', $jour, PDO::PARAM_STR);

}
