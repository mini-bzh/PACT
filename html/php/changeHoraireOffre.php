<?php
include("connection_params.php");

$dbh = new PDO("$driver:host=$server;dbname=$dbname", $user, $pass);

// requete pour avoir l'id de l'offre qui vient d'être creé
$stmt = $dbh->prepare("select max(idOffre) from tripskell.offre_pro");
$stmt->execute();
$idOffre = $stmt->fetchAll()[0]["max"];

echo("idOffre : " . $idOffre);

$jours = ["Lundi" => $_POST['lundi'],
        "Mardi"=> $_POST["mardi"],
        "Mercredi"=> $_POST["mercredi"],
        "Jeudi"=> $_POST["jeudi"],
        "Vendredi"=> $_POST["vendredi"],
        "Samedi"=> $_POST["samedi"],
        "Dimanche"=> $_POST["dimanche"]];


foreach ($jours as $jour => $horaires)
{
    // Remplacez les valeurs vides par NULL ou une valeur par défaut
    $debMatin = !empty($horaires[0]) ? $horaires[0] : null;
    $finMatin = !empty($horaires[1]) ? $horaires[1] : null;
    $debAprem = !empty($horaires[2]) ? $horaires[2] : null;
    $finAprem = !empty($horaires[3]) ? $horaires[3] : null;

    if($debMatin != null && $finMatin != null)
    {
        $query = "SELECT tripskell.add_horaire(:idOffre, :debMatin, :finMatin, :debAprem, :finAprem, :jour);";
        $stmt = $dbh->prepare($query);
    
        // Lier les variables aux paramètres
        $stmt->bindValue(':idOffre', $idOffre, PDO::PARAM_INT);
        $stmt->bindValue(':debMatin', $debMatin, $debMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':finMatin', $finMatin, $finMatin !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':debAprem', $debAprem, $debAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':finAprem', $finAprem, $finAprem !== null ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(':jour', $jour, PDO::PARAM_STR);
        $stmt->execute();

        echo("insertion pour " . $jour);
    }
    else
    {
        echo("pas d'insertion pour " . $jour);
    }
}
